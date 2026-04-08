"""Extract text from a PDF using PyMuPDF4LLM."""

import json
import sys

import pymupdf.layout
import pymupdf4llm


def extract_text(file_path: str, lang: str = 'eng') -> str:

    return pymupdf4llm.to_markdown(
        file_path,
        header=False,
        footer=False,
        ocr_language=lang
        )


def extract_pages(file_path: str, lang: str = 'eng') -> list[dict]:

    chunks = pymupdf4llm.to_markdown(
        file_path,
        page_chunks=True,
        header=False,
        footer=False,
        ocr_language=lang
        )

    return [
        {"page": i + 1, "text": chunk["text"]}
        for i, chunk in enumerate(chunks)
    ]


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
