<?php
namespace SSIVO_SEO\Includes;

class AdminPage {
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'register_menu' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
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

        // Recuperar valores actuales
        $default_title = get_option( 'ssivo_seo_default_title', get_bloginfo( 'name' ) );
        $default_image = get_option( 'ssivo_seo_default_image', '' );

        ?>
        <div class="wrap" style="max-width: 800px;">
            <h1 style="font-weight: 700; margin-bottom: 20px;">
                <span class="dashicons dashicons-chart-line" style="font-size: 28px; width: 28px; height: 28px; color: #3b82f6; margin-right: 8px; margin-top: 2px;"></span>
                Panel Principal de SSIVO-SEO
            </h1>
            
            <p>Configura las opciones globales de posicionamiento en buscadores para tu plataforma.</p>

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
        <?php
    }
}
