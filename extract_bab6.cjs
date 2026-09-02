const fs = require('fs');
const path = require('path');

async function extractBab6() {
  const pdfjsLib = await import('pdfjs-dist/legacy/build/pdf.mjs');
  const pdfPath = path.join(__dirname, 'BAB-6.pdf');
  const data = new Uint8Array(fs.readFileSync(pdfPath));
  const loadingTask = pdfjsLib.getDocument({ data });
  const pdf = await loadingTask.promise;

  let fullText = `# FULL MODULE BAB 6\n\nTotal Pages: ${pdf.numPages}\n\n`;

  for (let i = 1; i <= pdf.numPages; i++) {
    const page = await pdf.getPage(i);
    const textContent = await page.getTextContent();
    const pageText = textContent.items.map(item => item.str).join(' ');
    fullText += `\n\n--- PAGE ${i} ---\n${pageText}`;
    process.stdout.write(`Page ${i}/${pdf.numPages}\r`);
  }

  fs.writeFileSync(path.join(__dirname, 'FULL_MODULE_BAB_6.md'), fullText, 'utf8');
  console.log('\nExtraction complete! Written to FULL_MODULE_BAB_6.md');
}

extractBab6().catch(console.error);
