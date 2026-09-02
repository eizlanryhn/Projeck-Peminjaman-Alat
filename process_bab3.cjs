const fs = require('fs');
const path = require('path');
const { PNG } = require('pngjs');

async function processBAB3() {
    const pdfjsLib = await import('pdfjs-dist/legacy/build/pdf.mjs');
    const pdfPath = path.resolve('BAB-3.pdf');
    const data = new Uint8Array(fs.readFileSync(pdfPath));
    const loadingTask = pdfjsLib.getDocument({ data });
    const pdfDoc = await loadingTask.promise;

    console.log(`BAB-3 Total Pages: ${pdfDoc.numPages}`);

    // Step 1: Extract Text per page
    let pagesData = [];
    for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
        const page = await pdfDoc.getPage(pageNum);
        const textContent = await page.getTextContent();
        const pageText = textContent.items.map(item => item.str).join('\n');
        pagesData.push({ num: pageNum, text: pageText });
    }
    fs.writeFileSync('extracted_pdf_text_bab3.json', JSON.stringify({ pages: pagesData }, null, 2), 'utf-8');
    console.log('Saved extracted_pdf_text_bab3.json');

    // Step 2: Extract Images per page
    const outputDir = path.resolve('extracted_pdf_images_bab3');
    if (!fs.existsSync(outputDir)) {
        fs.mkdirSync(outputDir, { recursive: true });
    }

    let globalImgIndex = 0;
    for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
        const page = await pdfDoc.getPage(pageNum);
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
                        
                        const pngPath = path.join(outputDir, `page_${pageNum}_img_${globalImgIndex}.png`);
                        saveAsPNG(pngPath, imgObj.data, width, height, channels);
                        console.log(`Page ${pageNum} Image ${globalImgIndex} saved: ${width}x${height}`);
                    }
                } catch (e) {
                    console.log(`Error getting img ${imgName} on page ${pageNum}:`, e.message);
                }
            }
        }
    }
    console.log(`Extracted total ${globalImgIndex} image objects from BAB-3.pdf`);
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

processBAB3().catch(console.error);
