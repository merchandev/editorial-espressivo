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

    // Estilos principales (CSS Grid, Flexbox, Base)
    wp_enqueue_style( 'pro-main-style', get_template_directory_uri() . '/assets/css/main.css', array(), '1.0.0' );

    // Estilos de WordPress (style.css para variables CSS)
    wp_enqueue_style( 'pro-style', get_stylesheet_uri(), array('pro-main-style'), '1.0.0' );

    // Script principal (Modo oscuro, progreso de lectura)
    wp_enqueue_script( 'pro-main-js', get_template_directory_uri() . '/assets/js/main.js', array(), '1.0.0', true );

    // Script para buscador Ajax
    wp_enqueue_script( 'pro-ajax-search', get_template_directory_uri() . '/assets/js/ajax-search.js', array(), '1.0.0', true );
    
    // Pasar URL de admin-ajax al script
    wp_localize_script( 'pro-ajax-search', 'pro_ajax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'pro_ajax_nonce' )
    ));

    global $wp_query;
    wp_localize_script( 'pro-main-js', 'pro_loadmore_params', array(
        'ajax_url'     => admin_url( 'admin-ajax.php' ),
        'current_page' => get_query_var( 'paged' ) ? get_query_var('paged') : 1,
        'max_page'     => $wp_query->max_num_pages,
        'query_vars'   => json_encode( $wp_query->query_vars ),
        'nonce'        => wp_create_nonce( 'pro_ajax_nonce' )
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
                echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
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
    // Podcast
    register_post_type('podcast', array(
        'labels'      => array('name' => 'Podcasts', 'singular_name' => 'Podcast'),
        'public'      => true,
        'has_archive' => true,
        'supports'    => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon'   => 'dashicons-microphone'
    ));
    register_taxonomy('cat_programa', 'podcast', array(
        'labels'       => array('name' => 'Categorías de Programa', 'singular_name' => 'Categoría de Programa'),
        'hierarchical' => true,
        'show_in_rest' => true
    ));

    // Galería
    register_post_type('galeria', array(
        'labels'      => array('name' => 'Galerías', 'singular_name' => 'Galería'),
        'public'      => true,
        'has_archive' => true,
        'supports'    => array('title', 'editor', 'thumbnail'),
        'menu_icon'   => 'dashicons-format-gallery'
    ));
    // Taxonomía compartida: Municipio
    register_taxonomy('municipio', array('galeria', 'clasificado'), array(
        'labels'       => array('name' => 'Municipios', 'singular_name' => 'Municipio'),
        'hierarchical' => false,
        'show_in_rest' => true
    ));

    // Video
    register_post_type('video', array(
        'labels'      => array('name' => 'Videos', 'singular_name' => 'Video'),
        'public'      => true,
        'has_archive' => true,
        'supports'    => array('title', 'editor', 'thumbnail', 'excerpt'),
        'taxonomies'  => array('category'), // Usa las categorías nativas
        'menu_icon'   => 'dashicons-video-alt3'
    ));

    // Clasificado
    register_post_type('clasificado', array(
        'labels'      => array('name' => 'Clasificados', 'singular_name' => 'Clasificado'),
        'public'      => true,
        'has_archive' => true,
        'supports'    => array('title', 'editor', 'thumbnail'),
        'menu_icon'   => 'dashicons-megaphone'
    ));
    register_taxonomy('tipo_clasificado', 'clasificado', array(
        'labels'       => array('name' => 'Tipos de Clasificado', 'singular_name' => 'Tipo de Clasificado'),
        'hierarchical' => true,
        'show_in_rest' => true
    ));
}
add_action('init', 'pro_register_cpts');

/**
 * Paginación AJAX (Cargar más)
 */
function pro_load_more_posts() {
    check_ajax_referer('pro_ajax_nonce', 'nonce');

    $args = json_decode( stripslashes( sanitize_text_field($_POST['query']) ), true );
    $args['paged'] = intval($_POST['page']) + 1; // Avanzamos página
    $args['post_status'] = 'publish';

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
                        <?php 
                        $category = get_the_category(); 
                        if ( $category[0] ) { 
                            echo '<span class="cat-label cat-' . esc_attr( $category[0]->slug ) . '"><a href="' . esc_url( get_category_link( $category[0]->term_id ) ) . '">' . esc_html( $category[0]->cat_name ) . '</a></span>'; 
                        } 
                        ?>
                    </div>
                    <h3 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
                </div>
            </article>
            <?php
        endwhile;
    endif;
    wp_die();
}
add_action('wp_ajax_nopriv_pro_load_more_posts', 'pro_load_more_posts');
add_action('wp_ajax_pro_load_more_posts', 'pro_load_more_posts');
