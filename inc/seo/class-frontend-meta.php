<?php
namespace SSIVO_SEO\Includes;

class FrontendMeta {
    private $database;

    public function __construct( Database $database ) {
        $this->database = $database;
        // Enganchar en wp_head con prioridad 1 para aparecer arriba
        add_action( 'wp_head', [ $this, 'inject_meta_tags' ], 1 );
        
        // Deshabilitar el título por defecto de WordPress para evitar duplicados
        remove_action( 'wp_head', '_wp_render_title_tag', 1 );
    }

    public function inject_meta_tags() {
        if ( ! is_singular() ) {
            // Lógica por defecto para portadas o archivos (opcional)
            echo "<title>" . get_bloginfo('name') . "</title>\n";
            return;
        }

        global $post;
        $seo_data = $this->database->get_seo_data( $post->ID );

        // Si existen datos en nuestra tabla indexable, los usamos
        if ( $seo_data ) {
            $title = esc_html( $seo_data['meta_title'] ) . ' - ' . get_bloginfo('name');
            $desc  = esc_attr( $seo_data['meta_desc'] );
            
            echo "<title>{$title}</title>\n";
            if ( ! empty( $desc ) ) {
                echo "<meta name=\"description\" content=\"{$desc}\" />\n";
            }
        } else {
            // Fallback en caso de que el post no haya sido procesado por SSIVO-SEO aún
            echo "<title>" . get_the_title() . " - " . get_bloginfo('name') . "</title>\n";
        }
    }
}
