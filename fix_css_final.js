const fs = require('fs');
const cssPath = 'c:\\Users\\merch\\OneDrive\\Escritorio\\proyecto n2\\pro\\assets\\css\\main.css';

let css = `
/* ======= CRITICAL RESTORED FIXES FINAL ======= */

/* Header Top */
.header-topbar {
    border-bottom: 1px solid rgba(0,0,0,0.05);
}
.dark-mode .header-topbar {
    border-bottom-color: rgba(255,255,255,0.05);
}

/* Header Main (Logos) */
.custom-logo {
    max-width: 320px !important;
    height: auto !important;
    display: inline-block;
}
.logo-dark { display: none !important; }
.logo-light { display: inline-block !important; }

.dark-mode .logo-light { display: none !important; }
.dark-mode .logo-dark { display: inline-block !important; }

/* Desktop Menu Layout */
.desktop-menu-wrapper ul {
    display: flex !important;
    flex-wrap: wrap;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 15px;
}
.desktop-menu-wrapper ul li {
    display: block;
}
.desktop-menu-wrapper ul li a {
    color: #fff !important;
    text-decoration: none;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 13px;
    padding: 10px 5px;
    display: block;
}

/* Fix mobile vs desktop wrapper visibility */
@media (min-width: 768px) {
    .mobile-menu-wrapper { display: none !important; }
    .desktop-menu-wrapper { display: block !important; }
}
@media (max-width: 767px) {
    .mobile-menu-wrapper { display: block !important; }
    .desktop-menu-wrapper { display: none !important; }
}

/* Dark Mode Icon */
.material-symbols-outlined {
    font-family: 'Material Symbols Outlined' !important;
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

/* Ticker Último Minuto */
.news-ticker-wrapper {
    background-color: #f1f5f9;
    border-bottom: 1px solid #e2e8f0;
    padding: 8px 0;
}
.dark-mode .news-ticker-wrapper {
    background-color: #0f172a;
    border-bottom-color: #1e293b;
}
.news-ticker-inner {
    display: flex;
    align-items: center;
    max-width: 1300px;
    margin: 0 auto;
    padding: 0 15px;
}
.news-ticker-label {
    background-color: #0f172a;
    color: #fff;
    padding: 4px 12px;
    font-weight: 700;
    font-size: 12px;
    text-transform: uppercase;
    border-radius: 4px;
    margin-right: 15px;
    white-space: nowrap;
}
.dark-mode .news-ticker-label {
    background-color: var(--color-primary);
}
.news-ticker-slider {
    flex-grow: 1;
    overflow: hidden;
    white-space: nowrap;
}
.ticker-slide {
    display: none;
}
.ticker-slide.active {
    display: block;
}
.ticker-slide a {
    color: #d97706;
    font-weight: 600;
    text-decoration: none;
    font-size: 14px;
}
.dark-mode .ticker-slide a {
    color: #fbbf24;
}
.ticker-slide a:hover {
    text-decoration: underline;
}

/* Sponsor Image Max-height fix removed earlier */
.sponsor-banner-img {
    width: 100%;
    height: auto;
    display: block;
    transition: opacity 0.3s ease;
}
`;

fs.appendFileSync(cssPath, css);
console.log("CSS appended.");
