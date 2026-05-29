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
 * Category Sponsor Banner Template Part
 *
 * Muestra un banner de patrocinador en la parte superior de la página
 * con el nombre de la sección/categoría y el texto "Patrocinado por".
 *
 * Argumentos:
 *   'cat_name'   (string) – Nombre de la categoría a mostrar (ej: "Nacional")
 *   'location'   (string) – Ubicación del banner (ej: 'category-top')
 *
 * @package Pro
 */

// Obtener argumentos
$cat_name = isset( $args['cat_name'] ) ? $args['cat_name'] : '';
$location = isset( $args['location'] ) ? $args['location'] : 'category-top';

// Intentar obtener anuncios activos para esta ubicación
$ads = function_exists( 'pro_get_active_ads' ) ? pro_get_active_ads( $location ) : array();

// Si no hay anuncios ni placeholders activos, no mostramos nada
if ( empty( $ads ) ) {
    $show_placeholders = get_theme_mod( 'pro_show_ad_placeholders', true );
    if ( ! $show_placeholders ) {
        return;
    }
}

// Obtener datos del patrocinador (del primer anuncio activo)
$sponsor_name = '';
$sponsor_logo = '';
$sponsor_url  = '#';

if ( ! empty( $ads ) ) {
    // Buscar anuncio con meta de patrocinador
    // Usamos el título del banner como nombre del patrocinador
    $first_ad = $ads[0];
    $sponsor_name = isset( $first_ad['sponsor_name'] ) ? $first_ad['sponsor_name'] : ( isset( $first_ad['title'] ) ? $first_ad['title'] : '' );
    $sponsor_url  = ! empty( $first_ad['url'] ) ? $first_ad['url'] : '#';
    $sponsor_logo = isset( $first_ad['sponsor_logo'] ) ? $first_ad['sponsor_logo'] : '';
}
?>

<!-- BANNER PATROCINADOR: <?php echo esc_html( strtoupper( $location ) ); ?> -->
<div class="category-sponsor-banner">

    <!-- Cabecera: Nombre de sección + "Patrocinado por" -->
    <div class="sponsor-header">
        <?php if ( ! empty( $cat_name ) ) : ?>
            <span class="sponsor-section-name"><?php echo esc_html( strtoupper( $cat_name ) ); ?></span>
            <span class="sponsor-divider"></span>
        <?php endif; ?>
        <span class="sponsor-label">
            <span class="sponsor-label-text">Patrocinado por</span>
            <?php if ( ! empty( $sponsor_name ) ) : ?>
                <?php if ( $sponsor_url !== '#' ) : ?>
                    <a href="<?php echo esc_url( $sponsor_url ); ?>" target="_blank" rel="noopener noreferrer sponsored" class="sponsor-name-link">
                        <?php if ( ! empty( $sponsor_logo ) ) : ?>
                            <img src="<?php echo esc_url( $sponsor_logo ); ?>" alt="<?php echo esc_attr( $sponsor_name ); ?>" class="sponsor-logo">
                        <?php else : ?>
                            <strong class="sponsor-name"><?php echo esc_html( $sponsor_name ); ?></strong>
                        <?php endif; ?>
                    </a>
                <?php else : ?>
                    <?php if ( ! empty( $sponsor_logo ) ) : ?>
                        <img src="<?php echo esc_url( $sponsor_logo ); ?>" alt="<?php echo esc_attr( $sponsor_name ); ?>" class="sponsor-logo">
                    <?php else : ?>
                        <strong class="sponsor-name"><?php echo esc_html( $sponsor_name ); ?></strong>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </span>
    </div>

    <!-- Banner publicitario -->
    <div class="sponsor-banner-content">
        <?php if ( ! empty( $ads ) ) : ?>
            <?php
            $first_ad = $ads[0];
            $ad_url   = ! empty( $first_ad['url'] ) ? esc_url( $first_ad['url'] ) : '#';
            $ad_img   = ! empty( $first_ad['image'] ) ? esc_url( $first_ad['image'] ) : '';
            $ad_title = ! empty( $first_ad['title'] ) ? esc_attr( $first_ad['title'] ) : esc_attr( $cat_name );
            ?>
            <?php if ( $ad_url !== '#' ) : ?>
                <a href="<?php echo $ad_url; ?>" target="_blank" rel="noopener noreferrer sponsored" class="sponsor-banner-link">
                    <img src="<?php echo $ad_img; ?>" alt="<?php echo $ad_title; ?>" class="sponsor-banner-img">
                </a>
            <?php else : ?>
                <img src="<?php echo $ad_img; ?>" alt="<?php echo $ad_title; ?>" class="sponsor-banner-img">
            <?php endif; ?>
        <?php else : ?>
            <!-- Placeholder cuando no hay anuncio publicado -->
            <?php if ( get_theme_mod( 'pro_show_ad_placeholders', true ) ) : ?>
                <div class="sponsor-banner-placeholder">
                    <span class="sponsor-placeholder-icon material-symbols-outlined">campaign</span>
                    <span><?php echo ! empty( $cat_name ) ? esc_html( $cat_name ) . ' — ' : ''; ?>Espacio Publicitario</span>
                    <span class="sponsor-placeholder-size">728 × 90 px</span>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</div><!-- .category-sponsor-banner -->
