"""Extract text from a PDF using PyMuPDF4LLM."""

import json
import re
import sys

import pymupdf.layout
import pymupdf4llm

# ---------------------------------------------------------------------------
# Page classification thresholds — tune these to adjust sensitivity.
# The classifier is intentionally conservative: pages are only classified as
# 'title' or 'contents' when the evidence is clear; everything else is
# 'unknown' so it is still included in keyword searches.
# ---------------------------------------------------------------------------

# Title page detection
TITLE_MAX_PAGE_NUMBER = 3       # Only consider pages up to this number as title pages
TITLE_MAX_WORD_COUNT = 60       # A title page must have fewer words than this

# Table-of-contents detection
TOC_MAX_PAGE_NUMBER = 25        # Only consider pages up to this number as TOC pages

# A TOC-pattern line ends with: 3+ dots then a number, or 3+ spaces then a number.
## The number might be followed by one or more | if the contents has been rendered out as a markdown table
_TOC_LINE_PATTERN = re.compile(
    r'.{5,}(?:\.{3,}|\s{3,})\s*\d{1,4}\s*\|*\s*$',
    re.MULTILINE,
)

# Heading that explicitly names the page as a table of contents
_TOC_HEADING_PATTERN = re.compile(
    r'^\s*#*\s*(table\s+of\s+)?contents\s*$',
    re.IGNORECASE | re.MULTILINE,
)

# Without a "Contents" heading: require this many matching lines AND this ratio
TOC_MIN_MATCHING_LINES = 6      # Minimum number of TOC-pattern lines
TOC_MIN_LINE_RATIO = 0.35       # Minimum fraction of non-empty lines that match

# With a "Contents" heading present: lower bar (heading is strong evidence)
TOC_MIN_MATCHING_LINES_WITH_HEADING = 3


def classify_page(text: str, page_number: int) -> str:
    """Return 'title', 'contents', or 'unknown' for a single page.

    Page numbers are 1-indexed.  Biased toward 'unknown' — only classify
    when the evidence clearly matches a title or contents page.
    """
    # --- Table of contents (checked first — a TOC heading overrides a low word count) ---
    if page_number <= TOC_MAX_PAGE_NUMBER:
        non_empty_lines = [ln for ln in text.splitlines() if ln.strip()]
        toc_lines = _TOC_LINE_PATTERN.findall(text)
        has_heading = bool(_TOC_HEADING_PATTERN.search(text))

        if has_heading:
            if len(toc_lines) >= TOC_MIN_MATCHING_LINES_WITH_HEADING:
                return 'contents'
        else:
            if (
                len(non_empty_lines) > 0
                and len(toc_lines) >= TOC_MIN_MATCHING_LINES
                and len(toc_lines) / len(non_empty_lines) >= TOC_MIN_LINE_RATIO
            ):
                return 'contents'

    # --- Title page (checked after TOC so a low-word-count TOC isn't mis-labelled) ---
    if page_number <= TITLE_MAX_PAGE_NUMBER:
        words = text.split()
        if len(words) < TITLE_MAX_WORD_COUNT:
            return 'title'

    return 'body'


def sanitize_text(text: str) -> str:
    """Replace unprintable/PUA Unicode characters with a standard bullet point."""
    # Private Use Area: U+E000–U+F8FF (includes dingbat font bullets like U+F0B7)
    return re.sub(r'[-]', '•', text)


def extract_text(file_path: str, lang: str = 'eng') -> str:

    return sanitize_text(pymupdf4llm.to_markdown(
        file_path,
        header=False,
        footer=False,
        ocr_language=lang
        ))


def extract_pages(file_path: str, lang: str = 'eng') -> list[dict]:

    chunks = pymupdf4llm.to_markdown(
        file_path,
        page_chunks=True,
        header=False,
        footer=False,
        ocr_language=lang
        )

    pages = []
    for i, chunk in enumerate(chunks):
        page_number = i + 1
        text = sanitize_text(chunk["text"])
        pages.append({
            "page": page_number,
            "text": text,
            "page_type": classify_page(text, page_number),
        })

    return pages


if __name__ == "__main__":
    import argparse

    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("file_path")
    parser.add_argument("--pages", action="store_true",
                        help="Output per-page JSON instead of a single markdown string")

    args = parser.parse_args()

    if args.pages:
        pages = extract_pages(args.file_path)
        sys.stdout.write(json.dumps(pages, ensure_ascii=False))
    else:
        text = extract_text(args.file_path)
        sys.stdout.write(text)
