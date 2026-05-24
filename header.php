<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <!-- PWA Theme Color -->
    <meta name="theme-color" content="#ffffff">

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Saltar al contenido principal', 'pro' ); ?></a>

    <header id="masthead" class="site-header">
        <div class="header-topbar">
            <div class="container topbar-inner">
                <div class="date-display">
                    <?php echo date_i18n( 'l, j \d\e F \d\e Y' ); ?>
                </div>
                <nav id="topbar-navigation" class="topbar-navigation">
                    <?php 
                    wp_nav_menu( array(
                        'theme_location' => 'topbar',
                        'menu_id'        => 'topbar-menu',
                        'container'      => false,
                        'fallback_cb'    => false,
                    ) );
                    ?>
                    <button id="dark-mode-toggle" class="dark-mode-toggle" aria-label="Cambiar modo oscuro" title="Modo Oscuro">
                        <span class="material-symbols-outlined">dark_mode</span>
                    </button>
                </nav><!-- #topbar-navigation -->
            </div>
        </div>

        <div class="header-main container">
            <div class="site-branding">
                <?php
                if ( has_custom_logo() ) {
                    the_custom_logo();
                } else {
                    ?>
                    <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                    <?php
                    $pro_description = get_bloginfo( 'description', 'display' );
                    if ( $pro_description || is_customize_preview() ) :
                        ?>
                        <p class="site-description"><?php echo $pro_description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                    <?php endif;
                }
                ?>
            </div><!-- .site-branding -->

            <div class="header-ad">
                <?php if ( is_active_sidebar( 'ad-header' ) ) : ?>
                    <?php dynamic_sidebar( 'ad-header' ); ?>
                <?php else : ?>
                    <?php if ( get_theme_mod( 'pro_show_ad_placeholders', true ) ) : ?>
                        <div class="ad-placeholder">
                            <span>Espacio Publicitario (728x90)</span>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div><!-- .header-main -->

        <div class="header-navigation-wrapper">
            <div class="container navigation-inner">
                <nav id="site-navigation" class="main-navigation">
                    <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><span class="material-symbols-outlined" style="vertical-align: middle;">menu</span> <?php esc_html_e( 'Menú', 'pro' ); ?></button>
                    <?php
                    wp_nav_menu( array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                    ) );
                    ?>
                </nav><!-- #site-navigation -->

                <div class="header-actions">
                    <div class="search-container">
                        <form role="search" method="get" class="search-form ajax-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                            <label>
                                <span class="screen-reader-text"><?php echo _x( 'Buscar:', 'label', 'pro' ); ?></span>
                                <input type="search" class="search-field ajax-search-input" placeholder="<?php echo esc_attr_x( 'Buscar noticias...', 'placeholder', 'pro' ); ?>" value="<?php echo get_search_query(); ?>" name="s" autocomplete="off" />
                            </label>
                            <button type="submit" class="search-submit" aria-label="Buscar">
                                <span class="material-symbols-outlined">search</span>
                            </button>
                        </form>
                        <div class="ajax-search-results-container"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if ( is_single() ) : ?>
            <div id="reading-progress-container" class="reading-progress-container">
                <div id="reading-progress-bar" class="reading-progress-bar"></div>
            </div>
        <?php endif; ?>
    </header><!-- #masthead -->
