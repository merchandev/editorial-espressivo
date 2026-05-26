<?php
/**
 * Funciones y definiciones del tema Pro
 *
 * @package Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Salir si se accede directamente.
}

/**
 * Configuración inicial del tema.
 */
function pro_setup() {
    // Traducciones
    load_theme_textdomain( 'pro', get_template_directory() . '/languages' );

    // Título dinámico
    add_theme_support( 'title-tag' );

    // Soporte para imágenes destacadas
    add_theme_support( 'post-thumbnails' );

    // Tamaños de imágenes personalizados para mejor rendimiento
    // Héroe: Noticia principal (LCP)
    add_image_size( 'hero-thumbnail', 1200, 675, true ); 
    // Card: Noticias secundarias
    add_image_size( 'card-thumbnail', 600, 400, true ); 
    
    // Registrar menús de navegación
    register_nav_menus( array(
        'primary' => esc_html__( 'Menú Principal', 'pro' ),
        'footer'  => esc_html__( 'Menú del Pie de Página', 'pro' ),
        'topbar'  => esc_html__( 'Menú Superior', 'pro' ),
    ) );

    // Soporte HTML5
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ) );

    // Logo personalizado
    add_theme_support( 'custom-logo', array(
        'height'      => 80,
        'width'       => 250,
        'flex-width'  => true,
        'flex-height' => true,
    ) );
}
add_action( 'after_setup_theme', 'pro_setup' );

function pro_add_preconnect() {
    // Preconnect a Google Fonts para mejorar LCP
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
}
add_action( 'wp_head', 'pro_add_preconnect', 1 );

/**
 * Encolar scripts y estilos.
 */
function pro_scripts() {
    // Fuentes de Google (Playfair Display, Source Serif 4, Inter)
    wp_enqueue_style( 'pro-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Source+Serif+4:ital,wght@0,400;0,600;1,400&display=swap', array(), null );

    // Iconos de Google (Material Symbols Outlined)
    wp_enqueue_style( 'pro-icons', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap', array(), null );

    // Normalize.css para consistencia entre navegadores
    wp_enqueue_style( 'pro-normalize', 'https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css', array(), '8.0.1' );

    // Estilos principales (CSS customizado)
    $css_ver = filemtime( get_template_directory() . '/assets/css/main.css' );
    wp_enqueue_style( 'pro-main-style', get_template_directory_uri() . '/assets/css/main.css', array(), $css_ver );

    // Estilos de WordPress (style.css para variables CSS)
    $style_ver = filemtime( get_stylesheet_directory() . '/style.css' );
    wp_enqueue_style( 'pro-style', get_stylesheet_uri(), array('pro-main-style'), $style_ver );

    // Script principal (Modo oscuro, progreso de lectura, scroll infinito)
    $js_ver = filemtime( get_template_directory() . '/assets/js/main.js' );
    wp_enqueue_script( 'pro-main-js', get_template_directory_uri() . '/assets/js/main.js', array(), $js_ver, true );

    // Script para buscador Ajax
    wp_enqueue_script( 'pro-ajax-search', get_template_directory_uri() . '/assets/js/ajax-search.js', array(), '1.0.0', true );
    
    // Generar Nonce seguro una sola vez por petición
    $ajax_nonce = wp_create_nonce( 'pro_ajax_nonce' );

    // Pasar URL de admin-ajax al script
    wp_localize_script( 'pro-ajax-search', 'pro_ajax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => $ajax_nonce
    ));

    global $wp_query;
    // Obtener la fecha del post más reciente para el polling
    $latest_post = get_posts( array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC'
    ) );
    $latest_date = ! empty( $latest_post ) ? $latest_post[0]->post_date : '';

    $cat_id = 0;
    if ( is_category() ) {
        $cat_id = get_queried_object_id();
    } elseif ( is_page_template( 'page-categoria.php' ) ) {
        $category = get_term_by( 'name', get_the_title(), 'category' );
        if ( $category ) {
            $cat_id = $category->term_id;
        }
    }

    wp_localize_script( 'pro-main-js', 'pro_loadmore_params', array(
        'ajax_url'     => admin_url( 'admin-ajax.php' ),
        'current_page' => get_query_var( 'paged' ) ? get_query_var('paged') : 1,
        'max_page'     => $wp_query->max_num_pages,
        'query_vars'   => json_encode( $wp_query->query_vars ),
        'category_id'  => $cat_id,
        'latest_date'  => $latest_date,
        'nonce'        => $ajax_nonce
    ));
}
add_action( 'wp_enqueue_scripts', 'pro_scripts' );

/**
 * Diferir scripts (Defer) para no bloquear el renderizado
 */
function pro_defer_scripts( $tag, $handle ) {
    // Si estamos en el admin, no modificamos
    if ( is_admin() ) {
        return $tag;
    }
    
    // Lista de scripts a diferir
    $defer_scripts = array( 'pro-main-js', 'pro-ajax-search' );
    
    if ( in_array( $handle, $defer_scripts ) ) {
        return str_replace( ' src', ' defer="defer" src', $tag );
    }
    
    return $tag;
}
add_filter( 'script_loader_tag', 'pro_defer_scripts', 10, 2 );

/**
 * Registrar áreas de widgets (Para publicidad y pie de página)
 */
function pro_widgets_init() {
    register_sidebar( array(
        'name'          => esc_html__( 'Sidebar Principal', 'pro' ),
        'id'            => 'sidebar-1',
        'description'   => esc_html__( 'Añade widgets aquí.', 'pro' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );
    
    // Espacio para publicidad en Header
    register_sidebar( array(
        'name'          => esc_html__( 'Anuncio Cabecera', 'pro' ),
        'id'            => 'ad-header',
        'description'   => esc_html__( 'Añade un banner publicitario de 728x90 aquí.', 'pro' ),
        'before_widget' => '<div class="ad-container ad-header">',
        'after_widget'  => '</div>',
    ) );

    // Espacios publicitarios in-feed
    register_sidebar( array(
        'name'          => esc_html__( 'Anuncio In-Feed', 'pro' ),
        'id'            => 'ad-in-feed',
        'description'   => esc_html__( 'Añade banners que aparecerán entre las noticias.', 'pro' ),
        'before_widget' => '<div class="ad-container ad-in-feed">',
        'after_widget'  => '</div>',
    ) );
}
add_action( 'widgets_init', 'pro_widgets_init' );

/**
 * Seguridad: Remover versión de WordPress de la cabecera
 */
remove_action('wp_head', 'wp_generator');

/**
 * Buscador Predictivo (Ajax Endpoint)
 */
function pro_ajax_search() {
    check_ajax_referer('pro_ajax_nonce', 'nonce');

    $search_query = isset($_POST['s']) ? sanitize_text_field($_POST['s']) : '';
    
    if($search_query) {
        $args = array(
            's' => $search_query,
            'posts_per_page' => 5,
            'post_status' => 'publish'
        );
        $query = new WP_Query($args);
        
        if($query->have_posts()) {
            echo '<ul class="ajax-search-results">';
            while($query->have_posts()) {
                $query->the_post();
                echo '<li><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<p class="ajax-no-results">No se encontraron resultados.</p>';
        }
        wp_reset_postdata();
    }
    
    wp_die();
}
add_action('wp_ajax_nopriv_pro_ajax_search', 'pro_ajax_search');
add_action('wp_ajax_pro_ajax_search', 'pro_ajax_search');

/**
 * Registro de Custom Post Types y Taxonomías
 */
function pro_register_cpts() {
    // Clasificado

    register_post_type('clasificado', array(
        'labels'      => array('name' => 'Clasificados', 'singular_name' => 'Clasificado'),
        'public'      => true,
        'has_archive' => true,
        'supports'    => array('title', 'editor', 'thumbnail'),
        'menu_icon'   => 'dashicons-megaphone'
    ));
    // Taxonomía: Municipio (solo para clasificados ahora)
    register_taxonomy('municipio', array('clasificado'), array(
        'labels'       => array('name' => 'Municipios', 'singular_name' => 'Municipio'),
        'hierarchical' => false,
        'show_in_rest' => true
    ));
    register_taxonomy('tipo_clasificado', 'clasificado', array(
        'labels'       => array('name' => 'Tipos de Clasificado', 'singular_name' => 'Tipo de Clasificado'),
        'hierarchical' => true,
        'show_in_rest' => true
    ));
}

/**
 * Añadir Rol Dirección y restringir menús
 */
function pro_add_direccion_role() {
    if ( ! get_role( 'direccion' ) ) {
        $admin_role = get_role( 'administrator' );
        if ( $admin_role ) {
            add_role( 'direccion', 'Dirección', $admin_role->capabilities );
        }
    }
}
add_action( 'init', 'pro_add_direccion_role' );

function pro_restrict_direccion_menus() {
    $current_user = wp_get_current_user();
    if ( in_array( 'direccion', (array) $current_user->roles ) ) {
        // Quitar Hostinger y Hostinger Reach (slugs comunes)
        remove_menu_page( 'hostinger' );
        remove_menu_page( 'hostinger-reach' );
        remove_menu_page( 'toplevel_page_hostinger' ); // Por si acaso usa toplevel
        
        // Quitar menús nativos de WP solicitados
        remove_menu_page( 'edit-comments.php' ); // Comentarios
        remove_menu_page( 'themes.php' );        // Apariencia
        remove_menu_page( 'plugins.php' );       // Plugins
        remove_menu_page( 'tools.php' );         // Herramientas
        remove_menu_page( 'options-general.php' ); // Ajustes
    }
}
add_action( 'admin_menu', 'pro_restrict_direccion_menus', 999 );
add_action('init', 'pro_register_cpts');

/**
 * Paginación AJAX (Cargar más)
 */
function pro_load_more_posts() {
    check_ajax_referer('pro_ajax_nonce', 'nonce');

    // Seguridad Crítica: Reconstruir argumentos en el servidor
    $paged = isset($_POST['page']) ? intval($_POST['page']) + 1 : 1;
    $cat_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    
    $args = array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => get_option('posts_per_page'),
        'paged'          => $paged,
    );
    if ( $cat_id > 0 ) {
        $args['cat'] = $cat_id;
    }

    $query = new WP_Query( $args );

    if( $query->have_posts() ) :
        while( $query->have_posts() ): $query->the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('card-post'); ?>>
                <a href="<?php the_permalink(); ?>" class="post-thumbnail" aria-hidden="true" tabindex="-1">
                    <?php the_post_thumbnail( 'card-thumbnail', array( 'loading' => 'lazy' ) ); ?>
                </a>
                <div class="card-content">
                    <div class="post-meta">
                        <?php pro_post_categories(); ?>
                        <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                    </div>
                    <h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
                    <div class="entry-excerpt">
                        <?php echo wp_trim_words( get_the_excerpt(), 20, '...' ); ?>
                    </div>
                </div>
            </article>
            <?php
        endwhile;
    endif;
    wp_die();
}
add_action('wp_ajax_nopriv_pro_load_more_posts', 'pro_load_more_posts');
add_action('wp_ajax_pro_load_more_posts', 'pro_load_more_posts');

/**
 * Polling AJAX: Comprobar si hay nuevas noticias
 */
function pro_check_new_posts() {
    check_ajax_referer('pro_ajax_nonce', 'nonce');
    
    $latest_date = isset($_POST['latest_date']) ? sanitize_text_field($_POST['latest_date']) : '';
    
    if ( ! empty( $latest_date ) ) {
        $args = array(
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'date_query'     => array(
                array(
                    'after'     => $latest_date,
                    'inclusive' => false,
                ),
            ),
        );
        $query = new WP_Query( $args );
        
        if ( $query->have_posts() ) {
            wp_send_json_success( array( 'has_new_posts' => true ) );
        }
    }
    
    wp_send_json_success( array( 'has_new_posts' => false ) );
}
add_action('wp_ajax_nopriv_pro_check_new_posts', 'pro_check_new_posts');
add_action('wp_ajax_pro_check_new_posts', 'pro_check_new_posts');

/**
 * Función Helper: Renderizar categorías del post
 */
function pro_post_categories( $post_id = null, $force_category_slug = null ) {
    $categories = get_the_category( $post_id );
    if ( ! empty( $categories ) ) {
        $primary_category = null;
        $current_cat_id = 0;

        // Intentar detectar si estamos viendo una categoría específica
        if ( wp_doing_ajax() && isset($_POST['category_id']) && intval($_POST['category_id']) > 0 ) {
            $current_cat_id = intval($_POST['category_id']);
        } elseif ( is_category() ) {
            $current_cat_id = get_queried_object_id();
        } elseif ( is_page_template( 'page-categoria.php' ) ) {
            $page_title = get_the_title( get_queried_object_id() );
            $category_obj = get_term_by( 'name', $page_title, 'category' );
            if ( $category_obj ) {
                $current_cat_id = $category_obj->term_id;
            }
        }

        // 0. Si se pide forzar una etiqueta mediante código (ej. Hero de Inicio)
        if ( $force_category_slug ) {
            foreach ( $categories as $category ) {
                if ( $category->slug === $force_category_slug ) {
                    $primary_category = $category;
                    break;
                }
            }
        }

        // 1. Si estamos en una categoría, forzar que la etiqueta muestre esa misma categoría
        if ( ! $primary_category && $current_cat_id > 0 ) {
            foreach ( $categories as $category ) {
                if ( $category->term_id === $current_cat_id ) {
                    $primary_category = $category;
                    break;
                }
            }
        }

        // 2. Fallback: evitar 'relevantes' y tomar la principal
        if ( ! $primary_category ) {
            $primary_category = $categories[0];
            foreach ( $categories as $category ) {
                if ( $category->slug !== 'relevantes' ) {
                    $primary_category = $category;
                    break;
                }
            }
        }

        echo '<span class="cat-label cat-' . esc_attr( $primary_category->slug ) . '">';
        echo '<a href="' . esc_url( get_category_link( $primary_category->term_id ) ) . '">';
        echo esc_html( $primary_category->name );
        echo '</a></span> ';
    }
}

/**
 * Personalizador (Customizer)
 */
function pro_customize_register( $wp_customize ) {
    // Sección de Redes Sociales
    $wp_customize->add_section( 'pro_social_settings', array(
        'title'    => esc_html__( 'Redes Sociales', 'pro' ),
        'priority' => 30,
    ) );
    
    // Facebook
    $wp_customize->add_setting( 'pro_social_facebook', array( 'default' => '#', 'sanitize_callback' => 'esc_url_raw' ) );
    $wp_customize->add_control( 'pro_social_facebook', array(
        'label'    => esc_html__( 'URL de Facebook', 'pro' ),
        'section'  => 'pro_social_settings',
        'type'     => 'url',
    ) );
    // Twitter
    $wp_customize->add_setting( 'pro_social_twitter', array( 'default' => '#', 'sanitize_callback' => 'esc_url_raw' ) );
    $wp_customize->add_control( 'pro_social_twitter', array(
        'label'    => esc_html__( 'URL de Twitter / X', 'pro' ),
        'section'  => 'pro_social_settings',
        'type'     => 'url',
    ) );
    // Instagram
    $wp_customize->add_setting( 'pro_social_instagram', array( 'default' => '#', 'sanitize_callback' => 'esc_url_raw' ) );
    $wp_customize->add_control( 'pro_social_instagram', array(
        'label'    => esc_html__( 'URL de Instagram', 'pro' ),
        'section'  => 'pro_social_settings',
        'type'     => 'url',
    ) );
    // Telegram
    $wp_customize->add_setting( 'pro_social_telegram', array( 'default' => '#', 'sanitize_callback' => 'esc_url_raw' ) );
    $wp_customize->add_control( 'pro_social_telegram', array(
        'label'    => esc_html__( 'URL de Telegram', 'pro' ),
        'section'  => 'pro_social_settings',
        'type'     => 'url',
    ) );

    // Sección de Publicidad
    $wp_customize->add_section( 'pro_ad_settings', array(
        'title'    => esc_html__( 'Publicidad', 'pro' ),
        'priority' => 35,
    ) );
    $wp_customize->add_setting( 'pro_show_ad_placeholders', array( 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean' ) );
    $wp_customize->add_control( 'pro_show_ad_placeholders', array(
        'label'    => esc_html__( 'Mostrar Placeholders vacíos', 'pro' ),
        'description'=> esc_html__( 'Desactívalo en producción si no tienes anuncios insertados en los widgets.', 'pro' ),
        'section'  => 'pro_ad_settings',
        'type'     => 'checkbox',
    ) );
}
add_action( 'customize_register', 'pro_customize_register' );

// Include Ad Manager
require_once get_template_directory() . '/inc/ad-manager.php';

/**
 * Metabox para la Firma del Autor
 */
function pro_add_firma_metabox() {
    add_meta_box(
        'pro_firma_autor_metabox',       // ID
        'Firma',                         // Título
        'pro_firma_autor_metabox_html',  // Callback
        'post',                          // Pantalla (Post Type)
        'side',                          // Contexto
        'default'                        // Prioridad
    );
}
add_action( 'add_meta_boxes', 'pro_add_firma_metabox' );

function pro_firma_autor_metabox_html( $post ) {
    $value = get_post_meta( $post->ID, '_pro_firma_autor', true );
    wp_nonce_field( 'pro_firma_autor_nonce_action', 'pro_firma_autor_nonce' );
    ?>
    <label for="pro_firma_autor_field" style="display:block; margin-bottom:5px;">Nombre del autor para la firma:</label>
    <input type="text" id="pro_firma_autor_field" name="pro_firma_autor_field" value="<?php echo esc_attr( $value ); ?>" style="width:100%;" placeholder="Ej: Juan Pérez" />
    <p style="font-size: 12px; color: #666; margin-top: 5px;">Aparecerá en cursiva debajo del título de la noticia.</p>
    <?php
}

function pro_save_firma_autor_meta( $post_id ) {
    if ( ! isset( $_POST['pro_firma_autor_nonce'] ) || ! wp_verify_nonce( $_POST['pro_firma_autor_nonce'], 'pro_firma_autor_nonce_action' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    if ( isset( $_POST['pro_firma_autor_field'] ) ) {
        update_post_meta( $post_id, '_pro_firma_autor', sanitize_text_field( $_POST['pro_firma_autor_field'] ) );
    }
}
add_action( 'save_post', 'pro_save_firma_autor_meta' );

/**
 * Forzar etiqueta única EO-2026 y ocultar el panel de etiquetas
 */
function pro_enforce_single_tag( $post_id, $post, $update ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( $post->post_type !== 'post' ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    // Sobrescribir cualquier etiqueta con solo 'EO-2026'
    wp_set_post_tags( $post_id, 'EO-2026', false );
}
add_action( 'save_post', 'pro_enforce_single_tag', 99, 3 );

function pro_remove_tags_panel() {
    // Remover del Classic Editor
    remove_meta_box( 'tagsdiv-post_tag', 'post', 'side' );
}
add_action( 'admin_menu', 'pro_remove_tags_panel' );

function pro_hide_gutenberg_tags() {
    // Ocultar del Block Editor (Gutenberg) vía CSS
    echo '<style>
        .components-panel__body.edit-post-meta-boxes-area #tagsdiv-post_tag,
        .edit-post-sidebar .components-panel__body:has(.editor-post-taxonomies__hierarchical-terms-list[aria-label="Etiquetas"]),
        .edit-post-sidebar .components-panel__body:has(.components-form-token-field) {
            display: none !important;
        }
    </style>';
}
add_action( 'admin_head', 'pro_hide_gutenberg_tags' );

/**
 * Instalación "Nuclear" de páginas automáticas
 * Esto creará todas las páginas físicas al cargar el dashboard si no existen.
 */
function pro_nuclear_install_pages() {
    // Solo correr una vez para no saturar la base de datos
    if ( get_option( 'pro_pages_installed_nuclear_v4' ) ) {
        return;
    }

    // Asegurarse de que el usuario es admin
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $pages_to_create = array(
        'Contacto' => 'page-contacto.php',
        'Carteles y Edictos' => 'page-carteles.php',
        'Belleza' => 'page-categoria.php',
        'Bienestar' => 'page-categoria.php',
        'Buen ciudadano' => 'page-categoria.php',
        'Ciencia y Tecnología' => 'page-categoria.php',
        'Deportes' => 'page-categoria.php',
        'Economía' => 'page-categoria.php',
        'Educación' => 'page-categoria.php',
        'Entretenimiento' => 'page-categoria.php',
        'Entrevistas' => 'page-categoria.php',
        'Gastronomía' => 'page-categoria.php',
        'Internacional' => 'page-categoria.php',
        'Local' => 'page-categoria.php',
        'Monagas' => 'page-categoria.php',
        'Municipios' => 'page-categoria.php',
        'Nacional' => 'page-categoria.php',
        'Opinión' => 'page-categoria.php',
        'Política' => 'page-categoria.php',
        'Regiones' => 'page-categoria.php',
        'Relevantes' => 'page-categoria.php',
        'Salud' => 'page-categoria.php',
        'Sucesos' => 'page-categoria.php',
    );

    foreach ( $pages_to_create as $title => $template ) {
        $page_query = new WP_Query( array(
            'post_type'              => 'page',
            'title'                  => $title,
            'post_status'            => 'all',
            'posts_per_page'         => 1,
            'no_found_rows'          => true,
            'ignore_sticky_posts'    => true,
            'update_post_term_cache' => false,
            'update_post_meta_cache' => false,
            'orderby'                => 'post_date',
            'order'                  => 'DESC',
        ) );
        $page_check = ! empty( $page_query->posts ) ? $page_query->posts[0] : null;

        if ( ! $page_check ) {
            $new_page_id = wp_insert_post( array(
                'post_title'     => $title,
                'post_type'      => 'page',
                'post_status'    => 'publish',
                'post_author'    => 1,
            ) );
            if ( $new_page_id && ! is_wp_error( $new_page_id ) ) {
                if ( $template !== 'page.php' ) {
                    update_post_meta( $new_page_id, '_wp_page_template', $template );
                }
            }
        }
    }

    // Configurar Inicio estático
    $home_query = new WP_Query( array(
        'post_type'              => 'page',
        'title'                  => 'Inicio',
        'post_status'            => 'all',
        'posts_per_page'         => 1,
    ) );
    $home_page = ! empty( $home_query->posts ) ? $home_query->posts[0] : null;
    
    if ( ! $home_page ) {
        $home_page_id = wp_insert_post( array(
            'post_title'     => 'Inicio',
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'post_author'    => 1,
        ) );
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $home_page_id );
    } else {
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $home_page->ID );
    }

    // Auto-Crear un menú estructurado y asignarlo
    $menu_name = 'Menú Principal Nuclear';
    $menu_exists = wp_get_nav_menu_object( $menu_name );
    $menu_id = 0;

    if ( ! $menu_exists ) {
        $menu_id = wp_create_nav_menu( $menu_name );

        // Obtener IDs de las páginas
        $all_pages = get_posts( array( 'post_type' => 'page', 'numberposts' => -1 ) );
        $page_map = array();
        foreach ( $all_pages as $p ) {
            $page_map[ $p->post_title ] = $p->ID;
        }

        // Definir estructura principal y submenú
        $main_items = array( 'Inicio', 'Nacional', 'Internacional', 'Sucesos', 'Deportes', 'Economía', 'Política', 'Entretenimiento' );
        $all_titles = array_keys( $pages_to_create );
        $submenu_items = array_diff( $all_titles, $main_items );
        // Remove Contacto from submenu to put it at the end of main
        if ( ( $key = array_search( 'Contacto', $submenu_items ) ) !== false ) {
            unset( $submenu_items[ $key ] );
        }

        // Agregar items principales
        foreach ( $main_items as $item_title ) {
            if ( isset( $page_map[ $item_title ] ) ) {
                wp_update_nav_menu_item( $menu_id, 0, array(
                    'menu-item-title'     => $item_title,
                    'menu-item-object-id' => $page_map[ $item_title ],
                    'menu-item-object'    => 'page',
                    'menu-item-type'      => 'post_type',
                    'menu-item-status'    => 'publish',
                ) );
            }
        }

        // Crear item "Más" (Custom Link)
        $mas_item_id = wp_update_nav_menu_item( $menu_id, 0, array(
            'menu-item-title'  => 'Más',
            'menu-item-url'    => '#',
            'menu-item-status' => 'publish',
        ) );

        // Agregar items al submenú "Más"
        sort( $submenu_items );
        foreach ( $submenu_items as $sub_item ) {
            if ( isset( $page_map[ $sub_item ] ) ) {
                wp_update_nav_menu_item( $menu_id, 0, array(
                    'menu-item-title'     => $sub_item,
                    'menu-item-object-id' => $page_map[ $sub_item ],
                    'menu-item-object'    => 'page',
                    'menu-item-type'      => 'post_type',
                    'menu-item-status'    => 'publish',
                    'menu-item-parent-id' => $mas_item_id,
                ) );
            }
        }

        // Agregar "Contacto" al final
        if ( isset( $page_map['Contacto'] ) ) {
            wp_update_nav_menu_item( $menu_id, 0, array(
                'menu-item-title'     => 'Contacto',
                'menu-item-object-id' => $page_map['Contacto'],
                'menu-item-object'    => 'page',
                'menu-item-type'      => 'post_type',
                'menu-item-status'    => 'publish',
            ) );
        }
    } else {
        $menu_id = $menu_exists->term_id;
    }

    // Asignar forzosamente el menú a la ubicación 'primary'
    if ( $menu_id ) {
        $locations = get_theme_mod( 'nav_menu_locations' );
        if ( ! is_array( $locations ) ) {
            $locations = array();
        }
        $locations['primary'] = $menu_id;
        set_theme_mod( 'nav_menu_locations', $locations );
    }

    update_option( 'pro_pages_installed_nuclear_v4', true );
}
add_action( 'admin_init', 'pro_nuclear_install_pages' );

/**
 * Modificaciones del Menú (Flechas y enlaces)
 */
add_filter('nav_menu_item_title', 'pro_add_dropdown_arrow', 10, 4);
function pro_add_dropdown_arrow($title, $item, $args, $depth) {
    if (in_array('menu-item-has-children', $item->classes)) {
        $title .= ' <span class="dropdown-arrow" style="font-size: 0.8em; margin-left: 4px;">&#9662;</span>'; 
    }
    return $title;
}

add_filter('nav_menu_link_attributes', 'pro_prevent_hash_links', 10, 3);
function pro_prevent_hash_links($atts, $item, $args) {
    if (isset($atts['href']) && $atts['href'] === '#') {
        $atts['href'] = 'javascript:void(0);';
        $atts['style'] = isset($atts['style']) ? $atts['style'] . ' cursor: pointer;' : 'cursor: pointer;';
    }
    return $atts;
}

/**
 * ==============================================================
 * CUSTOM POST TYPE: CARTELES Y EDICTOS
 * ==============================================================
 */

// 1. Registrar el CPT
function pro_register_carteles_cpt() {
    $labels = array(
        'name'                  => 'Carteles',
        'singular_name'         => 'Cartel',
        'menu_name'             => 'Carteles',
        'name_admin_bar'        => 'Cartel',
        'add_new'               => 'Añadir Nuevo',
        'add_new_item'          => 'Añadir Nuevo Cartel',
        'new_item'              => 'Nuevo Cartel',
        'edit_item'             => 'Editar Cartel',
        'view_item'             => 'Ver Cartel',
        'all_items'             => 'Todos los Carteles',
        'search_items'          => 'Buscar Carteles',
        'not_found'             => 'No se encontraron carteles.',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'carteles' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-media-document', // Icono de documento
        'supports'           => array( 'title', 'thumbnail' ), // Solo título e imagen destacada
        'show_in_rest'       => true, // Soporte para Gutenberg si lo desean
    );

    register_post_type( 'cartel', $args );
}
add_action( 'init', 'pro_register_carteles_cpt' );

// 2. Añadir Meta Box para subir PDF
function pro_add_cartel_pdf_metabox() {
    add_meta_box(
        'pro_cartel_pdf',
        'Documento PDF del Cartel',
        'pro_render_cartel_pdf_metabox',
        'cartel',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'pro_add_cartel_pdf_metabox' );

function pro_render_cartel_pdf_metabox( $post ) {
    // Añadir wp_nonce para seguridad
    wp_nonce_field( 'pro_save_cartel_pdf_data', 'pro_cartel_pdf_meta_box_nonce' );

    // Obtener valor actual
    $value = get_post_meta( $post->ID, '_cartel_pdf_url', true );

    echo '<label for="pro_cartel_pdf_url">URL del archivo PDF: </label>';
    echo '<input type="url" id="pro_cartel_pdf_url" name="pro_cartel_pdf_url" value="' . esc_attr( $value ) . '" style="width:100%; margin-top:5px;" placeholder="https://..." />';
    echo '<p class="description">Pega aquí el enlace directo al archivo PDF. (Puedes subir el PDF a "Medios" y copiar su enlace aquí).</p>';
}

// 3. Guardar el valor del Meta Box
function pro_save_cartel_pdf_data( $post_id ) {
    if ( ! isset( $_POST['pro_cartel_pdf_meta_box_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['pro_cartel_pdf_meta_box_nonce'], 'pro_save_cartel_pdf_data' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( ! isset( $_POST['pro_cartel_pdf_url'] ) ) {
        return;
    }

    $my_data = sanitize_text_field( $_POST['pro_cartel_pdf_url'] );
    update_post_meta( $post_id, '_cartel_pdf_url', $my_data );
}
add_action( 'save_post', 'pro_save_cartel_pdf_data' );

/**
 * ==============================================================
 * SISTEMA DE FORMULARIO DE CONTACTO (CPT, AJAX, EXPORTACIÓN)
 * ==============================================================
 */

// 1. Registrar CPT "Mensajes"
function pro_register_mensajes_cpt() {
    $labels = array(
        'name'               => 'Mensajes',
        'singular_name'      => 'Mensaje',
        'menu_name'          => 'Mensajes',
        'name_admin_bar'     => 'Mensaje',
        'all_items'          => 'Todos los Mensajes',
        'search_items'       => 'Buscar Mensajes',
        'not_found'          => 'No se encontraron mensajes.',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false, // No público en front-end nativo
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 6,
        'menu_icon'          => 'dashicons-email',
        'capability_type'    => 'post',
        'capabilities'       => array(
            'create_posts' => 'do_not_allow', // Solo entra por AJAX (o admin manual, pero ocultamos botón default)
        ),
        'map_meta_cap'       => true,
        'supports'           => array( 'title', 'editor', 'custom-fields' ), // Titulo = Nombre, Editor = Mensaje
    );

    register_post_type( 'mensaje_contacto', $args );
}
add_action( 'init', 'pro_register_mensajes_cpt' );

// 2. Personalizar columnas del listado en el Admin
add_filter( 'manage_mensaje_contacto_posts_columns', 'pro_set_custom_mensaje_columns' );
function pro_set_custom_mensaje_columns( $columns ) {
    unset( $columns['date'] );
    $columns['title'] = 'Remitente (Nombre)';
    $columns['email'] = 'Correo';
    $columns['phone'] = 'Teléfono';
    $columns['depto'] = 'Departamento';
    $columns['date']  = 'Fecha';
    return $columns;
}

add_action( 'manage_mensaje_contacto_posts_custom_column' , 'pro_custom_mensaje_column', 10, 2 );
function pro_custom_mensaje_column( $column, $post_id ) {
    switch ( $column ) {
        case 'email' :
            echo esc_html( get_post_meta( $post_id, '_contacto_email', true ) ); 
            break;
        case 'phone' :
            echo esc_html( get_post_meta( $post_id, '_contacto_phone', true ) ); 
            break;
        case 'depto' :
            echo esc_html( get_post_meta( $post_id, '_contacto_depto', true ) ); 
            break;
    }
}

// 3. Endpoint AJAX para guardar el formulario
add_action( 'wp_ajax_nopriv_pro_submit_contact_form', 'pro_submit_contact_form' );
add_action( 'wp_ajax_pro_submit_contact_form', 'pro_submit_contact_form' );

function pro_submit_contact_form() {
    check_ajax_referer( 'pro_ajax_nonce', 'nonce' );

    $required_fields = ['name', 'email', 'phone', 'address', 'department', 'message'];
    foreach ($required_fields as $field) {
        if ( empty( $_POST[$field] ) ) {
            wp_send_json_error( array( 'message' => 'Por favor, completa todos los campos obligatorios.' ) );
            wp_die();
        }
    }

    $name       = sanitize_text_field( $_POST['name'] );
    $email      = sanitize_email( $_POST['email'] );
    $phone      = sanitize_text_field( $_POST['phone'] );
    $address    = sanitize_text_field( $_POST['address'] );
    $department = sanitize_text_field( $_POST['department'] );
    $message    = sanitize_textarea_field( $_POST['message'] );

    if ( ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => 'El correo electrónico no es válido.' ) );
        wp_die();
    }

    // Seguridad Extrema: Evitar Inyecciones, Enlaces y Código
    $security_check = $name . ' ' . $address . ' ' . $message;
    
    // Bloquear enlaces
    if ( preg_match('/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/', $security_check) || stripos($security_check, 'www.') !== false || stripos($security_check, '.com/') !== false ) {
        wp_send_json_error( array( 'message' => 'Por seguridad, no se permiten enlaces ni URLs en el formulario.' ) );
        wp_die();
    }

    // Bloquear HTML, scripts e inyecciones SQL básicas
    if ( preg_match('/(<|>|\[url|\[link|script|union select|drop table|concat\(|-- )/i', $security_check) ) {
        wp_send_json_error( array( 'message' => 'Se han detectado caracteres especiales o comandos no permitidos en el texto.' ) );
        wp_die();
    }

    $post_data = array(
        'post_title'   => $name,
        'post_content' => $message,
        'post_status'  => 'publish',
        'post_type'    => 'mensaje_contacto',
    );

    $post_id = wp_insert_post( $post_data );

    if ( $post_id ) {
        update_post_meta( $post_id, '_contacto_email', $email );
        update_post_meta( $post_id, '_contacto_phone', $phone );
        update_post_meta( $post_id, '_contacto_address', $address );
        update_post_meta( $post_id, '_contacto_depto', $department );

        wp_send_json_success( array( 'message' => '¡Mensaje enviado con éxito!' ) );
    } else {
        wp_send_json_error( array( 'message' => 'Hubo un error al guardar tu mensaje.' ) );
    }
    wp_die();
}

// 4. Botón de Exportar a CSV en la vista del CPT
add_action('manage_posts_extra_tablenav', 'pro_export_mensajes_button', 20, 1);
function pro_export_mensajes_button($which) {
    global $typenow;
    if ('mensaje_contacto' === $typenow && 'top' === $which) {
        $export_url = add_query_arg(array(
            'action' => 'pro_export_mensajes_csv',
            '_wpnonce' => wp_create_nonce('pro_export_mensajes_csv_nonce')
        ), admin_url('admin-post.php'));
        
        echo '<div class="alignleft actions">';
        echo '<a href="' . esc_url($export_url) . '" class="button button-primary">Descargar Excel/CSV</a>';
        echo '</div>';
    }
}

// 5. Lógica de exportación CSV
add_action('admin_post_pro_export_mensajes_csv', 'pro_export_mensajes_csv_handler');
function pro_export_mensajes_csv_handler() {
    if (!current_user_can('edit_posts')) {
        wp_die('No tienes permiso para hacer esto.');
    }
    check_admin_referer('pro_export_mensajes_csv_nonce');

    $args = array(
        'post_type'      => 'mensaje_contacto',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    );
    $query = new WP_Query($args);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=mensajes_contacto_' . date('Y-m-d') . '.csv');

    $output = fopen('php://output', 'w');
    // Bom UTF-8 para Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Cabeceras
    fputcsv($output, array('Fecha', 'Nombre', 'Correo', 'Teléfono', 'Dirección', 'Departamento', 'Mensaje'));

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            fputcsv($output, array(
                get_the_date('Y-m-d H:i:s'),
                get_the_title(),
                get_post_meta($post_id, '_contacto_email', true),
                get_post_meta($post_id, '_contacto_phone', true),
                get_post_meta($post_id, '_contacto_address', true),
                get_post_meta($post_id, '_contacto_depto', true),
                get_the_content()
            ));
        }
    }
    fclose($output);
    exit();
}

/**
 * Cargar personalizaciones del panel de administración (White-label)
 */
require_once get_template_directory() . '/inc/admin-whitelabel.php';

/**
 * Cargar configuraciones de seguridad (Login URL y prevención)
 */
require_once get_template_directory() . '/inc/security.php';
