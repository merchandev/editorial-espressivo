<?php
/**
 * @author Arturo Merchan | Merchan.Dev | Espressivo Venezuela,C.A
 * 
 * ADVERTENCIA LEGAL:
 * Queda totalmente prohibida su reproduccion, edicion, venta, propaganda, alteracion 
 * o cualquier otra accion que de una u otra forma violente la propiedad intelectual, 
 * material y digital de este proyecto. Esta infraccion esta prohibida y penada por la ley.
 */
/**
 * Gestor de Publicidad (Ad Manager)
 *
 * Registra el CPT para los anuncios, meta boxes y funciones auxiliares.
 *
 * @package Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

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

function pro_enqueue_ad_admin_scripts($hook) {
    global $post;
    if ($hook == 'post-new.php' || $hook == 'post.php') {
        if ($post && 'pro_ad_banner' === $post->post_type) {
            wp_enqueue_media();
        }
    }
}
add_action('admin_enqueue_scripts', 'pro_enqueue_ad_admin_scripts');
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
            <option value="nacional-ad" <?php selected($location, 'nacional-ad'); ?>>Después de Nacional</option>
            <option value="internacional-ad" <?php selected($location, 'internacional-ad'); ?>>Después de Internacional</option>
            <option value="economia-ad" <?php selected($location, 'economia-ad'); ?>>Después de Economía</option>
            <option value="in-feed-1" <?php selected($location, 'in-feed-1'); ?>>Después de Sucesos (In-Feed 1)</option>
            <option value="in-feed-2" <?php selected($location, 'in-feed-2'); ?>>Después de Locales (In-Feed 2)</option>
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
    <hr style="margin:20px 0;">
    <h4>Slides Adicionales (Opcional, hasta 5 en total)</h4>
    <p><em>La "Imagen Destacada" estándar es tu Slide 1. Su enlace es la URL de arriba. A continuación puedes añadir más slides para que roten automáticamente en esta ubicación.</em></p>
    
    <?php for ($i = 2; $i <= 5; $i++) : 
        $img_val = get_post_meta($post->ID, '_pro_ad_image_'.$i, true);
        $url_val = get_post_meta($post->ID, '_pro_ad_url_'.$i, true);
    ?>
    <div style="background:#f9f9f9; padding:15px; margin-bottom:15px; border:1px solid #ccc;">
        <strong>Slide <?php echo esc_html( $i ); ?></strong>
        <p>
            <label>Imagen URL:</label><br>
            <input type="text" name="pro_ad_image_<?php echo $i; ?>" id="pro_ad_image_<?php echo $i; ?>" value="<?php echo esc_attr($img_val); ?>" style="width:70%;">
            <button type="button" class="button pro-upload-btn" data-target="#pro_ad_image_<?php echo $i; ?>">Seleccionar Imagen</button>
        </p>
        <p>
            <label>URL de Destino:</label><br>
            <input type="url" name="pro_ad_url_<?php echo $i; ?>" value="<?php echo esc_attr($url_val); ?>" style="width:100%;">
        </p>
    </div>
    <?php endfor; ?>

    <script>
    jQuery(document).ready(function($) {
        $('.pro-upload-btn').on('click', function(e) {
            e.preventDefault();
            var targetInput = $($(this).data('target'));
            
            var uploader = wp.media({
                title: 'Seleccionar Imagen para Slide',
                button: { text: 'Usar esta imagen' },
                multiple: false
            });
            uploader.on('select', function() {
                var attachment = uploader.state().get('selection').first().toJSON();
                targetInput.val(attachment.url);
            });
            uploader.open();
        });

        var isSubmitting = false;
        
        $('#publish').on('click', function(e) {
            if (isSubmitting) return true; // Allow submit if already confirmed
            
            // Si no estamos publicando (ej. guardando como borrador), no bloqueamos
            if ($(this).attr('id') !== 'publish' && $(this).attr('name') !== 'publish') {
                return true; 
            }
            
            var location = $('#pro_ad_location').val();
            var post_id = $('#post_ID').val() || 0;
            var nonce = $('#pro_ad_meta_nonce').val();
            
            // Only validate if location is selected and we are publishing
            if (location) {
                e.preventDefault(); // Stop form submission
                
                $('#publishing-action .spinner').addClass('is-active');
                $(this).addClass('disabled');
                
                $.post(ajaxurl, {
                    action: 'pro_check_ad_location',
                    location: location,
                    post_id: post_id,
                    nonce: nonce
                }, function(response) {
                    $('#publishing-action .spinner').removeClass('is-active');
                    $('#publish').removeClass('disabled');
                    
                    if (response.success && response.data.conflict) {
                        var msg = "⚠️ ATENCIÓN:\n\nYa hay un banner activo ('" + response.data.title + "') ocupando la ubicación seleccionada.\n\nSi continúas, el banner anterior pasará a ser 'Borrador' y este nuevo ocupará su lugar.\n\n¿Deseas reemplazarlo?";
                        if (confirm(msg)) {
                            isSubmitting = true;
                            $('#publish').click(); // Submit again, bypassed by isSubmitting
                        }
                    } else {
                        isSubmitting = true;
                        $('#publish').click();
                    }
                }).fail(function() {
                    // Failsafe
                    isSubmitting = true;
                    $('#publish').click();
                });
            }
        });
    });
    </script>
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
        update_post_meta($post_id, '_pro_ad_url', esc_url_raw($_POST['pro_ad_url']));
    }
    if (isset($_POST['pro_ad_start'])) {
        update_post_meta($post_id, '_pro_ad_start', sanitize_text_field($_POST['pro_ad_start']));
    }
    if (isset($_POST['pro_ad_end'])) {
        update_post_meta($post_id, '_pro_ad_end', sanitize_text_field($_POST['pro_ad_end']));
    }
    
    // Guardar slides adicionales (2-5)
    for ($i = 2; $i <= 5; $i++) {
        if (isset($_POST['pro_ad_image_'.$i])) {
            update_post_meta($post_id, '_pro_ad_image_'.$i, esc_url_raw($_POST['pro_ad_image_'.$i]));
        }
        if (isset($_POST['pro_ad_url_'.$i])) {
            update_post_meta($post_id, '_pro_ad_url_'.$i, esc_url_raw($_POST['pro_ad_url_'.$i]));
        }
    }
    
    // Auto-reemplazo: Si publicamos este banner, despublicar (pasar a borrador) los otros en esta misma ubicación
    if (get_post_status($post_id) === 'publish' && isset($_POST['pro_ad_location'])) {
        $new_location = sanitize_text_field($_POST['pro_ad_location']);
        $args = array(
            'post_type'    => 'pro_ad_banner',
            'post_status'  => 'publish',
            'post__not_in' => array($post_id),
            'meta_query'   => array(
                array(
                    'key'     => '_pro_ad_location',
                    'value'   => $new_location,
                    'compare' => '='
                )
            )
        );
        $query = new WP_Query($args);
        if ($query->have_posts()) {
            // Eliminar acción temporalmente para evitar loops infinitos
            remove_action('save_post_pro_ad_banner', 'pro_save_ad_meta');
            foreach ($query->posts as $other_post) {
                wp_update_post(array(
                    'ID'          => $other_post->ID,
                    'post_status' => 'draft'
                ));
            }
            add_action('save_post_pro_ad_banner', 'pro_save_ad_meta');
        }
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
            
            if ($is_started && !$is_expired) {
                $base_title = get_the_title();
                $post_id = get_the_ID();
                
                // Slide 1 (Imagen Destacada)
                if (has_post_thumbnail()) {
                    $active_ads[] = array(
                        'title' => $base_title,
                        'image' => get_the_post_thumbnail_url($post_id, 'full'),
                        'url'   => get_post_meta($post_id, '_pro_ad_url', true)
                    );
                }
                
                // Slides 2 a 5
                for ($i = 2; $i <= 5; $i++) {
                    $img = get_post_meta($post_id, '_pro_ad_image_'.$i, true);
                    if (!empty($img)) {
                        $active_ads[] = array(
                            'title' => $base_title . ' - Slide ' . $i,
                            'image' => $img,
                            'url'   => get_post_meta($post_id, '_pro_ad_url_'.$i, true)
                        );
                    }
                }
            }
        }
        wp_reset_postdata();
    }
    
    return $active_ads;
}

// 5. AJAX Checker: Validar si la ubicación ya está ocupada
function pro_check_ad_location_ajax() {
    check_ajax_referer('pro_save_ad_meta', 'nonce');
    $location = isset($_POST['location']) ? sanitize_text_field($_POST['location']) : '';
    $post_id  = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    
    if (!$location) wp_send_json_error();
    
    $args = array(
        'post_type'    => 'pro_ad_banner',
        'post_status'  => 'publish',
        'post__not_in' => array($post_id),
        'meta_query'   => array(
            array(
                'key'     => '_pro_ad_location',
                'value'   => $location,
                'compare' => '='
            )
        )
    );
    $query = new WP_Query($args);
    
    $has_conflict = false;
    $conflict_title = '';
    
    if ($query->have_posts()) {
        // Find if at least one is currently active
        $now = current_time('mysql');
        while ($query->have_posts()) {
            $query->the_post();
            $start = get_post_meta(get_the_ID(), '_pro_ad_start', true);
            $end   = get_post_meta(get_the_ID(), '_pro_ad_end', true);
            
            $is_started = empty($start) || (str_replace('T', ' ', $start) <= $now);
            $is_expired = !empty($end) && (str_replace('T', ' ', $end) <= $now);
            
            if ($is_started && !$is_expired) {
                $has_conflict = true;
                $conflict_title = get_the_title();
                break;
            }
        }
        wp_reset_postdata();
    }
    
    wp_send_json_success(array('conflict' => $has_conflict, 'title' => $conflict_title));
}
add_action('wp_ajax_pro_check_ad_location', 'pro_check_ad_location_ajax');
