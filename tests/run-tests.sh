#!/bin/bash
# Smart Tools QA Test Runner
# Runs regression tests in headless browser and reports results

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"

echo "======================================"
echo "  Smart Tools QA Test Runner"
echo "======================================"
echo ""
echo "Project: $PROJECT_DIR"
echo "Date: $(date)"
echo ""

# Check if we have a browser available
if command -v open &> /dev/null; then
  BROWSER_CMD="open"
elif command -v xdg-open &> /dev/null; then
  BROWSER_CMD="xdg-open"
else
  echo "ERROR: No browser found. Please open the test file manually."
  echo "  File: $SCRIPT_DIR/regression-test.html"
  exit 1
fi

echo "Opening regression tests in browser..."
echo ""
$BROWSER_CMD "$SCRIPT_DIR/regression-test.html"

echo "Test page opened. Click 'Run All Tests' to start."
echo ""
echo "Test files:"
echo "  • $SCRIPT_DIR/regression-test.html  (19 tools regression)"
echo "  • $SCRIPT_DIR/smoke-test.html       (smoke tests)"
echo ""
echo "To run headless tests, install puppeteer and use:"
echo "  node $SCRIPT_DIR/headless-runner.js"
