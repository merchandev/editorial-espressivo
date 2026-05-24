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

    // Estilos principales (CSS Grid, Flexbox, Base)
    wp_enqueue_style( 'pro-main-style', get_template_directory_uri() . '/assets/css/main.css', array(), time() );

    // Estilos de WordPress (style.css para variables CSS)
    wp_enqueue_style( 'pro-style', get_stylesheet_uri(), array('pro-main-style'), time() );

    // Polyfills para navegadores antiguos (Safari antiguo, IE, etc)
    wp_enqueue_script( 'pro-polyfill', 'https://cdnjs.cloudflare.com/ajax/libs/core-js/3.32.2/minified.js', array(), null, false );
    wp_enqueue_script( 'pro-fetch-polyfill', 'https://cdnjs.cloudflare.com/ajax/libs/fetch/3.6.17/fetch.min.js', array(), null, false );

    // Script principal (Modo oscuro, progreso de lectura)
    wp_enqueue_script( 'pro-main-js', get_template_directory_uri() . '/assets/js/main.js', array('pro-polyfill', 'pro-fetch-polyfill'), '1.0.0', true );

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

    wp_localize_script( 'pro-main-js', 'pro_loadmore_params', array(
        'ajax_url'     => admin_url( 'admin-ajax.php' ),
        'current_page' => get_query_var( 'paged' ) ? get_query_var('paged') : 1,
        'max_page'     => $wp_query->max_num_pages,
        'query_vars'   => json_encode( $wp_query->query_vars ),
        'category_id'  => is_category() ? get_queried_object_id() : 0,
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
function pro_post_categories( $post_id = null ) {
    $categories = get_the_category( $post_id );
    if ( ! empty( $categories ) ) {
        foreach ( $categories as $category ) {
            echo '<span class="cat-label cat-' . esc_attr( $category->slug ) . '">';
            echo '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '">';
            echo esc_html( $category->name );
            echo '</a></span> ';
        }
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

/* ==========================================================================
   Gestor de Publicidad (Ad Manager)
   ========================================================================== */

// 1. Registrar Custom Post Type para Banners
function pro_register_ad_cpt() {
    register_post_type('pro_ad_banner', array(
        'labels'      => array(
            'name'          => 'Publicidad',
            'singular_name' => 'Banner',
            'add_new'       => 'A�adir Nuevo Banner',
            'add_new_item'  => 'A�adir Nuevo Banner',
            'edit_item'     => 'Editar Banner'
        ),
        'public'      => false,
        'show_ui'     => true,
        'show_in_menu'=> true,
        'supports'    => array('title', 'thumbnail'),
        'menu_icon'   => 'dashicons-megaphone',
        'menu_position' => 25,
    ));
}
add_action('init', 'pro_register_ad_cpt');

// 2. A�adir Meta Boxes
function pro_add_ad_meta_boxes() {
    add_meta_box('pro_ad_settings', 'Configuraci�n del Banner', 'pro_ad_meta_box_html', 'pro_ad_banner', 'normal', 'high');
}
add_action('add_meta_boxes', 'pro_add_ad_meta_boxes');

function pro_ad_meta_box_html() {
    wp_nonce_field('pro_save_ad_meta', 'pro_ad_meta_nonce');
    
     = get_post_meta(->ID, '_pro_ad_location', true);
          = get_post_meta(->ID, '_pro_ad_url', true);
        = get_post_meta(->ID, '_pro_ad_start', true);
          = get_post_meta(->ID, '_pro_ad_end', true);
    
    ?>
    <p>
        <label for="pro_ad_location"><strong>Ubicaci�n:</strong></label><br>
        <select name="pro_ad_location" id="pro_ad_location" style="width:100%;">
            <option value="header" <?php selected(, 'header'); ?>>Header (Arriba de todo)</option>
            <option value="in-feed-1" <?php selected(, 'in-feed-1'); ?>>In-Feed 1 (Despu�s de Premium)</option>
            <option value="in-feed-2" <?php selected(, 'in-feed-2'); ?>>In-Feed 2 (Despu�s de Locales)</option>
        </select>
    </p>
    <p>
        <label for="pro_ad_url"><strong>URL de destino (Enlace):</strong></label><br>
        <input type="url" name="pro_ad_url" id="pro_ad_url" value="<?php echo esc_url(); ?>" style="width:100%;">
    </p>
    <p>
        <label for="pro_ad_start"><strong>Fecha/Hora de Inicio:</strong></label><br>
        <input type="datetime-local" name="pro_ad_start" id="pro_ad_start" value="<?php echo esc_attr(); ?>" style="width:100%;">
        <small>D�jalo vac�o para publicar inmediatamente.</small>
    </p>
    <p>
        <label for="pro_ad_end"><strong>Fecha/Hora de Salida (Caducidad):</strong></label><br>
        <input type="datetime-local" name="pro_ad_end" id="pro_ad_end" value="<?php echo esc_attr(); ?>" style="width:100%;">
        <small>D�jalo vac�o para que nunca caduque autom�ticamente.</small>
    </p>
    <p><em>* Nota: La imagen del banner debes subirla en el panel derecho de "Imagen destacada".</em></p>
    <?php
}

// 3. Guardar Meta Data
function pro_save_ad_meta() {
    if (!isset(['pro_ad_meta_nonce']) || !wp_verify_nonce(['pro_ad_meta_nonce'], 'pro_save_ad_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', )) return;

    if (isset(['pro_ad_location'])) update_post_meta(, '_pro_ad_location', sanitize_text_field(['pro_ad_location']));
    if (isset(['pro_ad_url'])) update_post_meta(, '_pro_ad_url', esc_url_raw(['pro_ad_url']));
    if (isset(['pro_ad_start'])) update_post_meta(, '_pro_ad_start', sanitize_text_field(['pro_ad_start']));
    if (isset(['pro_ad_end'])) update_post_meta(, '_pro_ad_end', sanitize_text_field(['pro_ad_end']));
}
add_action('save_post_pro_ad_banner', 'pro_save_ad_meta');

// 4. Funci�n para obtener banners activos por ubicaci�n
function pro_get_active_ads() {
     = array(
        'post_type'      => 'pro_ad_banner',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'meta_query'     => array(
            array(
                'key'   => '_pro_ad_location',
                'value' => ,
            )
        )
    );
     = new WP_Query();
     = array();
     = current_time('Y-m-d\TH:i'); // Formato datetime-local

    if (->have_posts()) {
        while (->have_posts()) {
            ->the_post();
             = get_post_meta(get_the_ID(), '_pro_ad_start', true);
               = get_post_meta(get_the_ID(), '_pro_ad_end', true);
            
            // Validar si es vigente
            if (!empty() &&  < ) continue; // A�n no empieza
            if (!empty() &&  > ) continue;     // Ya caduc�

            if (has_post_thumbnail()) {
                [] = array(
                    'url'   => get_post_meta(get_the_ID(), '_pro_ad_url', true),
                    'image' => get_the_post_thumbnail_url(get_the_ID(), 'full'),
                    'title' => get_the_title()
                );
            }
        }
        wp_reset_postdata();
    }
    return ;
}

