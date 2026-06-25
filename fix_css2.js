const fs = require('fs');
const cssPath = 'c:\\Users\\merch\\OneDrive\\Escritorio\\proyecto n2\\pro\\assets\\css\\main.css';

let css = `
/* ======= CRITICAL RESTORED FIXES ======= */
/* 1. Header & Logos */
.custom-logo {
    max-width: 250px !important;
    height: auto !important;
}
.logo-dark { display: none !important; }
.dark-mode .logo-light { display: none !important; }
.dark-mode .logo-dark { display: block !important; }

/* 2. Menu Layout */
.main-navigation {
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: transparent !important;
}

.main-navigation > ul {
    display: flex;
    flex-direction: row;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 20px;
    align-items: center;
}

.main-navigation > ul > li {
    position: relative;
}

.main-navigation ul li a {
    font-weight: 700;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    text-decoration: none;
    color: var(--color-text, #111827);
    display: block;
    padding: 10px 0;
}

.dark-mode .main-navigation ul li a {
    color: #f8fafc;
}

/* Fix dark mode toggle icon */
.dark-mode-toggle {
    cursor: pointer;
    background: transparent;
    border: none;
    display: flex;
    align-items: center;
    color: inherit;
}
.material-symbols-outlined {
  font-family: 'Material Symbols Outlined';
  font-weight: normal;
  font-style: normal;
  font-size: 24px;
  line-height: 1;
  letter-spacing: normal;
  text-transform: none;
  display: inline-block;
  white-space: nowrap;
  word-wrap: normal;
  direction: ltr;
  -webkit-font-feature-settings: 'liga';
  -webkit-font-smoothing: antialiased;
}

.site-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
}

.header-top {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
}

.header-main {
    width: 100%;
    display: flex;
    justify-content: center;
    padding: 15px 20px;
}
`;

fs.appendFileSync(cssPath, css);
console.log("CSS appended.");
