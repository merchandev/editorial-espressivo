<?php
namespace SSIVO_SEO\Includes;

class AdminPage {
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'register_menu' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'wp_ajax_ssivo_seo_optimize', [ $this, 'ajax_optimize' ] );
    }

    public function ajax_optimize() {
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error();

        global $wpdb;
        $table_name = Database::get_table_name();
        
        $posts = $wpdb->get_results( "
            SELECT ID, post_title, post_content
            FROM {$wpdb->posts} p
            LEFT JOIN {$table_name} m ON p.ID = m.post_id
            WHERE p.post_status = 'publish' AND p.post_type = 'post' AND m.post_id IS NULL
            LIMIT 50
        " );

        if ( empty( $posts ) ) {
            wp_send_json_success(['done' => true]);
        }

        $db = new Database();
        foreach ( $posts as $p ) {
            $content = !empty($p->post_content) ? $p->post_content : '';
            $text = strip_shortcodes( $content );
            $text = wp_strip_all_tags( $text );
            $text = preg_replace( '/\s+/', ' ', $text );
            $text = trim( $text );
            
            if ( function_exists('mb_strlen') && mb_strlen( $text, 'UTF-8' ) > 155 ) {
                $desc = mb_strimwidth( $text, 0, 155, '...', 'UTF-8' );
            } else {
                $desc = substr( $text, 0, 155 );
            }

            $db->save_seo_data( $p->ID, [
                'meta_title' => !empty($p->post_title) ? $p->post_title : 'Sin Titulo',
                'meta_desc'  => $desc
            ]);
        }

        wp_send_json_success(['done' => false]);
    }

    public function register_menu() {
        add_menu_page(
            'Ajustes SSIVO-SEO',
            'SEO',
            'manage_options',
            'ssivo-seo',
            [ $this, 'render_page' ],
            'dashicons-chart-line',
            80
        );
    }

    public function register_settings() {
        register_setting( 'ssivo_seo_group', 'ssivo_seo_default_title' );
        register_setting( 'ssivo_seo_group', 'ssivo_seo_default_image' );
    }

    public function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        global $wpdb;

        // Recuperar valores actuales
        $default_title = get_option( 'ssivo_seo_default_title', get_bloginfo( 'name' ) );
        $default_image = get_option( 'ssivo_seo_default_image', '' );

        // --- ESTADÍSTICAS DEL DASHBOARD ---

        // 1. Total de publicaciones
        $total_posts = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = 'post'" );
        $total_posts = $total_posts ? intval( $total_posts ) : 0;

        // 2. Salud SEO (Porcentaje)
        $table_name = Database::get_table_name();
        $seo_meta_count = $wpdb->get_var( "SELECT COUNT(DISTINCT post_id) FROM {$table_name}" );
        $seo_percentage = 100;
        if ( $total_posts > 0 ) {
            $seo_percentage = round( ( $seo_meta_count / $total_posts ) * 100 );
            if ( $seo_percentage > 100 ) $seo_percentage = 100;
        }
        
        // Color de la salud SEO
        $health_color = $seo_percentage >= 97 ? '#10b981' : ( $seo_percentage >= 80 ? '#f59e0b' : '#ef4444' );

        // 3. Actividad de Autores
        $authors = $wpdb->get_results( "
            SELECT u.display_name, COUNT(p.ID) as post_count 
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->users} u ON p.post_author = u.ID
            WHERE p.post_status = 'publish' AND p.post_type = 'post'
            GROUP BY p.post_author
            ORDER BY post_count DESC
            LIMIT 5
        " );

        ?>
        <div class="wrap" style="max-width: 900px;">
            <h1 style="font-weight: 700; margin-bottom: 20px;">
                <span class="dashicons dashicons-chart-line" style="font-size: 28px; width: 28px; height: 28px; color: #3b82f6; margin-right: 8px; margin-top: 2px;"></span>
                Panel Principal de SSIVO-SEO
            </h1>
            
            <p style="font-size: 14px; color: #475569; margin-bottom: 30px;">
                Resumen de salud y opciones globales de posicionamiento en buscadores para tu plataforma.
            </p>

            <!-- DASHBOARD WIDGETS -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
                
                <!-- Widget: Salud del Sitio -->
                <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; border-top: 4px solid <?php echo $health_color; ?>;">
                    <h3 style="margin-top: 0; color: #1e293b; font-size: 15px; margin-bottom: 15px;">Salud SEO del Sitio</h3>
                    <div style="display: flex; align-items: flex-end; gap: 10px;">
                        <span id="seo-score-display" style="font-size: 36px; font-weight: 800; color: <?php echo $health_color; ?>; line-height: 1;"><?php echo $seo_percentage; ?>%</span>
                    </div>
                    <p style="margin: 10px 0 15px 0; font-size: 13px; color: #64748b; min-height: 38px;">
                        <?php if ( $seo_percentage >= 97 ) : ?>
                            ¡Excelente! Tu sitio mantiene el estándar requerido por encima del 97%.
                        <?php else : ?>
                            Atención: La salud SEO está por debajo del 97%. Optimiza artículos antiguos.
                        <?php endif; ?>
                    </p>
                    <?php if ( $seo_percentage < 100 ) : ?>
                        <button id="btn-optimize-seo" class="button button-primary" style="width: 100%; text-align: center; justify-content: center; display: flex; align-items: center; gap: 5px;">
                            <span class="dashicons dashicons-update-alt" id="optimize-spinner" style="display:none; animation: rotation 2s infinite linear;"></span>
                            Mejorar Salud SEO
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Widget: Publicaciones -->
                <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; border-top: 4px solid #3b82f6;">
                    <h3 style="margin-top: 0; color: #1e293b; font-size: 15px; margin-bottom: 15px;">Total Publicaciones</h3>
                    <div style="display: flex; align-items: flex-end; gap: 10px;">
                        <span style="font-size: 36px; font-weight: 800; color: #1e293b; line-height: 1;"><?php echo number_format_i18n( $total_posts ); ?></span>
                        <span style="font-size: 14px; color: #64748b; margin-bottom: 5px;">artículos activos</span>
                    </div>
                    <p style="margin: 10px 0 0 0; font-size: 13px; color: #64748b;">
                        Artículos indexados y publicados en el sistema.
                    </p>
                </div>

                <!-- Widget: Actividad de Usuarios -->
                <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; border-top: 4px solid #8b5cf6;">
                    <h3 style="margin-top: 0; color: #1e293b; font-size: 15px; margin-bottom: 15px;">Actividad de Autores</h3>
                    <ul style="margin: 0; padding: 0; list-style: none;">
                        <?php if ( $authors ) : ?>
                            <?php foreach ( $authors as $author ) : ?>
                                <li style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px;">
                                    <span style="color: #334155; font-weight: 500;"><?php echo esc_html( $author->display_name ); ?></span>
                                    <span style="color: #64748b; background: #f1f5f9; padding: 2px 8px; border-radius: 12px; font-size: 11px;"><?php echo number_format_i18n( $author->post_count ); ?> posts</span>
                                </li>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <li style="font-size: 13px; color: #64748b;">Sin actividad registrada.</li>
                        <?php endif; ?>
                    </ul>
                </div>

            </div>

            <!-- FORMULARIO DE AJUSTES -->
            <h2 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 15px;">Ajustes Globales</h2>
            <form method="post" action="options.php" style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0;">
                <?php
                settings_fields( 'ssivo_seo_group' );
                do_settings_sections( 'ssivo_seo_group' );
                ?>
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="ssivo_seo_default_title" style="font-weight: 600;">Sufijo del Título Global</label></th>
                            <td>
                                <input name="ssivo_seo_default_title" type="text" id="ssivo_seo_default_title" value="<?php echo esc_attr( $default_title ); ?>" class="regular-text" />
                                <p class="description">Este texto se añadirá automáticamente al final de tus títulos. (Ej. " | Espressivo Editorial")</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ssivo_seo_default_image" style="font-weight: 600;">URL Imagen Destacada por Defecto</label></th>
                            <td>
                                <input name="ssivo_seo_default_image" type="url" id="ssivo_seo_default_image" value="<?php echo esc_url( $default_image ); ?>" class="regular-text large-text" />
                                <p class="description">Imagen que se usará al compartir en Facebook/Twitter si el artículo no tiene imagen destacada propia.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;" />
                <?php submit_button( 'Guardar Cambios SEO', 'primary', 'submit', false ); ?>
            </form>
        </div>

        <style>
            @keyframes rotation { from { transform: rotate(0deg); } to { transform: rotate(359deg); } }
        </style>
        <script>
        jQuery(document).ready(function($) {
            $('#btn-optimize-seo').on('click', function(e) {
                e.preventDefault();
                var btn = $(this);
                var spinner = $('#optimize-spinner');
                
                if (btn.prop('disabled')) return;
                
                btn.prop('disabled', true);
                spinner.show();
                btn.html(spinner.prop('outerHTML') + ' Procesando lote...');
                
                function processBatch() {
                    $.post(ajaxurl, { action: 'ssivo_seo_optimize' }, function(response) {
                        if (response.success && response.data.done) {
                            $('#seo-score-display').text('100%').css('color', '#10b981');
                            btn.html('¡Salud al 100%!');
                            btn.removeClass('button-primary').css({'background':'#10b981', 'color':'#fff', 'border-color':'#10b981'});
                            setTimeout(function(){ location.reload(); }, 1500);
                        } else if (response.success && !response.data.done) {
                            btn.html(spinner.prop('outerHTML') + ' Optimizando siguientes 50...');
                            processBatch();
                        } else {
                            btn.html('Error. Reintentar');
                            btn.prop('disabled', false);
                            spinner.hide();
                        }
                    }).fail(function() {
                        btn.html('Error de conexión. Reintentar');
                        btn.prop('disabled', false);
                        spinner.hide();
                    });
                }
                
                processBatch();
            });
        });
        </script>
        <?php
    }
}
