const fs = require('fs');
const cssPath = 'c:\\Users\\merch\\OneDrive\\Escritorio\\proyecto n2\\pro\\assets\\css\\main.css';

let css = fs.readFileSync(cssPath, 'utf8');

// Replace ANY literal backslash-n sequences with actual newlines
css = css.replace(/\\n/g, '\n');

fs.writeFileSync(cssPath, css);
console.log('Fixed newlines properly');
