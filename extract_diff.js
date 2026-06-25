const fs = require('fs');
const lines = fs.readFileSync('C:\\Users\\merch\\.gemini\\antigravity\\brain\\6b6533eb-315e-484f-8495-2a0a7cae86b5\\.system_generated\\logs\\transcript_full.jsonl', 'utf8').split('\n');

for (let i = 0; i < lines.length; i++) {
    if (lines[i].includes('diff --git a/assets/css/main.css b/assets/css/main.css') && lines[i].includes('TOOL_RESPONSE')) {
        try {
            const data = JSON.parse(lines[i]);
            let content = data.content || '';
            let patchIdx = content.indexOf('diff --git');
            if (patchIdx !== -1) {
                fs.writeFileSync('c:\\Users\\merch\\OneDrive\\Escritorio\\proyecto n2\\pro\\main.patch', content.substring(patchIdx));
            }
        } catch (e) {
            console.log("Error parsing:", e.message);
        }
    }
}
