#!/usr/bin/env node
/**
 * Smart Tools Headless Test Runner
 * 
 * Runs regression tests in headless Chrome using Puppeteer.
 * Reports results to console and exits with appropriate code.
 * 
 * Usage:
 *   npm install
 *   npm test
 */

const puppeteer = require('puppeteer');
const path = require('path');

const TEST_TIMEOUT = 120000; // 2 minutes per test
const TOTAL_TIMEOUT = 600000; // 10 minutes total

async function runTests() {
  console.log('======================================');
  console.log('  Smart Tools Headless Test Runner');
  console.log('======================================\n');

  let browser;
  try {
    console.log('Launching headless Chrome...');
    browser = await puppeteer.launch({
      headless: 'new',
      args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage']
    });

    const page = await browser.newPage();
    
    // Enable console logging from the page
    page.on('console', msg => {
      const type = msg.type();
      const text = msg.text();
      if (type === 'error') console.error(`[BROWSER] ${text}`);
      else if (type === 'warning') console.warn(`[BROWSER] ${text}`);
      else console.log(`[BROWSER] ${text}`);
    });

    page.on('pageerror', err => {
      console.error(`[PAGE ERROR] ${err.message}`);
    });

    const testFile = path.join(__dirname, 'regression-test.html');
    const fileUrl = `file://${testFile}`;
    
    console.log(`Loading test page: ${fileUrl}\n`);
    
    await page.goto(fileUrl, { waitUntil: 'networkidle0', timeout: 30000 });
    
    // Wait for the app to be ready
    await page.waitForSelector('#app', { timeout: 10000 });
    
    console.log('Test page loaded. Starting tests...\n');
    
    // Click "Run All Tests" button
    await page.click('#run-all');
    
    // Wait for all tests to complete
    const startTime = Date.now();
    let lastStatus = '';
    
    while (true) {
      const status = await page.$eval('#status', el => el.textContent);
      const progress = await page.$eval('#progress-fill', el => el.style.width);
      
      if (status !== lastStatus) {
        console.log(`[${progress}] ${status}`);
        lastStatus = status;
      }
      
      if (status.includes('completed') || status.includes('All tests')) {
        break;
      }
      
      if (Date.now() - startTime > TOTAL_TIMEOUT) {
        throw new Error('Test timeout exceeded');
      }
      
      await new Promise(resolve => setTimeout(resolve, 500));
    }
    
    // Get final results
    const results = await page.evaluate(() => {
      const total = parseInt(document.getElementById('total').textContent);
      const passed = parseInt(document.getElementById('passed').textContent);
      const failed = parseInt(document.getElementById('failed').textContent);
      const skipped = parseInt(document.getElementById('skipped').textContent);
      
      const rows = Array.from(document.querySelectorAll('.result-row')).map(row => ({
        status: row.querySelector('.status').className.split(' ')[1],
        name: row.querySelector('.name').textContent,
        category: row.querySelector('.category').textContent,
        detail: row.querySelector('.detail').textContent,
        time: row.querySelector('.time').textContent
      }));
      
      return { total, passed, failed, skipped, rows };
    });
    
    console.log('\n======================================');
    console.log('  Test Results');
    console.log('======================================\n');
    
    console.log(`Total:   ${results.total}`);
    console.log(`Passed:  ${results.passed} ✓`);
    console.log(`Failed:  ${results.failed} ✗`);
    console.log(`Skipped: ${results.skipped} -`);
    console.log('');
    
    if (results.failed > 0) {
      console.log('Failed tests:');
      results.rows.filter(r => r.status === 'fail').forEach(r => {
        console.log(`  ✗ ${r.name} (${r.category})`);
        console.log(`    ${r.detail}`);
      });
      console.log('');
    }
    
    if (results.passed > 0) {
      console.log('Passed tests:');
      results.rows.filter(r => r.status === 'pass').forEach(r => {
        console.log(`  ✓ ${r.name} - ${r.detail} (${r.time})`);
      });
    }
    
    console.log('\n======================================');
    
    if (results.failed > 0) {
      console.log('RESULT: FAILED');
      console.log('======================================\n');
      process.exit(1);
    } else {
      console.log('RESULT: PASSED');
      console.log('======================================\n');
      process.exit(0);
    }
    
  } catch (error) {
    console.error('\n======================================');
    console.error('  Test Runner Error');
    console.error('======================================\n');
    console.error(error.message);
    
    if (error.stack) {
      console.error('\nStack trace:');
      console.error(error.stack);
    }
    
    process.exit(2);
  } finally {
    if (browser) {
      await browser.close();
    }
  }
}

runTests();
