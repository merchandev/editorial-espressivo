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
 * In-Feed Ad Template Part
 * 
 * Used for displaying rotating ads in feed zones.
 * Requires an argument 'location' (e.g. 'in-feed-1') passed via get_template_part.
 */

// If no arguments were provided, default to in-feed-1
$location = isset($args['location']) ? $args['location'] : 'in-feed-1';
$ads = function_exists('pro_get_active_ads') ? pro_get_active_ads($location) : array();

// Si no hay anuncios publicados
if ( empty($ads) ) {
    // Si los placeholders están desactivados globalmente, o si NO es el primer banner (below-hero), no mostramos nada.
    $show_placeholders = get_theme_mod( 'pro_show_ad_placeholders', true );
    if ( !$show_placeholders || $location !== 'below-hero' ) {
        return;
    }
}
?>

<!-- PUBLICIDAD: <?php echo esc_html(strtoupper($location)); ?> -->
<div class="in-feed-ad-wrapper">
    <h2 class="wapo-section-title" style="margin-bottom: 20px;"><span>Publicidad</span></h2>
    
    <?php if ( !empty($ads) ) : ?>
        <div class="in-feed-ad-slider">
            <?php foreach ($ads as $index => $ad) : 
                $active_class = ($index === 0) ? ' active' : '';
                $url = !empty($ad['url']) ? esc_url($ad['url']) : '#';
            ?>
            <div class="ad-slide<?php echo $active_class; ?>">
                <a href="<?php echo $url; ?>" target="_blank" rel="noopener noreferrer">
                    <img src="<?php echo esc_url($ad['image']); ?>" alt="<?php echo esc_attr($ad['title']); ?>" style="width: 100%; height: auto; border-radius: 4px; display: block;">
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <!-- Placeholder -->
        <?php if ( get_theme_mod( 'pro_show_ad_placeholders', true ) ) : 
            $placeholder_size = !empty($args['size']) ? $args['size'] : '1200x200';
            $dimensions = explode('x', $placeholder_size);
            $w = isset($dimensions[0]) ? intval($dimensions[0]) : 1200;
            $h = isset($dimensions[1]) ? intval($dimensions[1]) : 200;
        ?>
        <div class="in-feed-ad-slider">
            <div class="ad-slide active">
                <div style="width: 100%; height: 100%; background-color: #111827; color: #ffffff; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-family: var(--font-ui, sans-serif); font-size: 1.5rem; font-weight: bold; letter-spacing: 0.5px; border-radius: 4px;">
                    Publicidad <?php echo esc_html($location); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
