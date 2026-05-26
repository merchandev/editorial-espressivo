<?php
/**
 * Plantilla para el formulario de búsqueda global
 */
?>
<form role="search" method="get" class="search-form custom-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label>
        <span class="screen-reader-text"><?php echo _x( 'Buscar por:', 'label', 'pro' ); ?></span>
        <input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Buscar noticias...', 'placeholder', 'pro' ); ?>" value="<?php echo get_search_query(); ?>" name="s" autocomplete="off" />
    </label>
    <button type="submit" class="search-submit" aria-label="Buscar">
        <span class="material-symbols-outlined">search</span>
    </button>
</form>
