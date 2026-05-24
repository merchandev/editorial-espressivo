<?php
/**
 * Gestor de Publicidad (Ad Manager)
 * 
 * Registra el CPT para los anuncios, meta boxes y funciones auxiliares.
 */

// 1. Registrar Custom Post Type para Publicidad
function pro_register_ad_cpt() {
    register_post_type('pro_ad_banner', array(
        'labels'      => array(
            'name'          => 'Publicidad',
            'singular_name' => 'Banner',
            'add_new'       => 'Añadir Nuevo',
            'add_new_item'  => 'Añadir Nuevo Banner',
            'edit_item'     => 'Editar Banner',
            'all_items'     => 'Todos los Banners',
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


// 2. Añadir Meta Boxes
function pro_add_ad_meta_boxes() {
    add_meta_box('pro_ad_settings', 'Configuración del Banner', 'pro_ad_meta_box_html', 'pro_ad_banner', 'normal', 'high');
}
add_action('add_meta_boxes', 'pro_add_ad_meta_boxes');

function pro_ad_meta_box_html($post) {
    wp_nonce_field('pro_save_ad_meta', 'pro_ad_meta_nonce');
    
    $location = get_post_meta($post->ID, '_pro_ad_location', true);
    $url      = get_post_meta($post->ID, '_pro_ad_url', true);
    $start    = get_post_meta($post->ID, '_pro_ad_start', true);
    $end      = get_post_meta($post->ID, '_pro_ad_end', true);
    
    ?>
    <p>
        <label for="pro_ad_location"><strong>Ubicación:</strong></label><br>
        <select name="pro_ad_location" id="pro_ad_location" style="width:100%;">
            <option value="header" <?php selected($location, 'header'); ?>>Header (Arriba de todo)</option>
            <option value="below-hero" <?php selected($location, 'below-hero'); ?>>Debajo del Hero (Noticias Principales)</option>
            <option value="in-feed-1" <?php selected($location, 'in-feed-1'); ?>>In-Feed 1 (Después de Premium)</option>
            <option value="in-feed-2" <?php selected($location, 'in-feed-2'); ?>>In-Feed 2 (Después de Locales)</option>
        </select>
    </p>
    <p>
        <label for="pro_ad_url"><strong>URL de Destino (Opcional):</strong></label><br>
        <input type="url" name="pro_ad_url" id="pro_ad_url" value="<?php echo esc_url($url); ?>" style="width:100%;">
    </p>
    <p>
        <label for="pro_ad_start"><strong>Fecha y Hora de Inicio:</strong></label><br>
        <input type="datetime-local" name="pro_ad_start" id="pro_ad_start" value="<?php echo esc_attr($start); ?>" style="width:100%;">
        <small>Déjalo vacío para publicar inmediatamente.</small>
    </p>
    <p>
        <label for="pro_ad_end"><strong>Fecha y Hora de Caducidad:</strong></label><br>
        <input type="datetime-local" name="pro_ad_end" id="pro_ad_end" value="<?php echo esc_attr($end); ?>" style="width:100%;">
        <small>Déjalo vacío para que nunca caduque automáticamente.</small>
    </p>
    <?php
}

// 3. Guardar Meta Datos
function pro_save_ad_meta($post_id) {
    if (!isset($_POST['pro_ad_meta_nonce']) || !wp_verify_nonce($_POST['pro_ad_meta_nonce'], 'pro_save_ad_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['pro_ad_location'])) {
        update_post_meta($post_id, '_pro_ad_location', sanitize_text_field($_POST['pro_ad_location']));
    }
    if (isset($_POST['pro_ad_url'])) {
        update_post_meta($post_id, '_pro_ad_url', sanitize_url($_POST['pro_ad_url']));
    }
    if (isset($_POST['pro_ad_start'])) {
        update_post_meta($post_id, '_pro_ad_start', sanitize_text_field($_POST['pro_ad_start']));
    }
    if (isset($_POST['pro_ad_end'])) {
        update_post_meta($post_id, '_pro_ad_end', sanitize_text_field($_POST['pro_ad_end']));
    }
}
add_action('save_post_pro_ad_banner', 'pro_save_ad_meta');

// 4. Helper Function para recuperar anuncios activos
function pro_get_active_ads($location) {
    $now = current_time('mysql');
    
    $args = array(
        'post_type'      => 'pro_ad_banner',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'     => '_pro_ad_location',
                'value'   => $location,
                'compare' => '='
            ),
        ),
    );
    
    $query = new WP_Query($args);
    $active_ads = array();
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            
            $start = get_post_meta(get_the_ID(), '_pro_ad_start', true);
            $end   = get_post_meta(get_the_ID(), '_pro_ad_end', true);
            
            // Validar fechas
            $is_started = empty($start) || (str_replace('T', ' ', $start) <= $now);
            $is_expired = !empty($end) && (str_replace('T', ' ', $end) <= $now);
            
            if ($is_started && !$is_expired && has_post_thumbnail()) {
                $active_ads[] = array(
                    'title' => get_the_title(),
                    'image' => get_the_post_thumbnail_url(get_the_ID(), 'full'),
                    'url'   => get_post_meta(get_the_ID(), '_pro_ad_url', true)
                );
            }
        }
        wp_reset_postdata();
    }
    
    return $active_ads;
}
