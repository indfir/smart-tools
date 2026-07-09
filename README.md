<div align="center">

# Smart Tools

### All-in-one PDF, image & document toolkit — runs entirely in your browser.

**Convert** · **Edit** · **Organize** · **Sign** · **Print** · **AI-powered analysis**

[Features](#features) · [Live Tools](#tool-categories) · [Tech Stack](#tech-stack) · [Deployment](#deployment) · [AI Setup](#ai-setup)

---

</div>

## Overview

Smart Tools is a comprehensive, privacy-first document utility that brings **40+ tools** for PDF manipulation, image editing, format conversion, and AI-powered document analysis into a single web application. Every file is processed **100% client-side** in the user's browser — nothing is uploaded to any server.

Built as a single-file HTML application with an optional PHP proxy for AI features, it can be deployed to any static hosting provider with zero build steps.

## Features

### Privacy First
- All document processing happens **entirely in the browser**
- Files are **never uploaded** to any server
- AI features use **your own API key**, stored locally in your browser
- No tracking, no analytics, no data collection

### 40+ Tools Across 12 Categories

#### Compress
| Tool | Description |
|------|-------------|
| **Compress PDF** | Reduce PDF file size while maintaining document quality with adjustable compression levels |

#### Convert
| Tool | Description |
|------|-------------|
| **PDF Converter** | Universal converter — transform files to and from PDF in one place |

#### AI PDF (powered by your API key)
| Tool | Description |
|------|-------------|
| **AI PDF Assistant** | Ask questions and get instant answers from your document |
| **Chat with PDF** | Have a natural conversation with your document's contents |
| **AI PDF Summarizer** | Generate concise summaries with short, medium, or detailed length options |
| **Translate PDF** | Translate documents into 7+ languages with layout preservation |
| **AI Question Generator** | Auto-generate multiple choice, true/false, or open-ended quiz questions |

#### Organize
| Tool | Description |
|------|-------------|
| **Merge PDF** | Combine PDF, image, Word, Excel, and PowerPoint files into a single PDF |
| **Split PDF** | Split by page range, extract specific pages, or split every N pages |
| **Rotate PDF** | Rotate all or selected pages (90° CW/CCW, 180°) |
| **Delete PDF Pages** | Remove specific pages from a PDF document |
| **Extract PDF Pages** | Pull pages into a single PDF or separate files (ZIP) |
| **Organize PDF** | Visual drag-and-drop page reordering and deletion |

#### View & Edit
| Tool | Description |
|------|-------------|
| **Edit PDF** | Add text, highlights, drawings, and shapes |
| **PDF Annotator** | Highlight, underline, comment, and draw on PDFs |
| **PDF Reader** | Full-featured in-browser PDF viewer with page navigation and zoom |
| **Number Pages** | Add page numbers with customizable position, size, and range |
| **Crop PDF** | Trim page margins with precise mm-level control |
| **Redact PDF** | Permanently black out sensitive text or areas |
| **Watermark PDF** | Stamp text or image watermarks with opacity, rotation, and position control |
| **PDF Form Filler** | Detect and fill form fields inside PDFs |
| **Share PDF** | Generate shareable links for uploaded documents |

#### Convert from PDF
| Tool | Description |
|------|-------------|
| **PDF to Word** | Convert PDF to editable .docx with layout preservation and OCR |
| **PDF to Excel** | Extract tables into .xlsx spreadsheets |
| **PDF to PPT** | Transform pages into PowerPoint slides |
| **PDF to JPG** | Export pages as high-quality images (72/150/300 DPI) |

#### Convert to PDF
| Tool | Source Format |
|------|---------------|
| **Word to PDF** | .doc, .docx |
| **Excel to PDF** | .xls, .xlsx (single or all sheets) |
| **PPT to PDF** | .ppt, .pptx (with optional speaker notes) |
| **JPG to PDF** | .jpg, .jpeg, .png (multiple images, reorderable) |
| **PDF OCR** | Scanned PDFs and images → searchable PDF |
| **HTML to PDF** | Web pages or raw HTML → PDF |
| **TXT to PDF** | Plain text files or pasted text → PDF |
| **RTF to PDF** | Rich text files → PDF |
| **ODT to PDF** | OpenDocument text files → PDF |
| **EPUB to PDF** | E-books → printable PDF with cover page |

#### Sign
| Tool | Description |
|------|-------------|
| **Sign PDF** | Draw, type, or upload a signature and place it on your PDF |
| **Request Signatures** | Send documents for others to sign via email |

#### Print Tools
| Tool | Description |
|------|-------------|
| **Print ID Card** | Arrange KTP/ID cards into printable layouts with front-back duplex alignment, cut lines, and mm-precise positioning |
| **Print Paper Layout** | Divide paper into 2/4/8/10/custom sections and fit content into each |

#### Image Tools
| Tool | Description |
|------|-------------|
| **Crop Image** | Visual crop with free or preset ratios (including pas foto 2×3, 3×4, 4×6) |
| **Resize Image** | Resize by exact pixels or percentage with aspect ratio lock |
| **Compress Image** | Shrink file size with adjustable quality (JPG/PNG/WebP) |
| **Convert Image** | Convert between JPG, PNG, and WebP with transparency handling |
| **Remove Background** | AI-powered (on-device) or manual background removal to transparent PNG |
| **Change Background** | Replace backgrounds with solid color, image, or blurred version |
| **Image Editor** | Adjust brightness, contrast, saturation, grayscale, and blur with live preview |
| **Image to PDF** | Turn images into printable PDFs with page size and fit control |
| **Print Photo / ID Card** | Arrange pas foto or ID sizes on paper with duplex alignment |
| **Batch Image Tools** | Compress, resize, convert, and rename up to 100 images at once → ZIP |

#### Scan
| Tool | Description |
|------|-------------|
| **PDF Scanner** | Turn photos of documents into clean scanned PDFs with auto-crop and contrast enhancement |

#### More
| Tool | Description |
|------|-------------|
| **Unlock PDF** | Remove password protection from PDFs you own |
| **Protect PDF** | Add password encryption with print/copy/edit restrictions |
| **Flatten PDF** | Merge form fields and annotations into page content |

### UI/UX
- **Dark & Light themes** with system preference detection
- **Responsive design** — works on desktop, tablet, and mobile
- **Drag & drop** file upload
- **Search & filter** tools by category
- **Recent tools** quick access
- **Progress indicators** with step-by-step status
- **Toast notifications** and confirmation modals

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Frontend** | Vanilla HTML, CSS, JavaScript (single-file, no build step) |
| **PDF Rendering** | [pdf.js](https://mozilla.github.io/pdf.js/) 3.11 (lazy-loaded from CDN) |
| **PDF Manipulation** | Client-side PDF generation and page manipulation |
| **Image Processing** | Canvas API, Web APIs |
| **AI Background Removal** | ONNX model (~25 MB, runs in-browser via WebAssembly) |
| **AI Proxy** | PHP cURL proxy with allowlisted upstream providers |
| **AI Providers** | Anthropic, OpenAI, Google Gemini, OpenRouter, Groq, Alibaba DashScope |
| **Hosting** | Any static host + PHP (e.g., Hostinger, shared hosting) |

## Project Structure

```
smart-tools/
├── index.html        # Complete web application (HTML + CSS + JS, ~5400 lines)
├── ai-proxy.php      # Server-side proxy for AI provider API calls
├── .gitignore        # Git ignore rules
├── CARA-UPLOAD.txt   # Deployment guide (Indonesian)
└── README.md         # This file
```

## Deployment

### Quick Start (Static + PHP Hosting)

1. **Upload files** to your web host:
   - Upload `index.html` and `ai-proxy.php` to the **same folder** on your server
   - Example paths:
     - Main page: `public_html/index.html` + `public_html/ai-proxy.php`
     - Subfolder: `public_html/tools/index.html` + `public_html/tools/ai-proxy.php`
     - Subdomain: `pdf.yourdomain.com/index.html` + `pdf.yourdomain.com/ai-proxy.php`

2. **Open in browser** — no installation or build required.

### Hosting Options

| Provider | Notes |
|----------|-------|
| **Hostinger** | Upload via File Manager or FTP to `public_html` |
| **Any shared hosting** | Must support PHP 7.4+ for the AI proxy |
| **GitHub Pages** | Works for the frontend only (AI proxy requires PHP) |
| **Netlify / Vercel** | Frontend only; use a serverless function for AI proxy |

### Without AI Proxy

If you don't need AI features, you can deploy **only `index.html`** to any static hosting provider. All non-AI tools work without a backend.

## AI Setup

The AI features (PDF Assistant, Chat with PDF, Summarizer, Translator, Question Generator) require an API key from a supported provider.

### How It Works

1. Users enter their own API key in the app (stored in `localStorage`, never sent anywhere except the provider)
2. The app sends requests through `ai-proxy.php` to the chosen AI provider
3. The proxy validates the upstream URL against an allowlist before forwarding

### Supported Providers

| Provider | Base URL |
|----------|----------|
| Anthropic (Claude) | `https://api.anthropic.com` |
| OpenAI | `https://api.openai.com` |
| Google Gemini | `https://generativelanguage.googleapis.com` |
| OpenRouter | `https://openrouter.ai` |
| Groq | `https://api.groq.com` |
| Alibaba DashScope | `https://coding-intl.dashscope.aliyuncs.com` |

### Security Notes

- **Never hardcode your API key** in the source files — users can read it
- `ai-proxy.php` only forwards to **allowlisted** upstream URLs
- The proxy is not an open relay — it validates `X-Upstream-Origin` headers
- API keys are stored per-user in their browser's `localStorage`

## Browser Compatibility

- Chrome/Edge 90+
- Firefox 88+
- Safari 15+
- Mobile browsers (iOS Safari, Chrome for Android)

## Privacy & Security

- **Zero server-side processing** — all file operations run in the user's browser
- **No data collection** — no analytics, no cookies, no tracking
- **No file uploads** — documents never leave the user's device (except AI requests)
- **AI background removal** uses an on-device ONNX model (~25 MB, downloaded once)
- **API keys** are stored locally and sent only to the selected AI provider
- **AI proxy** is locked to approved endpoints via allowlist

## License

This project is provided as-is for personal and commercial use.

---

<div align="center">

**Built with care for privacy-first document processing.**

[GitHub](https://github.com/indfir/smart-tools) · [Report Issues](https://github.com/indfir/smart-tools/issues)

</div>
