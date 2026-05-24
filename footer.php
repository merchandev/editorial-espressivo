    <footer id="colophon" class="site-footer">
        <div class="footer-widgets container">
            <div class="footer-grid">
                <div class="footer-branding">
                    <?php if ( has_custom_logo() ) : ?>
                        <div class="footer-logo">
                            <?php the_custom_logo(); ?>
                        </div>
                    <?php else: ?>
                        <h2 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h2>
                    <?php endif; ?>
                    <p class="footer-description">
                        Diario digital independiente, enfocado en llevar las noticias más importantes de Venezuela y el mundo.
                    </p>
                    <div class="footer-social">
                        <?php if ( get_theme_mod('pro_social_facebook', '#') !== '#' && get_theme_mod('pro_social_facebook') !== '' ) : ?>
                            <a href="<?php echo esc_url( get_theme_mod('pro_social_facebook') ); ?>" target="_blank" rel="noopener noreferrer">Facebook</a>
                        <?php endif; ?>
                        
                        <?php if ( get_theme_mod('pro_social_twitter', '#') !== '#' && get_theme_mod('pro_social_twitter') !== '' ) : ?>
                            <a href="<?php echo esc_url( get_theme_mod('pro_social_twitter') ); ?>" target="_blank" rel="noopener noreferrer">Twitter</a>
                        <?php endif; ?>
                        
                        <?php if ( get_theme_mod('pro_social_instagram', '#') !== '#' && get_theme_mod('pro_social_instagram') !== '' ) : ?>
                            <a href="<?php echo esc_url( get_theme_mod('pro_social_instagram') ); ?>" target="_blank" rel="noopener noreferrer">Instagram</a>
                        <?php endif; ?>
                        
                        <?php if ( get_theme_mod('pro_social_telegram', '#') !== '#' && get_theme_mod('pro_social_telegram') !== '' ) : ?>
                            <a href="<?php echo esc_url( get_theme_mod('pro_social_telegram') ); ?>" target="_blank" rel="noopener noreferrer">Telegram</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="footer-navigation">
                    <h3>Categorías</h3>
                    <ul class="footer-category-list">
                        <?php
                        $categories = get_categories( array(
                            'orderby' => 'name',
                            'order'   => 'ASC',
                            'hide_empty' => true,
                        ) );
                        
                        if ( $categories ) {
                            foreach ( $categories as $category ) {
                                echo '<li><a href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a></li>';
                            }
                        } else {
                            echo '<li>Sin categorías</li>';
                        }
                        ?>
                    </ul>
                </div>

                <div class="footer-contact">
                    <h3>Contacto</h3>
                    <p><span class="material-symbols-outlined" style="vertical-align: middle; font-size: 1.2rem; margin-right: 5px;">mail</span> redaccion@pro.com</p>
                    <p><span class="material-symbols-outlined" style="vertical-align: middle; font-size: 1.2rem; margin-right: 5px;">location_on</span> Maturín, Venezuela.</p>
                    
                    <br>
                    <a href="#" class="btn-primary" style="display: inline-flex; align-items: center; gap: 8px;"><span class="material-symbols-outlined">report</span> Envía tu denuncia</a>
                </div>
            </div>
        </div><!-- .footer-widgets -->

        <div class="site-info container">
            <div class="site-info-inner">
                <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. Todos los derechos reservados.</p>
                <div class="footer-legal">
                    <a href="#">Términos y Condiciones</a>
                    <a href="#">Política de Privacidad</a>
                </div>
            </div>
        </div><!-- .site-info -->
    </footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
<!-- Organization Schema -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "NewsMediaOrganization",
  "name": "<?php bloginfo('name'); ?>",
  "url": "<?php echo esc_url( home_url( '/' ) ); ?>",
  "logo": "<?php echo has_custom_logo() ? wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' ) : ''; ?>"
}
</script>
</body>
</html>
