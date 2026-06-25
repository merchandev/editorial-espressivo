const fs = require('fs');

const mainHeadLines = fs.readFileSync('main_head.css', 'utf16le').split(/\r?\n/);
const diffLines = fs.readFileSync('diff_utf8.txt', 'utf8').split(/\r?\n/);

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
        } else if (line.startsWith('-')) {
            // Ignore deleted lines
        } else if (line === '') {
            // End of diff or empty line
        } else {
            // A new chunk or something else? If it starts with '@@', stop.
            if (line.startsWith('@@')) {
                capture = false;
            }
        }
    }
}

// Ensure we have exactly 416 lines in newBlock!
console.log('newBlock length:', newBlock.length);

const beforeBlock = mainHeadLines.slice(0, 3399);
const afterBlock = mainHeadLines.slice(3399 + 119);

console.log('beforeBlock length:', beforeBlock.length);
console.log('afterBlock length:', afterBlock.length);

const finalLines = beforeBlock.concat(newBlock).concat(afterBlock);

fs.writeFileSync('assets/css/main.css', finalLines.join('\n'), 'utf8');
console.log('Restored main.css with ' + finalLines.length + ' lines.');
