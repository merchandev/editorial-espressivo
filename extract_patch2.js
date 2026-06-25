const fs = require('fs');

const data = fs.readFileSync('C:\\Users\\merch\\.gemini\\antigravity\\brain\\6b6533eb-315e-484f-8495-2a0a7cae86b5\\.system_generated\\logs\\transcript_full.jsonl', 'utf8');
const lines = data.split('\n');

for (let line of lines) {
    if (!line) continue;
    try {
        const obj = JSON.parse(line);
        if (obj.type === 'RUN_COMMAND' && obj.content && obj.content.includes('diff --git a/assets/css/main.css') && !obj.content.includes('fs.writeFileSync')) {
            let str = obj.content;
            let idx = str.indexOf('diff --git');
            if (idx !== -1) {
                fs.writeFileSync('c:\\Users\\merch\\OneDrive\\Escritorio\\proyecto n2\\pro\\main.patch', str.substring(idx));
                console.log("Patch successfully written to main.patch");
            }
        } 
    } catch(e) {}
}
