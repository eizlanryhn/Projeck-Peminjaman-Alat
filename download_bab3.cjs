const https = require('https');
const http = require('http');
const fs = require('fs');
const URL = require('url');

function get(urlStr, callback) {
    const parsed = URL.parse(urlStr);
    const mod = parsed.protocol === 'https:' ? https : http;
    mod.get(urlStr, { headers: { 'User-Agent': 'Mozilla/5.0' } }, (res) => {
        if (res.statusCode >= 300 && res.statusCode < 400 && res.headers.location) {
            let redirectUrl = res.headers.location;
            if (!redirectUrl.startsWith('http')) {
                redirectUrl = parsed.protocol + '//' + parsed.host + redirectUrl;
            }
            console.log('Redirecting to:', redirectUrl);
            get(redirectUrl, callback);
        } else {
            callback(res);
        }
    }).on('error', (err) => console.error('Error:', err));
}

function downloadFile(id, dest) {
    const url = 'https://drive.google.com/uc?export=download&id=' + id;
    get(url, (res) => {
        let contentType = res.headers['content-type'] || '';
        console.log('Content-Type:', contentType);
        if (contentType.includes('text/html')) {
            let body = '';
            res.on('data', chunk => body += chunk);
            res.on('end', () => {
                let match = body.match(/href="(\/uc\?export=download[^"]+)"/);
                if (match) {
                    let confirmUrl = 'https://drive.google.com' + match[1].replace(/&amp;/g, '&');
                    console.log('Found confirm link, downloading...');
                    const file = fs.createWriteStream(dest);
                    get(confirmUrl, (res2) => {
                        res2.pipe(file);
                        file.on('finish', () => {
                            file.close(() => console.log('Downloaded', dest, 'Size:', fs.statSync(dest).size));
                        });
                    });
                } else {
                    console.log('No confirm link found in HTML response');
                }
            });
        } else {
            const file = fs.createWriteStream(dest);
            res.pipe(file);
            file.on('finish', () => {
                file.close(() => console.log('Downloaded', dest, 'Size:', fs.statSync(dest).size));
            });
        }
    });
}

downloadFile('1NTmd_znjVZRkC4IgQfbMmfPGLbE6gaZX', 'BAB-3.pdf');
