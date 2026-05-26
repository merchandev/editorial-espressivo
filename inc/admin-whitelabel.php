<?php
/**
 * Personalización y White-label del Dashboard de WordPress
 * para los roles Direccion y Autor.
 *
 * @package Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Función auxiliar para comprobar si el usuario actual es de los roles especificados
function pro_is_target_role() {
    $user = wp_get_current_user();
    $roles = (array) $user->roles;
    return in_array( 'direccion', $roles ) || in_array( 'author', $roles );
}

/**
 * 1. Reemplazar el logo de WordPress en la barra de administración
 */
function pro_replace_wp_logo_admin_bar( $wp_admin_bar ) {
    if ( pro_is_target_role() ) {
        // Remover el logo original de WP
        $wp_admin_bar->remove_node( 'wp-logo' );
        
        // Añadir el logo personalizado
        $logo_url = get_template_directory_uri() . '/assets/image/logo-dashboard.png';
        $args = array(
            'id'    => 'custom-brand-logo',
            'title' => '<span style="display: flex; align-items: center; height: 100%;"><img src="' . esc_url( $logo_url ) . '" style="height: 20px; object-fit: contain; margin-top: -2px;" alt="Panel de Control"></span>',
            'href'  => admin_url(),
            'meta'  => array( 'class' => 'custom-brand-node' )
        );
        $wp_admin_bar->add_node( $args );
    }
}
// 999 para asegurarnos de que se ejecute al final y podamos remover el nodo original
add_action( 'admin_bar_menu', 'pro_replace_wp_logo_admin_bar', 999 );

/**
 * 2. Cambiar el esquema de color del panel de administración
 */
function pro_force_admin_color_scheme( $color_scheme ) {
    if ( pro_is_target_role() ) {
        return 'modern'; // 'modern' es un tema oscuro y elegante integrado en WP
    }
    return $color_scheme;
}
add_filter( 'get_user_option_admin_color', 'pro_force_admin_color_scheme' );

// Remover el selector de colores del perfil para estos roles
function pro_remove_color_picker() {
    if ( pro_is_target_role() ) {
        global $_wp_admin_css_colors;
        $_wp_admin_css_colors = array(); // Vaciar las opciones de colores disponibles
    }
}
add_action( 'admin_init', 'pro_remove_color_picker' );

/**
 * 3. Eliminar rastros de WordPress en el footer del Dashboard
 */
function pro_custom_admin_footer_text( $text ) {
    if ( pro_is_target_role() ) {
        return 'Panel de Administración Profesional | Desarrollado a medida';
    }
    return $text;
}
add_filter( 'admin_footer_text', 'pro_custom_admin_footer_text' );

function pro_custom_admin_footer_version( $version ) {
    if ( pro_is_target_role() ) {
        return ''; // Elimina la versión de WordPress
    }
    return $version;
}
add_filter( 'update_footer', 'pro_custom_admin_footer_version', 999 );

/**
 * 4. Eliminar widgets de eventos y noticias de WordPress del escritorio
 */
function pro_remove_dashboard_widgets() {
    if ( pro_is_target_role() ) {
        remove_meta_box( 'dashboard_primary', 'dashboard', 'side' ); // Noticias y eventos de WP
        remove_action( 'welcome_panel', 'wp_welcome_panel' ); // Panel de bienvenida genérico de WP
    }
}
add_action( 'wp_dashboard_setup', 'pro_remove_dashboard_widgets' );

/**
 * 5. Reemplazar el texto "Acerca de WordPress" en otras partes del admin
 */
function pro_custom_admin_css() {
    if ( pro_is_target_role() ) {
        echo '<style>
            /* Ocultar el enlace "Acerca de WordPress" en el menú de usuario por si acaso */
            #wp-admin-bar-wp-logo { display: none !important; }
            /* Ocultar la ayuda contextual de WP */
            #contextual-help-link-wrap { display: none !important; }
            /* Ajustar el padding del nuevo logo */
            #wpadminbar .custom-brand-node a { padding: 0 10px !important; }
        </style>';
    }
}
add_action( 'admin_head', 'pro_custom_admin_css' );
add_action( 'wp_head', 'pro_custom_admin_css' ); // También en el frontend por si está la barra

?>
