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
        // Obtener configuración global
        $suffix = get_option( 'ssivo_seo_default_title', get_bloginfo('name') );
        $suffix_str = ! empty( $suffix ) ? ' | ' . $suffix : '';
        $default_img = get_option( 'ssivo_seo_default_image', '' );

        if ( ! is_singular() ) {
            // Lógica por defecto para portadas o archivos
            echo "<title>" . get_bloginfo('name') . "</title>\n";
            return;
        }

        global $post;
        $seo_data = $this->database->get_seo_data( $post->ID );
        
        // Obtener overrides personalizados del Metabox
        $custom_title = get_post_meta( $post->ID, '_ssivo_seo_custom_title', true );
        $custom_desc  = get_post_meta( $post->ID, '_ssivo_seo_custom_desc', true );

        // Determinar Título Final
        $final_title = '';
        if ( ! empty( $custom_title ) ) {
            $final_title = esc_html( $custom_title );
        } elseif ( $seo_data && ! empty( $seo_data['meta_title'] ) ) {
            $final_title = esc_html( $seo_data['meta_title'] );
        } else {
            $final_title = get_the_title();
        }
        $final_title .= $suffix_str;

        // Determinar Descripción Final
        $final_desc = '';
        if ( ! empty( $custom_desc ) ) {
            $final_desc = esc_attr( $custom_desc );
        } elseif ( $seo_data && ! empty( $seo_data['meta_desc'] ) ) {
            $final_desc = esc_attr( $seo_data['meta_desc'] );
        }

        // Determinar Imagen
        $image_url = $default_img;
        if ( has_post_thumbnail( $post->ID ) ) {
            $image_url = get_the_post_thumbnail_url( $post->ID, 'large' );
        }

        // Imprimir Etiquetas
        echo "<title>{$final_title}</title>\n";
        if ( ! empty( $final_desc ) ) {
            echo "<meta name=\"description\" content=\"{$final_desc}\" />\n";
        }

        // Open Graph
        echo "<meta property=\"og:title\" content=\"{$final_title}\" />\n";
        echo "<meta property=\"og:type\" content=\"article\" />\n";
        echo "<meta property=\"og:url\" content=\"" . get_permalink() . "\" />\n";
        if ( ! empty( $final_desc ) ) {
            echo "<meta property=\"og:description\" content=\"{$final_desc}\" />\n";
        }
        if ( ! empty( $image_url ) ) {
            echo "<meta property=\"og:image\" content=\"" . esc_url( $image_url ) . "\" />\n";
        }
    }
}
