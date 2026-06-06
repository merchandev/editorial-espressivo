import { useState, useEffect } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

const SnippetPreview = () => {
    // Referencias a los datos inyectados por PHP (Meta Box)
    const rootElement = document.getElementById('ssivo-seo-metabox-root');
    const initialCustomTitle = rootElement?.dataset?.customTitle || '';
    const initialCustomDesc = rootElement?.dataset?.customDesc || '';

    const [customTitle, setCustomTitle] = useState(initialCustomTitle);
    const [customDesc, setCustomDesc] = useState(initialCustomDesc);

    // Seleccionar datos en tiempo real de Gutenberg
    const { postTitle, excerpt, siteName } = useSelect((select) => {
        const coreEditor = select('core/editor');
        const coreSite = select('core');
        
        // Obtener el título del sitio desde la API (puede requerir permisos, o usamos fallback)
        let siteData = coreSite.getSite();
        let fallbackSiteName = siteData ? siteData.title : 'Espressivo Editorial';

        return {
            postTitle: coreEditor.getEditedPostAttribute('title'),
            excerpt: coreEditor.getEditedPostAttribute('excerpt'),
            siteName: fallbackSiteName
        };
    }, []);

    // Determinar qué mostrar (Automático vs Personalizado)
    const finalTitle = customTitle.trim() !== '' ? customTitle : (postTitle || 'Título del Artículo');
    const finalDesc = customDesc.trim() !== '' ? customDesc : (excerpt || 'Esta es una descripción generada automáticamente basada en el extracto del artículo para mostrar en los resultados de búsqueda.');

    return (
        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '30px', padding: '10px 0' }}>
            
            {/* Columna Izquierda: Formulario de Edición */}
            <div>
                <h4 style={{ marginTop: 0, marginBottom: '15px', color: '#1e293b', fontSize: '15px' }}>Personalizar SEO</h4>
                
                <div style={{ marginBottom: '20px' }}>
                    <label style={{ display: 'block', fontWeight: '600', marginBottom: '8px', color: '#475569' }}>
                        Título SEO Personalizado
                    </label>
                    <input 
                        type="text" 
                        name="ssivo_seo_custom_title" 
                        value={customTitle} 
                        onChange={(e) => setCustomTitle(e.target.value)} 
                        style={{ width: '100%', padding: '8px', border: '1px solid #cbd5e1', borderRadius: '4px' }}
                        placeholder="Escribe un título atractivo (Opcional)"
                    />
                    <p style={{ margin: '4px 0 0 0', fontSize: '12px', color: '#64748b' }}>
                        Si lo dejas en blanco, se usará el título del artículo.
                    </p>
                </div>

                <div>
                    <label style={{ display: 'block', fontWeight: '600', marginBottom: '8px', color: '#475569' }}>
                        Descripción SEO Personalizada (Meta Description)
                    </label>
                    <textarea 
                        name="ssivo_seo_custom_desc" 
                        value={customDesc} 
                        onChange={(e) => setCustomDesc(e.target.value)} 
                        style={{ width: '100%', height: '100px', padding: '8px', border: '1px solid #cbd5e1', borderRadius: '4px' }}
                        placeholder="Escribe una descripción cautivadora (Opcional)"
                    />
                    <p style={{ margin: '4px 0 0 0', fontSize: '12px', color: '#64748b' }}>
                        Si lo dejas en blanco, se usará el Resumen (Extracto) del artículo o los primeros párrafos.
                    </p>
                </div>
            </div>

            {/* Columna Derecha: Vista Previa */}
            <div style={{ background: '#f8fafc', padding: '20px', borderRadius: '8px', border: '1px solid #e2e8f0' }}>
                <h4 style={{ marginTop: 0, marginBottom: '20px', color: '#1e293b', fontSize: '15px', display: 'flex', alignItems: 'center', gap: '8px' }}>
                    <span className="dashicons dashicons-search" style={{ color: '#3b82f6' }}></span>
                    Vista Previa en Google
                </h4>
                
                {/* Simulador de Google */}
                <div style={{ fontFamily: 'arial, sans-serif', maxWidth: '600px' }}>
                    <div style={{ fontSize: '14px', color: '#202124', display: 'flex', alignItems: 'center', gap: '5px', marginBottom: '2px' }}>
                        <div style={{ width: '28px', height: '28px', background: '#e2e8f0', borderRadius: '50%', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '12px' }}>🌐</div>
                        <div>
                            <span style={{ display: 'block', lineHeight: '1.2' }}>{siteName}</span>
                            <span style={{ color: '#4d5156', fontSize: '12px' }}>https://tusitio.com › categoria › articulo</span>
                        </div>
                    </div>
                    <h3 style={{ margin: '5px 0 3px 0', fontSize: '20px', color: '#1a0dab', fontWeight: 'normal', lineHeight: '1.3' }}>
                        {finalTitle} | {siteName}
                    </h3>
                    <p style={{ margin: 0, fontSize: '14px', color: '#4d5156', lineHeight: '1.58' }}>
                        {finalDesc.length > 155 ? finalDesc.substring(0, 155) + '...' : finalDesc}
                    </p>
                </div>
            </div>

        </div>
    );
};

export default SnippetPreview;
