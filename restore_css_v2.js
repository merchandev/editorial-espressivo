const fs = require('fs');
const diffLines = fs.readFileSync('diff_utf8.txt', 'utf8').split('\n');
let capture = false;
let newBlock = [];
for (let i = 0; i < diffLines.length; i++) {
    const line = diffLines[i];
    if (line.startsWith('@@ -3400,119 +3545,416 @@')) {
        capture = true;
        continue;
    }
    if (capture) {
        // We only want the ADDED lines, because the CONTEXT lines and DELETED lines
        // are already in main_head.css!
        if (line.startsWith('+')) {
            newBlock.push(line.substring(1));
        }
    }
}

const mainHeadLines = fs.readFileSync('main_head.css', 'utf16le').split('\n');
// We keep ALL of mainHeadLines!
const finalLines = mainHeadLines.concat(newBlock);

fs.writeFileSync('assets/css/main.css', finalLines.join('\n'), 'utf8');
console.log('Restored main.css with ' + finalLines.length + ' lines.');
