const fs = require('fs');
const cssPath = 'c:\\Users\\merch\\OneDrive\\Escritorio\\proyecto n2\\pro\\assets\\css\\main.css';

let css = `
/* ======= URGENT CSS FIXES (Restored) ======= */
.site-logo img, .custom-logo {
    max-width: 250px !important;
    height: auto !important;
}

.dark-mode .site-logo img, .dark-mode .custom-logo {
    filter: brightness(0) invert(1) !important;
}

/* Header & Menu fixes */
.main-navigation {
    display: flex;
    align-items: center;
    background-color: transparent !important;
}

.main-navigation ul {
    display: flex;
    flex-direction: row;
    list-style: none;
    margin: 0;
    padding: 0;
}

.main-navigation ul li {
    margin-right: 15px;
}

.main-navigation ul li a {
    font-weight: 700;
    letter-spacing: 0.5px;
    position: relative;
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
}

.header-actions .search-form {
    border-color: transparent;
    transition: all 0.3s ease;
}

.dark-mode .category-sponsor-banner {
    background: var(--color-primary);
    border-color: var(--color-border);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.dark-mode .sponsor-header {
    background: var(--color-primary);
    border-bottom-color: var(--color-border);
}

.sponsor-banner-img {
    width: 100%;
    height: auto;
    display: block;
    transition: opacity 0.3s ease;
}

.dark-mode-toggle .material-icons {
    font-family: 'Material Icons';
}
`;

fs.appendFileSync(cssPath, css);
console.log("CSS appended.");
