const https = require('https');
const fs = require('fs');

function download(url, dest, cb) {
  const file = fs.createWriteStream(dest);
  https.get(url, { headers: { 'User-Agent': 'Mozilla/5.0' } }, function(response) {
    if (response.statusCode === 302 || response.statusCode === 301 || response.statusCode === 303) {
      console.log('Redirecting to:', response.headers.location);
      download(response.headers.location, dest, cb);
      return;
    }
    console.log('Content-Type:', response.headers['content-type']);
    response.pipe(file);
    file.on('finish', function() {
      file.close(() => {
        console.log('Downloaded BAB-5.pdf Size:', fs.statSync(dest).size);
        if (cb) cb();
      });
    });
  }).on('error', function(err) {
    fs.unlink(dest, () => {});
    console.error('Error downloading:', err);
  });
}

const fileId = '16mlVV12RUkTJDDnAOl_a5Uh7DwqCI0CO';
const downloadUrl = `https://drive.google.com/uc?export=download&id=${fileId}`;

download(downloadUrl, 'BAB-5.pdf');
