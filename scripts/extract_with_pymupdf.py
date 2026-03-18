"""Extract text from a PDF using PyMuPDF4LLM."""

import sys

import pymupdf.layout
import pymupdf4llm


def extract_text(file_path: str, lang: str = 'eng', sort: bool = False) -> str:

    return pymupdf4llm.to_markdown(
        file_path,
        header=False,
        footer=False,
        ocr_language=lang
        )


if __name__ == "__main__":
    import argparse

    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("file_path")

    args = parser.parse_args()

    text = extract_text(args.file_path)
    sys.stdout.write(text)
