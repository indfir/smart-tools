# PDF Tools Online

A simple online PDF utility web app with browser-based document tools and an optional AI proxy backend.

## Files
- `index.html` — main web app UI and client-side PDF tools
- `ai-proxy.php` — PHP proxy for AI provider requests
- `CARA-UPLOAD.txt` — deployment notes for Hostinger/indfir.com

## Features
- Merge, split, rotate, delete/extract pages
- Organize, compress, watermark, page numbering
- JPG/PDF conversion, TXT to PDF, scanner-style workflows
- Optional AI-powered features via a user-supplied API key

## Deployment
This project is designed to run as a static frontend plus PHP proxy. Upload both `index.html` and `ai-proxy.php` to the same folder on a PHP-capable host such as Hostinger.

## Notes
- The core PDF tools run directly in the browser.
- The AI proxy only forwards requests to approved upstream providers.
