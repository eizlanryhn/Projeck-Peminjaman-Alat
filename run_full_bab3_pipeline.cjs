const fs = require('fs');
const path = require('path');
const { PNG } = require('pngjs');
const { createWorker } = require('tesseract.js');

async function runPipeline() {
    console.log('Loading BAB-3.pdf...');
    const pdfjsLib = await import('pdfjs-dist/legacy/build/pdf.mjs');
    const pdfPath = path.resolve('BAB-3.pdf');
    const data = new Uint8Array(fs.readFileSync(pdfPath));
    const loadingTask = pdfjsLib.getDocument({ data });
    const pdfDoc = await loadingTask.promise;

    console.log(`BAB-3 has ${pdfDoc.numPages} pages.`);

    const outputDir = path.resolve('extracted_pdf_images_bab3');
    if (!fs.existsSync(outputDir)) {
        fs.mkdirSync(outputDir, { recursive: true });
    }

    let pagesData = [];
    let globalImgIndex = 0;
    let imageFiles = [];

    for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
        const page = await pdfDoc.getPage(pageNum);
        const textContent = await page.getTextContent();
        const pageText = textContent.items.map(item => item.str).join('\n');
        pagesData.push({ num: pageNum, text: pageText });

        const ops = await page.getOperatorList();
        for (let i = 0; i < ops.fnArray.length; i++) {
            if (ops.fnArray[i] === pdfjsLib.OPS.paintImageXObject) {
                const imgName = ops.argsArray[i][0];
                try {
                    const imgObj = await new Promise((resolve, reject) => {
                        page.objs.get(imgName, (obj) => {
                            if (obj) resolve(obj);
                            else reject(new Error('Object not found: ' + imgName));
                        });
                    });
                    
                    if (imgObj && imgObj.data) {
                        globalImgIndex++;
                        const width = imgObj.width;
                        const height = imgObj.height;
                        const channels = imgObj.data.length / (width * height);
                        
                        const filename = `page_${pageNum}_img_${globalImgIndex}.png`;
                        const pngPath = path.join(outputDir, filename);
                        saveAsPNG(pngPath, imgObj.data, width, height, channels);
                        imageFiles.push({ filename, pageNum, pngPath });
                    }
                } catch (e) {
                    console.log(`Error getting img on page ${pageNum}:`, e.message);
                }
            }
        }
    }

    console.log(`Saved ${imageFiles.length} image objects. Running OCR...`);
    const worker = await createWorker('eng');
    let ocrResults = {};

    for (const img of imageFiles) {
        console.log(`OCR on ${img.filename}...`);
        const { data: { text } } = await worker.recognize(img.pngPath);
        ocrResults[img.filename] = text;
    }
    await worker.terminate();

    // Build FULL_MODULE_BAB_3.md
    let fullDoc = [];
    pagesData.forEach((pageObj) => {
        const pageNum = pageObj.num;
        fullDoc.push(`\n=========================================\nPAGE ${pageNum}\n=========================================\n`);
        fullDoc.push(pageObj.text);

        const pageImgs = imageFiles.filter(i => i.pageNum === pageNum);
        pageImgs.forEach(img => {
            fullDoc.push(`\n--- [IMAGE CODE SNIPPET / SCREENSHOT: ${img.filename}] ---`);
            fullDoc.push(ocrResults[img.filename] || '');
        });
    });

    fs.writeFileSync('FULL_MODULE_BAB_3.md', fullDoc.join('\n'), 'utf-8');
    console.log('Successfully created FULL_MODULE_BAB_3.md! Length:', fullDoc.join('\n').length);
}

function saveAsPNG(pngPath, data, width, height, channels) {
    const png = new PNG({ width, height });
    for (let y = 0; y < height; y++) {
        for (let x = 0; x < width; x++) {
            const srcIdx = (y * width + x) * channels;
            const r = data[srcIdx];
            const g = channels >= 3 ? data[srcIdx + 1] : r;
            const b = channels >= 3 ? data[srcIdx + 2] : r;
            const a = channels >= 4 ? data[srcIdx + 3] : 255;

            const pngIdx = (y * width + x) * 4;
            png.data[pngIdx] = r;
            png.data[pngIdx + 1] = g;
            png.data[pngIdx + 2] = b;
            png.data[pngIdx + 3] = a;
        }
    }
    const buffer = PNG.sync.write(png);
    fs.writeFileSync(pngPath, buffer);
}

runPipeline().catch(console.error);
