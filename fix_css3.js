const fs = require('fs');
const cssPath = 'c:\\Users\\merch\\OneDrive\\Escritorio\\proyecto n2\\pro\\assets\\css\\main.css';

let css = `
/* ======= CRITICAL RESTORED FIXES 3 ======= */

/* Header Top */
.header-top {
    display: flex;
    justify-content: space-between;
    padding: 10px 20px;
    font-size: 13px;
    border-bottom: 1px solid #eaeaea;
}

.dark-mode .header-top {
    border-bottom-color: #333;
}

/* Header Main (Logos) */
.header-main {
    text-align: center;
    padding: 20px;
}

.custom-logo {
    max-width: 400px !important;
    height: auto !important;
    display: inline-block;
}

/* Dark mode logo toggle */
.logo-dark { display: none !important; }
.logo-light { display: inline-block !important; }

.dark-mode .logo-light { display: none !important; }
.dark-mode .logo-dark { display: inline-block !important; }

/* Menu Nav Bar */
.main-navigation {
    background-color: #000 !important;
    color: #fff !important;
    text-align: center;
    padding: 10px 0;
}

.dark-mode .main-navigation {
    background-color: #111 !important;
}

.main-navigation > ul {
    display: flex !important;
    flex-wrap: wrap;
    justify-content: center;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 20px;
}

.main-navigation ul li {
    display: inline-block;
}

.main-navigation ul li a {
    color: #fff !important;
    text-decoration: none;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 14px;
    padding: 5px 10px;
}

/* Dark Mode Icon */
.material-symbols-outlined {
  font-family: 'Material Symbols Outlined', sans-serif !important;
  font-size: 20px;
  vertical-align: middle;
}

/* Ticker Último Minuto */
.news-ticker-wrapper {
    background-color: #f8f9fa;
    border-bottom: 1px solid #ddd;
    padding: 10px 0;
}

.dark-mode .news-ticker-wrapper {
    background-color: #1a1a1a;
    border-bottom-color: #333;
}

.news-ticker-inner {
    display: flex;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.news-ticker-label {
    background-color: #000;
    color: #fff;
    padding: 5px 15px;
    font-weight: bold;
    border-radius: 20px;
    margin-right: 15px;
    white-space: nowrap;
}

.news-ticker-slider {
    flex-grow: 1;
    overflow: hidden;
}

.ticker-slide a {
    color: #d97706; /* Color naranja/dorado según la imagen */
    font-weight: bold;
    text-decoration: none;
}
`;

fs.appendFileSync(cssPath, css);
console.log("CSS appended.");
