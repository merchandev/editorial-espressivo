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
                    <div class="social-links">
                        <a href="#" aria-label="Facebook">Facebook</a>
                        <a href="#" aria-label="Twitter">Twitter</a>
                        <a href="#" aria-label="Instagram">Instagram</a>
                        <a href="#" aria-label="Telegram">Telegram</a>
                    </div>
                </div>

                <div class="footer-navigation">
                    <h3>Secciones</h3>
                    <?php
                    wp_nav_menu( array(
                        'theme_location' => 'footer',
                        'menu_id'        => 'footer-menu',
                        'fallback_cb'    => false,
                    ) );
                    ?>
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
