/**
 * Script principal de Pro
 * Maneja el modo oscuro (detectando preferencia y localstorage) y la barra de progreso de lectura.
 */
document.addEventListener('DOMContentLoaded', function() {
    

    // 1. Modo Oscuro (Dark Mode)
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    
    // Obtener preferencia guardada. Por defecto siempre es "light".
    const currentTheme = localStorage.getItem('theme');
    if (currentTheme === 'dark') {
        document.body.classList.add('dark-mode');
    }

    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function(e) {
            e.preventDefault();
            document.body.classList.toggle('dark-mode');
            let theme = 'light';
            if (document.body.classList.contains('dark-mode')) {
                theme = 'dark';
            }
            localStorage.setItem('theme', theme);
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
            formData.append('category_id', pro_loadmore_params.category_id);
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

    // 6. Actualización Automática de Noticias (AJAX Polling)
    if (typeof pro_loadmore_params !== 'undefined' && pro_loadmore_params.latest_date) {
        // Ejecutar cada 60 segundos
        setInterval(function() {
            const formData = new FormData();
            formData.append('action', 'pro_check_new_posts');
            formData.append('latest_date', pro_loadmore_params.latest_date);
            formData.append('nonce', pro_loadmore_params.nonce);

            fetch(pro_loadmore_params.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.has_new_posts) {
                    mostrarNotificacionNuevasNoticias();
                }
            })
            .catch(error => console.error('Error comprobando nuevas noticias:', error));
        }, 60000); // 60 segundos
    }

    function mostrarNotificacionNuevasNoticias() {
        // Solo mostrar si no existe ya
        if (!document.getElementById('new-posts-toast')) {
            const toast = document.createElement('div');
            toast.id = 'new-posts-toast';
            toast.className = 'new-posts-toast';
            toast.innerHTML = '<span>Hay nuevas noticias. Haz clic para actualizar.</span>';
            
            toast.addEventListener('click', function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            });

            document.body.appendChild(toast);
            
            // Animación de entrada
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);
        }
    }

});
