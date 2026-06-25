const fs = require('fs');
const diffLines = fs.readFileSync('diff.txt', 'utf16le').split('\n');
let capture = false;
let newBlock = [];
for (let i = 0; i < diffLines.length; i++) {
    const line = diffLines[i];
    if (line.startsWith('@@ -3400,119 +3545,416 @@')) {
        capture = true;
        continue;
    }
    if (capture) {
        if (line.startsWith('+') || line.startsWith(' ')) {
            newBlock.push(line.substring(1));
        }
    }
}

const mainHeadLines = fs.readFileSync('main_head.css', 'utf16le').split('\n');
// The hunk says it replaces lines 3400 to 3519.
// Note: line numbers in diff are 1-based.
// We need to keep lines 0 to 3398 (3399 lines).
// Let's just slice it.
const finalLines = mainHeadLines.slice(0, 3399).concat(newBlock);

fs.writeFileSync('assets/css/main.css', finalLines.join('\n'), 'utf8');
console.log('Restored main.css with ' + finalLines.length + ' lines.');
