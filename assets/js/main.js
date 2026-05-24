/**
 * Script principal de Pro
 * Maneja el modo oscuro (detectando preferencia y localstorage) y la barra de progreso de lectura.
 */
document.addEventListener('DOMContentLoaded', function() {
    

    // 2. Barra de Progreso de Lectura
    const progressBar = document.getElementById('reading-progress-bar');
    if (progressBar) {
        window.addEventListener('scroll', function() {
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            progressBar.style.width = scrolled + '%';
        });
    }

    // 3. Menú Móvil
    const menuToggle = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('.main-navigation');
    
    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('toggled');
            const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', !isExpanded);
        });
    }

    // 4. Paginación AJAX "Cargar Más"
    const loadMoreBtn = document.getElementById('load-more-btn');
    if (loadMoreBtn && typeof pro_loadmore_params !== 'undefined') {
        let currentPage = parseInt(pro_loadmore_params.current_page);
        const maxPage = parseInt(pro_loadmore_params.max_page);

        if (currentPage >= maxPage) {
            loadMoreBtn.style.display = 'none';
        }

        loadMoreBtn.addEventListener('click', function() {
            loadMoreBtn.textContent = 'Cargando...';
            loadMoreBtn.disabled = true;

            const formData = new FormData();
            formData.append('action', 'pro_load_more_posts');
            formData.append('query', pro_loadmore_params.query_vars);
            formData.append('page', currentPage);
            formData.append('nonce', pro_loadmore_params.nonce);

            fetch(pro_loadmore_params.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(html => {
                if (html.trim() !== '') {
                    // Seleccionar el contenedor dependiendo si estamos en index o category
                    let grid = document.querySelector('.secondary-posts-grid') || document.querySelector('.category-grid');
                    if (grid) {
                        grid.insertAdjacentHTML('beforeend', html);
                    }
                    
                    currentPage++;
                    if (currentPage >= maxPage) {
                        loadMoreBtn.style.display = 'none';
                    } else {
                        loadMoreBtn.textContent = 'Cargar más';
                        loadMoreBtn.disabled = false;
                    }
                } else {
                    loadMoreBtn.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error cargando posts:', error);
                loadMoreBtn.textContent = 'Cargar más';
                loadMoreBtn.disabled = false;
            });
        });
    }

    // 5. Barra de progreso de lectura (solo en Single)
    const progressBar = document.getElementById('reading-progress-bar');
    if (progressBar) {
        window.addEventListener('scroll', () => {
            const scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const progress = (scrollTop / scrollHeight) * 100;
            progressBar.style.width = progress + '%';
        });
    }

});
