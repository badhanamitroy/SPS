#!/usr/bin/env python3
"""
SPS Platform — Secure PDF Preview Page-Level Slicer
Extracts exact allowed page ranges (e.g. pages 1..20) from original PDFs
for strict server-side document delivery to unauthorized or preview readers.
"""
import sys
import os

def slice_pdf(source_path: str, output_path: str, start_page: int, end_page: int) -> bool:
    try:
        import pypdfium2 as pdfium
    except ImportError:
        sys.stderr.write("ERROR: pypdfium2 not installed\n")
        return False

    if not os.path.isfile(source_path):
        sys.stderr.write(f"ERROR: Source file not found: {source_path}\n")
        return False

    try:
        src_doc = pdfium.PdfDocument(source_path)
        total = len(src_doc)
        start_idx = max(0, start_page - 1)
        end_idx = min(total, end_page)

        if start_idx >= end_idx:
            start_idx = 0
            end_idx = min(total, 1)

        page_indices = list(range(start_idx, end_idx))
        new_doc = pdfium.PdfDocument.new()
        new_doc.import_pages(src_doc, page_indices)

        os.makedirs(os.path.dirname(output_path), exist_ok=True)
        new_doc.save(output_path)
        print(f"SUCCESS:{len(page_indices)}")
        return True
    except Exception as e:
        sys.stderr.write(f"ERROR: {str(e)}\n")
        return False

if __name__ == '__main__':
    if len(sys.argv) < 5:
        sys.stderr.write("Usage: python pdf_slicer.py <source_path> <output_path> <start_page> <end_page>\n")
        sys.exit(1)

    src = sys.argv[1]
    dest = sys.argv[2]
    try:
        s = int(sys.argv[3])
        e = int(sys.argv[4])
    except ValueError:
        s, e = 1, 10

    success = slice_pdf(src, dest, s, e)
    sys.exit(0 if success else 1)
