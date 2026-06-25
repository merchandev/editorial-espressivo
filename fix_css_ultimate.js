const fs = require('fs');
const cssPath = 'c:\\Users\\merch\\OneDrive\\Escritorio\\proyecto n2\\pro\\assets\\css\\main.css';

let css = fs.readFileSync(cssPath, 'utf8');

// 1. Fix the font-family
css = css.replace(/font-family: 'Material Symbols Outlined' !important;/g, "font-family: 'Material Icons Outlined' !important;");

// 2. Fix the font-weight and other ligatures for the icon
css = css.replace(/font-weight: normal;/g, "font-weight: normal !important;");
css = css.replace(/font-style: normal;/g, "font-style: normal !important;");
css = css.replace(/letter-spacing: normal;/g, "letter-spacing: normal !important;");
css = css.replace(/text-transform: none;/g, "text-transform: none !important;");

// 3. Fix the desktop menu wrapper selector
css = css.replace(/\.desktop-menu-wrapper ul {/g, ".desktop-menu-wrapper > ul {");
css = css.replace(/\.desktop-menu-wrapper ul li {/g, ".desktop-menu-wrapper > ul > li {");
css = css.replace(/\.desktop-menu-wrapper ul li a {/g, ".desktop-menu-wrapper > ul > li > a {");

// 4. Add overflow visible and submenu hover fixes
css += `
/* ======= ULTIMATE FIXES ======= */
.desktop-menu-wrapper > ul {
    overflow: visible !important;
}
.desktop-menu-wrapper > ul > li {
    position: relative;
}
.desktop-menu-wrapper > ul > li:hover > .sub-menu {
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
}
body .main-navigation .sub-menu {
    background: #0f0f0f !important;
}
body.dark-mode .main-navigation .sub-menu {
    background: #0f0f0f !important;
}
`;

fs.writeFileSync(cssPath, css);
console.log("Ultimate fixes applied.");
