# Smart Tools QA Testing Guide

## Overview

Smart Tools includes a comprehensive QA testing framework to verify all 19 previously-demo tools are working correctly. Tests run in the browser and verify that each tool produces real, valid output files.

## Test Suites

### 1. Regression Tests (`regression-test.html`)

**Purpose:** Verify all 19 previously-demo tools produce valid output

**Tools tested:**
- PDF Converter (PDF → JPG, DOCX, XLSX, PPTX, TXT)
- Edit PDF (add text, shapes, highlights)
- PDF Annotator (highlight, underline, comment, pen)
- Redact PDF (black out sensitive areas)
- Share PDF (prepare for sharing)
- PDF to Word/Excel/PPT (extract content to Office formats)
- Word/Excel/PPT to PDF (convert Office files)
- PDF OCR (Tesseract.js text recognition)
- HTML to PDF (convert web pages)
- RTF/ODT/EPUB to PDF (convert document formats)
- Request Signatures (add signature fields)
- Unlock/Protect PDF (password operations)

**What it verifies:**
- Each tool handler exists and executes without errors
- Output files are non-empty
- Output files have correct MIME types
- File sizes are reasonable (not corrupted)

### 2. Smoke Tests (`smoke-test.html`)

**Purpose:** Quick verification that core functionality works

**Tests:**
- PDF generation with pdf-lib
- PDF rendering with pdf.js
- Image processing with Canvas API
- ZIP file creation
- Office file generation (DOCX, XLSX, PPTX)

## Running Tests

### Option 1: Browser (Interactive)

Open the test file in a browser:

```bash
# macOS
open tests/regression-test.html

# Linux
xdg-open tests/regression-test.html

# Windows
start tests/regression-test.html
```

Click "Run All Tests" and watch results appear in real-time.

### Option 2: Headless (CI/CD)

Run tests in headless Chrome using Puppeteer:

```bash
cd tests
npm install
npm test
```

The headless runner:
- Launches Chrome in headless mode
- Loads the test page
- Runs all tests automatically
- Reports results to console
- Exits with code 0 (pass) or 1 (fail)

### Option 3: Shell Script

```bash
./tests/run-tests.sh
```

Opens the test page in your default browser.

## Test Output

### Browser Output

The test page shows:
- **Progress bar** - visual progress indicator
- **Summary cards** - total, passed, failed, skipped counts
- **Results table** - each test with status, name, category, detail, time
- **Log panel** - detailed console output

### Headless Output

```
======================================
  Test Results
======================================

Total:   19
Passed:  19 ✓
Failed:  0 ✗
Skipped: 0 -

Passed tests:
  ✓ PDF Converter - 245.3 KB (1234ms)
  ✓ Edit PDF - 12.4 KB (567ms)
  ...

======================================
RESULT: PASSED
======================================
```

## Test Coverage

### What's Tested

✓ All 19 previously-demo tools have real implementations
✓ Each tool produces non-empty output
✓ Output files have correct MIME types
✓ Office file generation (DOCX, XLSX, PPTX) creates valid ZIP structures
✓ PDF manipulation (edit, annotate, redact) modifies PDFs correctly
✓ Format conversion (PDF ↔ Office, HTML, RTF, ODT, EPUB) works
✓ OCR integration (Tesseract.js) processes images
✓ Password operations (unlock/protect) execute without errors

### What's NOT Tested

✗ Visual quality of output (requires human review)
✗ Edge cases (malformed input, very large files)
✗ Performance benchmarks
✗ Cross-browser compatibility (tested in Chrome only)
✗ AI features (require API keys)
✗ Network-dependent features

## Adding New Tests

### Adding a Tool Test

1. Add test definition to `TESTS` array:
```javascript
{ id: "tool-id", name: "Tool Name", category: "Category" }
```

2. Add test function:
```javascript
async function testToolId() {
  // Create test input
  const pdfBlob = await createTestPDF("Test content");
  const file = new File([pdfBlob], "test.pdf", { type: "application/pdf" });
  const entry = { file, name: "test.pdf", ext: ".pdf", size: pdfBlob.size };
  
  // Set up state
  state.tool = { id: "tool-id", output: { name: "output.pdf", ext: "pdf" } };
  state.files = [entry];
  state.opts = { /* tool options */ };
  state.ex = { /* extra state */ };
  
  // Run handler
  const handler = REAL_HANDLERS["tool-id"];
  if (!handler) return { pass: false, detail: "Handler not found" };
  
  const result = await handler(() => {});
  
  // Verify output
  if (!result.blob || result.blob.size === 0) return { pass: false, detail: "Empty output" };
  if (result.blob.type !== "application/pdf") return { pass: false, detail: "Wrong type" };
  
  return { pass: true, detail: `PDF (${(result.blob.size / 1024).toFixed(1)} KB)` };
}
```

3. Add case to `runTest` switch statement:
```javascript
case "tool-id":
  result = await testToolId();
  break;
```

### Adding a Smoke Test

Add a new test function that verifies a specific capability:

```javascript
async function testNewCapability() {
  try {
    // Test the capability
    const result = await someFunction();
    if (!result) throw new Error("Failed");
    return { pass: true, detail: "Capability works" };
  } catch (e) {
    return { pass: false, detail: e.message };
  }
}
```

## Troubleshooting

### Tests Fail to Load

**Problem:** Test page shows blank or "Loading..." forever

**Solution:**
- Check browser console for errors
- Ensure pdf-lib and pdf.js CDN URLs are accessible
- Try opening in a different browser

### Tests Timeout

**Problem:** Headless runner times out

**Solution:**
- Increase `TOTAL_TIMEOUT` in `headless-runner.js`
- Check if Chrome is installed: `which google-chrome` or `which chromium`
- Try running with `--no-sandbox` flag (already included)

### Tests Fail

**Problem:** One or more tests show ✗

**Solution:**
- Check the detail column for error message
- Open browser test page and run failed test individually
- Verify the tool handler exists in `index.html`
- Check if dependencies (pdf-lib, pdf.js, Tesseract.js) are loaded

### OCR Tests Fail

**Problem:** PDF OCR test fails

**Solution:**
- OCR requires internet connection to download Tesseract.js
- First run may be slow (downloads ~15MB model)
- Check browser console for Tesseract.js errors

## Continuous Integration

### GitHub Actions Example

```yaml
name: QA Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup Node.js
        uses: actions/setup-node@v3
        with:
          node-version: '18'
      
      - name: Install dependencies
        run: npm install
        working-directory: tests
      
      - name: Run tests
        run: npm test
        working-directory: tests
```

### GitLab CI Example

```yaml
test:
  image: node:18
  script:
    - cd tests
    - npm install
    - npm test
  artifacts:
    when: on_failure
    paths:
      - tests/*.log
```

## Test Maintenance

### When to Update Tests

- **New tool added:** Add test to verify it works
- **Tool modified:** Re-run tests to ensure no regression
- **Dependency updated:** Run full test suite
- **Bug fixed:** Add test case for the bug

### Test Data

Tests use generated test files:
- `createTestPDF()` - generates PDF with text
- `createTestDocx()` - generates Word document
- `createTestXlsx()` - generates Excel spreadsheet
- `createTestPptx()` - generates PowerPoint presentation
- `createTestOdt()` - generates OpenDocument text
- `createTestEpub()` - generates EPUB ebook

These are created on-the-fly, so no test fixtures need to be committed.

## Performance

### Test Duration

- **Browser:** ~30-60 seconds for all 19 tests
- **Headless:** ~45-90 seconds (includes Chrome startup)
- **OCR tests:** Add 10-20 seconds each (Tesseract.js processing)

### Optimization

- Tests run sequentially (not parallel) to avoid resource contention
- Each test creates fresh input files (no shared state)
- Results are cached in the Map for fast re-runs

## Reporting

### JSON Output

For CI integration, you can extract results as JSON:

```javascript
const results = await page.evaluate(() => {
  return Array.from(document.querySelectorAll('.result-row')).map(row => ({
    status: row.querySelector('.status').className.split(' ')[1],
    name: row.querySelector('.name').textContent,
    category: row.querySelector('.category').textContent,
    detail: row.querySelector('.detail').textContent,
    time: parseInt(row.querySelector('.time').textContent)
  }));
});
console.log(JSON.stringify(results, null, 2));
```

### Exit Codes

- `0` - All tests passed
- `1` - One or more tests failed
- `2` - Test runner error (timeout, browser crash, etc.)

## Resources

- [pdf-lib documentation](https://pdf-lib.js.org/)
- [pdf.js documentation](https://mozilla.github.io/pdf.js/)
- [Tesseract.js documentation](https://tesseract.projectnaptha.com/)
- [Puppeteer documentation](https://pptr.dev/)

## Support

For test failures or questions:
1. Check this documentation
2. Review browser console for errors
3. Run tests in browser (not headless) for better error messages
4. Check that all dependencies are loaded from CDN
5. Verify the tool handler exists in `index.html`
