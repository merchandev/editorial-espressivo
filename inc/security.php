<?php
/**
 * Ocultar wp-login.php y cambiar la URL de inicio de sesión a /turpial
 */

// 1. Redirigir el wp-login.php por defecto a la página de inicio, a menos que se acceda por /turpial
add_action('init', 'pro_custom_login_redirect');
function pro_custom_login_redirect() {
    $request_uri = $_SERVER['REQUEST_URI'];
    $login_path = '/turpial';

    // Si están accediendo a /turpial, forzamos la carga del login
    if ( strpos($request_uri, $login_path) !== false ) {
        global $pagenow;
        $pagenow = 'wp-login.php';
        $_SERVER['REQUEST_URI'] = '/wp-login.php';
        
        // Evitamos que WP intente redirigir de nuevo a la URL canónica
        remove_action( 'template_redirect', 'wp_redirect_admin_locations', 1000 );
        
        require_once ABSPATH . 'wp-login.php';
        exit;
    }

    // Si acceden a wp-login.php directamente (y no están enviando el formulario de login)
    if ( strpos($request_uri, 'wp-login.php') !== false && $_SERVER['REQUEST_METHOD'] === 'GET' && !is_user_logged_in() && !isset($_GET['action']) ) {
        wp_redirect( home_url() );
        exit;
    }
}

// 2. Reescribir las URLs generadas por WordPress para que apunten a /turpial
add_filter( 'site_url', 'pro_filter_login_url', 10, 4 );
function pro_filter_login_url( $url, $path, $scheme, $blog_id ) {
    if ( $path === 'wp-login.php' || preg_match( '/wp-login\.php\?action=\w+/', $path ) ) {
        return str_replace( 'wp-login.php', 'turpial', $url );
    }
    return $url;
}

add_filter( 'network_site_url', 'pro_filter_login_url', 10, 3 );
add_filter( 'wp_redirect', 'pro_filter_login_redirect', 10, 2 );

function pro_filter_login_redirect( $location, $status ) {
    if ( strpos( $location, 'wp-login.php' ) !== false ) {
        $location = str_replace( 'wp-login.php', 'turpial', $location );
    }
    return $location;
}

/**
 * 3. Cambiar el logo de la pantalla de inicio de sesión (/turpial)
 */
function pro_custom_login_logo() {
    $logo_url = get_template_directory_uri() . '/assets/image/logo-dashboard.png';
    ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url('<?php echo esc_url($logo_url); ?>');
            height: 80px;
            width: 100%;
            background-size: contain;
            background-repeat: no-repeat;
            padding-bottom: 20px;
        }
        /* Color del botón de login para mantener el tema */
        .login .button-primary {
            background: #0f172a !important;
            border-color: #0f172a !important;
            color: #fff !important;
        }
        .login .button-primary:hover {
            background: #1e293b !important;
            border-color: #1e293b !important;
        }
    </style>
    <?php
}
add_action( 'login_enqueue_scripts', 'pro_custom_login_logo' );

function pro_custom_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'pro_custom_login_logo_url' );

function pro_custom_login_logo_url_title() {
    return get_bloginfo( 'name' );
}
add_filter( 'login_headertext', 'pro_custom_login_logo_url_title' );

?>
