<?php
namespace SSIVO_SEO\Includes;

class AdminPage {
    public function __construct() {
        add_action( 'admin_menu',    [ $this, 'register_menu' ] );
        add_action( 'admin_init',    [ $this, 'register_settings' ] );
        add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
    }

    /**
     * Registrar endpoint proxy para datos de Google Site Kit.
     * Permite que roles con edit_posts accedan a los datos sin necesidad
     * de ser el propietario de la cuenta de Site Kit.
     */
    public function register_rest_routes() {
        register_rest_route( 'ssivo-seo/v1', '/analytics', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'proxy_analytics_data' ],
            'permission_callback' => function() {
                return current_user_can( 'edit_posts' );
            },
        ] );
    }

    /**
     * Proxy: despacha internamente las peticiones a Site Kit usando rest_do_request().
     * Al cambiar el usuario activo antes de la llamada interna, Site Kit ve al
     * administrador y devuelve los datos. No se usan cookies ni HTTP externo.
     */
    public function proxy_analytics_data( \WP_REST_Request $request ) {
        // Buscar un administrador nativo de WordPress
        $admin_users = get_users( [ 'role' => 'administrator', 'number' => 1 ] );
        if ( empty( $admin_users ) ) {
            return new \WP_Error( 'no_admin', 'No se encontró administrador.', [ 'status' => 403 ] );
        }

        $admin_id      = $admin_users[0]->ID;
        $original_user = get_current_user_id();

        // Cambiar al administrador para las llamadas internas a Site Kit
        wp_set_current_user( $admin_id );

        // Definir rutas y sus parámetros de consulta
        $endpoints = [
            'ga4'       => [
                'route'  => '/google-site-kit/v1/modules/analytics-4/data/report',
                'params' => 'metrics[0][name]=screenPageViews&metrics[1][name]=activeUsers&dateRange=last-28-days',
            ],
            'search'    => [
                'route'  => '/google-site-kit/v1/modules/search-console/data/searchanalytics',
                'params' => 'dimensions=date&dateRange=last-28-days',
            ],
            'top_pages' => [
                'route'  => '/google-site-kit/v1/modules/analytics-4/data/report',
                'params' => 'metrics[0][name]=screenPageViews&dimensions[0][name]=pageTitle&dimensions[1][name]=pagePath&orderby[0][metric][metricName]=screenPageViews&orderby[0][desc]=true&limit=5&dateRange=last-28-days',
            ],
            'keywords'  => [
                'route'  => '/google-site-kit/v1/modules/search-console/data/searchanalytics',
                'params' => 'dimensions=query&limit=5&dateRange=last-28-days',
            ],
            'countries' => [
                'route'  => '/google-site-kit/v1/modules/analytics-4/data/report',
                'params' => 'metrics[0][name]=screenPageViews&dimensions[0][name]=country&orderby[0][metric][metricName]=screenPageViews&orderby[0][desc]=true&limit=5&dateRange=last-28-days',
            ],
            'devices'   => [
                'route'  => '/google-site-kit/v1/modules/analytics-4/data/report',
                'params' => 'metrics[0][name]=screenPageViews&dimensions[0][name]=deviceCategory&dateRange=last-28-days',
            ],
        ];

        $results = [];
        foreach ( $endpoints as $key => $config ) {
            // Crear petición REST interna (sin HTTP, sin cookies)
            $rest_req = new \WP_REST_Request( 'GET', $config['route'] );
            parse_str( $config['params'], $query_params );
            $rest_req->set_query_params( $query_params );

            // Despachar internamente — Site Kit ve al administrador como usuario actual
            $rest_response = rest_do_request( $rest_req );
            $data          = $rest_response->get_data();
            $status        = $rest_response->get_status();

            // Si Site Kit devuelve error, incluirlo en el resultado para debug
            $results[ $key ] = ( $status >= 200 && $status < 300 ) ? $data : [
                '__error'  => true,
                '__status' => $status,
                '__data'   => $data,
            ];
        }

        // Restaurar el usuario original tras las llamadas internas
        wp_set_current_user( $original_user );

        return rest_ensure_response( $results );
    }


    public function register_menu() {
        add_menu_page(
            'Ajustes SSIVO-SEO',
            'SEO',
            'edit_posts',
            'ssivo-seo',
            [ $this, 'render_page' ],
            'dashicons-chart-line',
            80
        );
    }

    public function register_settings() {
        register_setting( 'ssivo_seo_group', 'ssivo_seo_default_title' );
        register_setting( 'ssivo_seo_group', 'ssivo_seo_default_image' );
    }

    public function render_page() {
        if ( ! current_user_can( 'edit_posts' ) ) {
            return;
        }

        global $wpdb;

        // Recuperar valores actuales
        $default_title = get_option( 'ssivo_seo_default_title', get_bloginfo( 'name' ) );
        $default_image = get_option( 'ssivo_seo_default_image', '' );

        // --- ESTADÍSTICAS DEL DASHBOARD ---

        // 1. Total de publicaciones
        $total_posts = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = 'post'" );
        $total_posts = $total_posts ? intval( $total_posts ) : 0;

        // 2. Actividad de Autores
        $authors = $wpdb->get_results( "
            SELECT u.display_name, COUNT(p.ID) as post_count 
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->users} u ON p.post_author = u.ID
            WHERE p.post_status = 'publish' AND p.post_type = 'post'
            GROUP BY p.post_author
            ORDER BY post_count DESC
            LIMIT 5
        " );

        ?>
        <div class="wrap" style="max-width: 900px;">
            <h1 style="font-weight: 700; margin-bottom: 20px;">
                <span class="dashicons dashicons-chart-line" style="font-size: 28px; width: 28px; height: 28px; color: #3b82f6; margin-right: 8px; margin-top: 2px;"></span>
                Panel Principal de SSIVO-SEO
            </h1>
            
            <p style="font-size: 14px; color: #475569; margin-bottom: 30px;">
                Resumen de salud y opciones globales de posicionamiento en buscadores para tu plataforma.
            </p>

            <!-- DASHBOARD WIDGETS -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">

                <!-- Widget: Publicaciones -->
                <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; border-top: 4px solid #64748b;">
                    <h3 style="margin-top: 0; color: #1e293b; font-size: 15px; margin-bottom: 15px;">Total Publicaciones</h3>
                    <div style="display: flex; align-items: flex-end; gap: 10px;">
                        <span style="font-size: 36px; font-weight: 800; color: #1e293b; line-height: 1;"><?php echo number_format_i18n( $total_posts ); ?></span>
                        <span style="font-size: 14px; color: #64748b; margin-bottom: 5px;">artículos activos</span>
                    </div>
                    <p style="margin: 10px 0 0 0; font-size: 13px; color: #64748b;">
                        Artículos indexados y publicados en el sistema.
                    </p>
                </div>

                <!-- Widget: Actividad de Usuarios -->
                <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; border-top: 4px solid #8b5cf6;">
                    <h3 style="margin-top: 0; color: #1e293b; font-size: 15px; margin-bottom: 15px;">Actividad de Autores</h3>
                    <ul style="margin: 0; padding: 0; list-style: none;">
                        <?php if ( $authors ) : ?>
                            <?php foreach ( $authors as $author ) : ?>
                                <li style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px;">
                                    <span style="color: #334155; font-weight: 500;"><?php echo esc_html( $author->display_name ); ?></span>
                                    <span style="color: #64748b; background: #f1f5f9; padding: 2px 8px; border-radius: 12px; font-size: 11px;"><?php echo number_format_i18n( $author->post_count ); ?> posts</span>
                                </li>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <li style="font-size: 13px; color: #64748b;">Sin actividad registrada.</li>
                        <?php endif; ?>
                    </ul>
                </div>

            </div>

            <!-- Métricas Site Kit (Diseño Nativo) -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
                
                <!-- Widget: Visitas -->
                <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; border-top: 4px solid #3b82f6;">
                    <h3 style="margin-top: 0; color: #1e293b; font-size: 15px; margin-bottom: 15px;">Visitas (Últimos 28 días)</h3>
                    <div style="display: flex; align-items: flex-end; gap: 10px;">
                        <span id="sk-visitas" style="font-size: 36px; font-weight: 800; color: #1e293b; line-height: 1;">...</span>
                        <span style="font-size: 14px; color: #64748b; margin-bottom: 5px;">vistas de página</span>
                    </div>
                </div>

                <!-- Widget: Usuarios Únicos -->
                <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; border-top: 4px solid #10b981;">
                    <h3 style="margin-top: 0; color: #1e293b; font-size: 15px; margin-bottom: 15px;">Usuarios Únicos</h3>
                    <div style="display: flex; align-items: flex-end; gap: 10px;">
                        <span id="sk-usuarios" style="font-size: 36px; font-weight: 800; color: #1e293b; line-height: 1;">...</span>
                        <span style="font-size: 14px; color: #64748b; margin-bottom: 5px;">usuarios</span>
                    </div>
                </div>

                <!-- Widget: Impresiones -->
                <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; border-top: 4px solid #f59e0b;">
                    <h3 style="margin-top: 0; color: #1e293b; font-size: 15px; margin-bottom: 15px;">Impresiones en Buscador</h3>
                    <div style="display: flex; align-items: flex-end; gap: 10px;">
                        <span id="sk-impresiones" style="font-size: 36px; font-weight: 800; color: #1e293b; line-height: 1;">...</span>
                        <span style="font-size: 14px; color: #64748b; margin-bottom: 5px;">veces visto</span>
                    </div>
                </div>
            </div>

            <!-- Nuevas Métricas -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin-bottom: 30px;">
                <!-- Top Contenidos -->
                <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; border-top: 4px solid #ef4444;">
                    <h3 style="margin-top: 0; color: #1e293b; font-size: 15px; margin-bottom: 15px;">Top 5 Contenidos Más Visitados</h3>
                    <ul id="sk-top-content" style="margin: 0; padding: 0; list-style: none;">
                        <li style="font-size: 13px; color: #64748b;">Cargando...</li>
                    </ul>
                </div>

                <!-- Top Keywords -->
                <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; border-top: 4px solid #8b5cf6;">
                    <h3 style="margin-top: 0; color: #1e293b; font-size: 15px; margin-bottom: 15px;">Palabras Clave (Google Search)</h3>
                    <ul id="sk-top-keywords" style="margin: 0; padding: 0; list-style: none;">
                        <li style="font-size: 13px; color: #64748b;">Cargando...</li>
                    </ul>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin-bottom: 30px;">
                <!-- Países -->
                <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; border-top: 4px solid #10b981;">
                    <h3 style="margin-top: 0; color: #1e293b; font-size: 15px; margin-bottom: 15px;">Tráfico por Países</h3>
                    <ul id="sk-top-countries" style="margin: 0; padding: 0; list-style: none;">
                        <li style="font-size: 13px; color: #64748b;">Cargando...</li>
                    </ul>
                </div>

                <!-- Dispositivos -->
                <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; border-top: 4px solid #3b82f6;">
                    <h3 style="margin-top: 0; color: #1e293b; font-size: 15px; margin-bottom: 15px;">Distribución por Dispositivo</h3>
                    <ul id="sk-devices" style="margin: 0; padding: 0; list-style: none;">
                        <li style="font-size: 13px; color: #64748b;">Cargando...</li>
                    </ul>
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Helper: crear elemento de lista seguro (sin XSS)
                function makeLi(leftText, rightText, extraStyle) {
                    var li = document.createElement('li');
                    li.style.cssText = 'display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:13px;' + (extraStyle || '');
                    var spanL = document.createElement('span');
                    spanL.style.cssText = 'color:#334155;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:80%;';
                    spanL.textContent = leftText;
                    var spanR = document.createElement('span');
                    spanR.style.color = '#64748b';
                    spanR.textContent = rightText;
                    li.appendChild(spanL);
                    li.appendChild(spanR);
                    return li;
                }

                function setEmpty(id, msg) {
                    var el = document.getElementById(id);
                    if (!el) return;
                    el.innerHTML = '';
                    var li = document.createElement('li');
                    li.style.cssText = 'font-size:13px;color:#64748b;';
                    li.textContent = msg || 'Sin datos suficientes.';
                    el.appendChild(li);
                }

                function setError(id) {
                    var el = document.getElementById(id);
                    if (!el) return;
                    el.innerHTML = '';
                    var li = document.createElement('li');
                    li.style.cssText = 'color:red;font-size:13px;';
                    li.textContent = 'Error al cargar datos.';
                    el.appendChild(li);
                }

                // Una sola llamada al proxy – funciona para cualquier rol con edit_posts
                var proxyUrl = '<?php echo esc_url_raw( rest_url( "ssivo-seo/v1/analytics" ) ); ?>';
                var nonce    = '<?php echo wp_create_nonce( "wp_rest" ); ?>';

                fetch(proxyUrl, {
                    headers: { 'X-WP-Nonce': nonce, 'Content-Type': 'application/json' }
                })
                .then(function(res) { return res.json(); })
                .then(function(all) {
                    // DEBUG: ver respuesta completa del proxy en consola
                    console.log('[SSIVO-SEO] Respuesta del proxy:', JSON.stringify(all));

                    // 1. GA4 – Visitas y Usuarios
                    var ga4 = all.ga4;
                    if (ga4 && ga4.rowCount > 0 && ga4.rows && ga4.rows[0].metricValues) {
                        document.getElementById('sk-visitas').textContent  = parseInt(ga4.rows[0].metricValues[0].value).toLocaleString();
                        document.getElementById('sk-usuarios').textContent = parseInt(ga4.rows[0].metricValues[1].value).toLocaleString();
                    } else {
                        document.getElementById('sk-visitas').textContent  = '0';
                        document.getElementById('sk-usuarios').textContent = '0';
                    }

                    // 2. Search Console – Impresiones totales
                    var sc = all.search;
                    if (Array.isArray(sc) && sc.length > 0) {
                        var totalImp = 0;
                        sc.forEach(function(row) { totalImp += parseInt(row.impressions || 0); });
                        document.getElementById('sk-impresiones').textContent = totalImp.toLocaleString();
                    } else {
                        document.getElementById('sk-impresiones').textContent = '0';
                    }

                    // 3. Top Contenidos (GA4)
                    var ulContent = document.getElementById('sk-top-content');
                    ulContent.innerHTML = '';
                    var tp = all.top_pages;
                    if (tp && tp.rows && tp.rows.length > 0) {
                        tp.rows.forEach(function(row) {
                            var t = row.dimensionValues[0].value;
                            var v = parseInt(row.metricValues[0].value).toLocaleString() + ' vis';
                            ulContent.appendChild(makeLi(t, v));
                        });
                    } else { setEmpty('sk-top-content'); }

                    // 4. Top Keywords (Search Console)
                    var ulKw = document.getElementById('sk-top-keywords');
                    ulKw.innerHTML = '';
                    var kw = all.keywords;
                    if (Array.isArray(kw) && kw.length > 0) {
                        kw.forEach(function(row) {
                            var q = row.keys[0];
                            var c = (row.clicks ? parseInt(row.clicks).toLocaleString() : '0') + ' clics';
                            ulKw.appendChild(makeLi(q, c));
                        });
                    } else { setEmpty('sk-top-keywords'); }

                    // 5. Países (GA4)
                    var ulCo = document.getElementById('sk-top-countries');
                    ulCo.innerHTML = '';
                    var co = all.countries;
                    if (co && co.rows && co.rows.length > 0) {
                        co.rows.forEach(function(row) {
                            var country = row.dimensionValues[0].value;
                            var views   = parseInt(row.metricValues[0].value).toLocaleString() + ' vis';
                            ulCo.appendChild(makeLi(country, views));
                        });
                    } else { setEmpty('sk-top-countries'); }

                    // 6. Dispositivos (GA4)
                    var ulDev = document.getElementById('sk-devices');
                    ulDev.innerHTML = '';
                    var dv = all.devices;
                    if (dv && dv.rows && dv.rows.length > 0) {
                        var total = 0;
                        dv.rows.forEach(function(r) { total += parseInt(r.metricValues[0].value); });
                        dv.rows.forEach(function(row) {
                            var device = row.dimensionValues[0].value;
                            var val    = parseInt(row.metricValues[0].value);
                            var pct    = total > 0 ? Math.round((val / total) * 100) + '%' : '0%';
                            var li = makeLi(device, pct, 'text-transform:capitalize;');
                            ulDev.appendChild(li);
                        });
                    } else { setEmpty('sk-devices'); }

                })
                .catch(function(err) {
                    console.error('SSIVO-SEO proxy error:', err);
                    ['sk-visitas','sk-usuarios','sk-impresiones'].forEach(function(id) {
                        var el = document.getElementById(id);
                        if (el) el.textContent = 'N/D';
                    });
                    ['sk-top-content','sk-top-keywords','sk-top-countries','sk-devices'].forEach(function(id) {
                        setError(id);
                    });
                });
            });
            </script>

            <?php if ( current_user_can( 'manage_options' ) ) : ?>
            <!-- FORMULARIO DE AJUSTES -->
            <h2 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 15px;">Ajustes Globales</h2>
            <form method="post" action="options.php" style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0;">
                <?php
                settings_fields( 'ssivo_seo_group' );
                do_settings_sections( 'ssivo_seo_group' );
                ?>
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="ssivo_seo_default_title" style="font-weight: 600;">Sufijo del Título Global</label></th>
                            <td>
                                <input name="ssivo_seo_default_title" type="text" id="ssivo_seo_default_title" value="<?php echo esc_attr( $default_title ); ?>" class="regular-text" />
                                <p class="description">Este texto se añadirá automáticamente al final de tus títulos. (Ej. " | Espressivo Editorial")</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ssivo_seo_default_image" style="font-weight: 600;">URL Imagen Destacada por Defecto</label></th>
                            <td>
                                <input name="ssivo_seo_default_image" type="url" id="ssivo_seo_default_image" value="<?php echo esc_url( $default_image ); ?>" class="regular-text large-text" />
                                <p class="description">Imagen que se usará al compartir en Facebook/Twitter si el artículo no tiene imagen destacada propia.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;" />
                <?php submit_button( 'Guardar Cambios SEO', 'primary', 'submit', false ); ?>
            </form>
            <?php endif; ?>
        </div>
        <?php
    }
}
