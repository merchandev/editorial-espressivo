<?php
/**
 * In-Feed Ad Template Part
 * 
 * Used for displaying rotating ads in feed zones.
 * Requires an argument 'location' (e.g. 'in-feed-1') passed via get_template_part.
 */

// If no arguments were provided, default to in-feed-1
$location = isset($args['location']) ? $args['location'] : 'in-feed-1';
$ads = function_exists('pro_get_active_ads') ? pro_get_active_ads($location) : array();
?>

<!-- PUBLICIDAD: <?php echo esc_html(strtoupper($location)); ?> -->
<div class="in-feed-ad-wrapper">
    <div class="ad-label-tag"><span>Publicidad</span></div>
    
    <?php if ( !empty($ads) ) : ?>
        <div class="in-feed-ad-slider">
            <?php foreach ($ads as $index => $ad) : 
                $active_class = ($index === 0) ? ' active' : '';
                $url = !empty($ad['url']) ? esc_url($ad['url']) : '#';
            ?>
            <div class="ad-slide<?php echo $active_class; ?>">
                <a href="<?php echo $url; ?>" target="_blank" rel="noopener noreferrer">
                    <img src="<?php echo esc_url($ad['image']); ?>" alt="<?php echo esc_attr($ad['title']); ?>">
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <!-- Placeholder -->
        <?php if ( get_theme_mod( 'pro_show_ad_placeholders', true ) ) : ?>
        <div class="in-feed-ad-slider">
            <div class="ad-slide active">
                <img src="https://via.placeholder.com/728x90/111827/FFFFFF?text=Publicidad+<?php echo esc_attr($location); ?>" alt="Ad Placeholder">
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
