/**
 * Script principal de Pro
 * Maneja el modo oscuro, menú móvil, ticker, sliders, polling de noticias y scroll infinito.
 */
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Modo Oscuro (Dark Mode)
    const darkModeToggle = document.getElementById('dark-mode-toggle');
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

    // 2. Menú Móvil
    const menuToggle = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('.main-navigation');
    
    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('toggled');
            const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', !isExpanded);
        });
    }

    // 3. News Ticker Slider
    const tickerSlider = document.getElementById('newsTickerSlider');
    if (tickerSlider) {
        const slides = tickerSlider.querySelectorAll('.ticker-slide');
        if (slides.length > 0) {
            let currentSlide = 0;
            slides[currentSlide].classList.add('active');
            
            if (slides.length > 1) {
                setInterval(() => {
                    slides[currentSlide].classList.remove('active');
                    slides[currentSlide].classList.add('prev');
                    
                    setTimeout(() => {
                        const prevSlide = tickerSlider.querySelector('.ticker-slide.prev');
                        if (prevSlide) prevSlide.classList.remove('prev');
                    }, 500);
                    
                    currentSlide = (currentSlide + 1) % slides.length;
                    slides[currentSlide].classList.add('active');
                }, 5000);
            }
        }
    }

    // 4. Paginación AJAX "Cargar Más" (Botón para Inicio)
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
                    // Seleccionar el contenedor (ej. portada)
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
    function mostrarNotificacionNuevasNoticias() {
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
            
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);
        }
    }

    if (typeof pro_loadmore_params !== 'undefined' && pro_loadmore_params.latest_date) {
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
        }, 60000);
    }

    // 7. Ad Sliders (Publicidad In-Feed)
    function initAdSliders() {
        const sliders = document.querySelectorAll('.in-feed-ad-slider, .header-ad');
        sliders.forEach(slider => {
            const slides = slider.querySelectorAll('.ad-slide');
            if (slides.length <= 1) return;

            let currentIndex = 0;
            setInterval(() => {
                slides[currentIndex].classList.remove('active');
                currentIndex = (currentIndex + 1) % slides.length;
                slides[currentIndex].classList.add('active');
            }, 5000);
        });
    }
    initAdSliders();

    // 8. Scroll Infinito en Categorías
    const initInfiniteScroll = () => {
        let trigger = document.querySelector('.infinite-scroll-trigger');
        if (!trigger || typeof pro_loadmore_params === 'undefined') return;

        let isFetching = false;

        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && !isFetching) {
                let currentPage = parseInt(trigger.getAttribute('data-current-page'));
                let maxPages = parseInt(trigger.getAttribute('data-max-pages'));
                let catId = trigger.getAttribute('data-cat-id');

                if (currentPage < maxPages) {
                    isFetching = true;
                    fetchNextPageAJAX(currentPage, catId, maxPages);
                }
            }
        }, {
            rootMargin: '200px'
        });

        observer.observe(trigger);

        function fetchNextPageAJAX(currentPage, catId, maxPages) {
            const formData = new FormData();
            formData.append('action', 'pro_load_more_posts');
            formData.append('category_id', catId);
            formData.append('page', currentPage);
            formData.append('nonce', pro_loadmore_params.nonce);

            fetch(pro_loadmore_params.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(html => {
                if (html.trim() !== '') {
                    const grid = document.querySelector('.category-grid');
                    if (grid) {
                        grid.insertAdjacentHTML('beforeend', html);
                    }
                    
                    let nextPage = currentPage + 1;
                    trigger.setAttribute('data-current-page', nextPage);
                    
                    if (nextPage >= maxPages) {
                        trigger.remove();
                        observer.disconnect();
                    } else {
                        isFetching = false;
                    }
                } else {
                    trigger.remove();
                    observer.disconnect();
                }
            })
            .catch(error => {
                console.error('Error cargando noticias:', error);
                isFetching = false;
            });
        }
    };
    
    initInfiniteScroll();

    // ==========================================
    // VISOR PDF DE CARTELES (LIGHTBOX)
    // ==========================================
    const carteles = document.querySelectorAll('.card-cartel');
    const pdfModal = document.getElementById('pdf-lightbox-modal');
    
    if (carteles.length > 0 && pdfModal) {
        const modalIframe = document.getElementById('pdf-iframe');
        const modalTitle = document.getElementById('pdf-modal-title');
        const closeBtn = document.querySelector('.pdf-modal-close');

        // Abrir Modal
        carteles.forEach(cartel => {
            cartel.addEventListener('click', function(e) {
                e.preventDefault();
                const pdfUrl = this.getAttribute('data-pdf-url');
                const title = this.querySelector('.entry-title').innerText;

                if (pdfUrl) {
                    modalTitle.innerText = title;
                    modalIframe.src = pdfUrl;
                    pdfModal.classList.add('active');
                    document.body.style.overflow = 'hidden'; // Prevenir scroll de fondo
                } else {
                    alert('Este cartel no tiene un documento PDF adjunto.');
                }
            });
        });

        // Función para cerrar modal
        const closeModal = () => {
            pdfModal.classList.remove('active');
            document.body.style.overflow = '';
            setTimeout(() => {
                modalIframe.src = ''; // Limpiar iframe después de la animación
            }, 300);
        };

        // Cerrar con botón X
        closeBtn.addEventListener('click', closeModal);

        // Cerrar al hacer clic fuera del contenido
        pdfModal.addEventListener('click', function(e) {
            if (e.target === pdfModal) {
                closeModal();
            }
        });

        // Cerrar con la tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && pdfModal.classList.contains('active')) {
                closeModal();
            }
        });
    }

    // ==========================================
    // FORMULARIO DE CONTACTO (AJAX)
    // ==========================================
    const contactForm = document.getElementById('pro-contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Limpiar errores previos
            const errorLayer = document.getElementById('contact-form-error');
            errorLayer.style.display = 'none';
            errorLayer.innerText = '';
            
            const inputs = contactForm.querySelectorAll('input, select, textarea');
            let hasError = false;

            inputs.forEach(input => {
                input.parentElement.classList.remove('has-error');
                if (input.required && !input.value.trim()) {
                    hasError = true;
                    input.parentElement.classList.add('has-error');
                }
                if (input.type === 'email' && input.value.trim() !== '') {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(input.value.trim())) {
                        hasError = true;
                        input.parentElement.classList.add('has-error');
                    }
                }
            });

            if (hasError) {
                errorLayer.innerText = 'Por favor, completa correctamente todos los campos marcados en rojo.';
                errorLayer.style.display = 'block';
                return;
            }

            // Preparar datos
            const formData = new FormData(contactForm);
            formData.append('action', 'pro_submit_contact_form');
            formData.append('nonce', typeof pro_ajax !== 'undefined' ? pro_ajax.nonce : ''); // Usar nonce si está disp

            // UI Loading
            const btn = document.getElementById('contact-submit-btn');
            const btnText = btn.querySelector('.btn-text');
            const btnSpinner = btn.querySelector('.btn-spinner');
            
            btn.disabled = true;
            btnText.style.display = 'none';
            btnSpinner.style.display = 'inline-block';

            // Fetch AJAX
            const ajaxUrl = (typeof pro_ajax !== 'undefined') ? pro_ajax.ajax_url : (typeof pro_loadmore_params !== 'undefined' ? pro_loadmore_params.ajax_url : '/wp-admin/admin-ajax.php');

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btnText.style.display = 'inline-block';
                btnSpinner.style.display = 'none';

                if (data.success) {
                    // Ocultar formulario suavemente y mostrar éxito
                    contactForm.style.transition = 'opacity 0.3s ease';
                    contactForm.style.opacity = '0';
                    setTimeout(() => {
                        contactForm.style.display = 'none';
                        const successLayer = document.getElementById('contact-success-layer');
                        successLayer.style.display = 'block';
                    }, 300);
                } else {
                    errorLayer.innerText = data.data.message || 'Error desconocido al enviar.';
                    errorLayer.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.disabled = false;
                btnText.style.display = 'inline-block';
                btnSpinner.style.display = 'none';
                errorLayer.innerText = 'Ocurrió un error de conexión. Intenta nuevamente.';
                errorLayer.style.display = 'block';
            });
        });

        // Limpiar error al escribir
        contactForm.querySelectorAll('input, select, textarea').forEach(input => {
            input.addEventListener('input', function() {
                this.parentElement.classList.remove('has-error');
            });
        });
    }

    /* ==========================================================================
       VALIDACIÓN DE FORMULARIOS DE BÚSQUEDA
       ========================================================================== */
    const searchForms = document.querySelectorAll('form[role="search"], .search-form');
    
    searchForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const input = form.querySelector('input[name="s"]');
            if (!input) return;
            
            let val = input.value.trim();
            
            // 1. Vacío o muy corto
            if (val.length < 2) {
                e.preventDefault();
                input.focus();
                // Opcional: mostrar un mensaje visual pequeño
                input.style.border = '2px solid #ef4444';
                setTimeout(() => input.style.border = '', 2000);
                return;
            }
            
            // 2. Contiene enlaces o dominios
            const urlPattern = /(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?|www\.|[a-zA-Z0-9\-\.]+\.(com|net|org|info)/i;
            if (urlPattern.test(val)) {
                e.preventDefault();
                input.value = '';
                input.placeholder = 'Enlaces no permitidos';
                input.style.border = '2px solid #ef4444';
                setTimeout(() => {
                    input.placeholder = 'Buscar noticias...';
                    input.style.border = '';
                }, 3000);
                return;
            }
            
            // 3. Contiene solo números o caracteres extraños (permitir letras, espacios, acentos, y puntuación básica de texto)
            // Rechaza si hay etiquetas HTML, corchetes, o demasiados signos
            const strictPattern = /^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s\.,\-\¿\?¡!]+$/;
            
            // Si el valor NO coincide con el patrón estricto
            if (!strictPattern.test(val)) {
                e.preventDefault();
                input.value = '';
                input.placeholder = 'Texto inválido (evita números/símbolos)';
                input.style.border = '2px solid #ef4444';
                setTimeout(() => {
                    input.placeholder = 'Buscar noticias...';
                    input.style.border = '';
                }, 3000);
                return;
            }
        });
    });

});
