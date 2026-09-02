const https = require('https');
const http = require('http');
const fs = require('fs');
const path = require('path');

const fileId = '1vyVOvNu1XmLoDXU6O-LCZfmpptqMxvO2';
const outputPath = path.join(__dirname, 'BAB-6.pdf');

function download(url, dest, redirectCount = 0) {
  if (redirectCount > 10) { console.error('Too many redirects'); process.exit(1); }
  const lib = url.startsWith('https') ? https : http;
  lib.get(url, (res) => {
    if (res.statusCode >= 300 && res.statusCode < 400 && res.headers.location) {
      console.log('Redirecting to:', res.headers.location);
      download(res.headers.location, dest, redirectCount + 1);
      return;
    }
    console.log('Content-Type:', res.headers['content-type']);
    const file = fs.createWriteStream(dest);
    res.pipe(file);
    file.on('finish', () => {
      file.close();
      const size = fs.statSync(dest).size;
      console.log('Downloaded BAB-6.pdf Size:', size);
    });
  }).on('error', (err) => {
    console.error('Error downloading:', err);
  });
}

const googleUrl = `https://drive.google.com/uc?export=download&id=${fileId}&confirm=t`;
download(googleUrl, outputPath);
