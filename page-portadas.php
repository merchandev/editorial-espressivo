<?php
/**
 * Template Name: Página de Portadas de Revista
 *
 * Plantilla de página para mostrar las portadas de revista (CPT portada).
 * Incluye un visor lightbox interactivo con soporte de zoom, arrastre (pan) y descarga directa.
 *
 * @package Edit-Pro
 */

get_header();
?>

<main id="primary" class="site-main container archive-container">

    <header class="page-header">
        <h1 class="page-title"><?php the_title(); ?></h1>
        <?php if ( get_the_content() ) : ?>
            <div class="page-description">
                <?php the_content(); ?>
            </div>
        <?php endif; ?>
    </header><!-- .page-header -->

    <?php 
    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
    $portadas_query = new WP_Query( array(
        'post_type'      => 'portada',
        'posts_per_page' => 12,
        'paged'          => $paged
    ) );
    
    if ( $portadas_query->have_posts() ) : ?>
        <div class="portadas-grid">
            <?php
            while ( $portadas_query->have_posts() ) :
                $portadas_query->the_post();
                
                // Obtener URL de la imagen destacada en tamaño completo
                $full_image_url = '';
                if ( has_post_thumbnail() ) {
                    $full_image_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
                }
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('card-portada'); ?> data-full-url="<?php echo esc_url($full_image_url); ?>">
                    <div class="portada-thumbnail">
                        <?php 
                        if ( has_post_thumbnail() ) {
                            the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) );
                        } else {
                            echo '<div class="placeholder-portada"><span class="material-symbols-outlined">image</span></div>';
                        }
                        ?>
                        <div class="portada-overlay">
                            <span class="material-symbols-outlined portada-icon">zoom_in</span>
                            <span>Ampliar Portada</span>
                        </div>
                    </div>
                    <div class="portada-content">
                        <div class="post-meta">
                            <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                        </div>
                        <h2 class="entry-title"><?php the_title(); ?></h2>
                    </div>
                </article>
                <?php
            endwhile;
            ?>
        </div>

        <?php
        // Paginación personalizada para WP_Query
        $total_pages = $portadas_query->max_num_pages;
        if ($total_pages > 1) {
            $current_page = max(1, get_query_var('paged'));
            echo '<nav class="navigation pagination" aria-label="Entradas"><div class="nav-links">';
            echo paginate_links(array(
                'base' => get_pagenum_link(1) . '%_%',
                'format' => 'page/%#%/',
                'current' => $current_page,
                'total' => $total_pages,
                'prev_text' => '&larr; Anteriores',
                'next_text' => 'Siguientes &rarr;',
            ));
            echo '</div></nav>';
        }
        
        wp_reset_postdata();
        ?>

    <?php else : ?>
        <section class="no-results not-found">
            <div class="page-content">
                <p><?php esc_html_e( 'Actualmente no hay portadas de revista publicadas.', 'pro' ); ?></p>
            </div>
        </section>
    <?php endif; ?>

</main><!-- #primary -->

<!-- Lightbox Modal Interactivo de Portadas -->
<div id="portada-lightbox-modal" class="portada-modal" aria-hidden="true" role="dialog">
    <div class="portada-modal-backdrop"></div>
    
    <!-- Controles superiores e información -->
    <div class="portada-modal-bar">
        <h3 id="portada-modal-title">Visor de Portada</h3>
        <div class="portada-modal-actions">
            <!-- Botón de Descarga Directa -->
            <a id="portada-download-btn" href="" download class="portada-action-btn" title="Descargar Portada">
                <span class="material-symbols-outlined">download</span>
                <span class="btn-text">Descargar</span>
            </a>
            <!-- Botón de Cerrar -->
            <button class="portada-modal-close" aria-label="Cerrar visor" title="Cerrar">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
    </div>

    <!-- Contenedor principal interactivo -->
    <div class="portada-modal-body">
        <div class="portada-viewport" id="portada-viewport">
            <img id="portada-lightbox-image" src="" alt="Portada" draggable="false">
        </div>
    </div>

    <!-- Controles flotantes inferiores de Zoom -->
    <div class="portada-zoom-controls">
        <button id="portada-zoom-out" class="zoom-btn" title="Alejar (Zoom -)">
            <span class="material-symbols-outlined">zoom_out</span>
        </button>
        <button id="portada-zoom-reset" class="zoom-btn text-btn" title="Restablecer">
            100%
        </button>
        <button id="portada-zoom-in" class="zoom-btn" title="Acercar (Zoom +)">
            <span class="material-symbols-outlined">zoom_in</span>
        </button>
    </div>
</div>

<?php
get_footer();
