# Tema WordPress "Edit-Pro" — Portal de Noticias Avanzado y Framework SaaS

Este es un tema y framework de WordPress diseñado y desarrollado a medida para portales de noticias modernos, rápidos, profesionales y de alto rendimiento. Construido desde cero bajo una arquitectura de marca blanca (white-label) y SaaS, está optimizado para su fácil duplicación, reventa y distribución a editoriales y medios de comunicación digitales. Evita el uso excesivo de plugins de terceros y ofrece una experiencia de usuario (UX) premium de alto impacto comercial.

---

## 🚀 Características Principales

### 1. Sistema de "Carteles y Edictos" (PDF Lightbox)
- **Custom Post Type Dedicado:** Sección exclusiva en el panel de administración separada de las noticias.
- **Cuadrícula de Documentos:** Diseño vertical optimizado para miniaturas de documentos legales (`/carteles/`).
- **Visor PDF Nativo sin salir de la página:** Al hacer clic en un cartel, se oscurece la pantalla y se abre un visor de PDF integrado que permite leer, hacer zoom e imprimir el documento oficial.

### 2. Formulario de Contacto Inteligente y Exportación
- **100% Nativo (Sin Contact Form 7):** Formulario desarrollado con HTML/JS y procesado por AJAX para envíos instantáneos sin recargar la página.
- **Validaciones en Vivo:** Borde rojo inteligente para campos vacíos o mal formateados (sin asteriscos molestos).
- **Animación de Éxito:** Checkmark animado al completar el envío.
- **Base de Datos Propia:** Todos los mensajes se guardan en un Custom Post Type oculto ("Mensajes").
- **Exportación a Excel:** Botón dedicado para descargar toda la base de contactos a un archivo CSV.

### 3. Sistema de Gestión de Publicidad (Ad Manager)
- **Bloques Publicitarios Flexibles:** Soporte nativo para Banners en el Header, In-Feed (entre noticias) y Sidebar.
- CPT oculto para la gestión de creatividades.

### 4. Experiencia de Usuario y Diseño
- **Scroll Infinito Automático (AJAX):** Las noticias cargan a medida que el usuario baja en la página.
- **Grid "Hero" Dinámico:** Diseño asimétrico para destacar las noticias principales en portada.
- **Menús Inteligentes:** Flechas desplegables automáticas y anulación de enlaces vacíos (`#`).
- **Diseño Estético y Limpio:** Enfoque en tipografía, espacios y colores de alto contraste para facilitar la lectura.

### 5. Portadas de Revista Interactivas (Zoom & Drag Lightbox)
- **Custom Post Type Dedicado (Portadas):** Organiza y publica portadas de revista o ediciones impresas directamente desde el menú del panel de control.
- **Lightbox Premium con Glassmorphism:** Al hacer clic en una portada, se abre un visor interactivo a pantalla completa con fondo oscurecido y desenfocado.
- **Zoom y Panning Fluido:** Controles flotantes para acercar (Zoom In), alejar (Zoom Out) y restaurar (100%), con soporte para zoom mediante la rueda del ratón y arrastre interactivo para móviles y PCs.
- **Descarga Directa de Alta Resolución:** Botón superior para descargar el archivo original de la portada.

---

## 🔐 Seguridad

### Login URL personalizada (`/turpial`)
- La URL de inicio de sesión por defecto (`/wp-login.php`) fue movida a `/turpial`.
- Cualquier acceso directo a `/wp-login.php` o `/wp-admin/` por usuarios no autenticados recibe una respuesta **404**, ocultando la existencia del CMS.
- La acción `$_REQUEST['action']` del formulario de login es sanitizada con `sanitize_key()`.
- El matching de la ruta `/turpial` es exacto (`===`) para evitar activaciones accidentales.

### Protección contra XSS
- Todos los `get_search_query()` en formularios de búsqueda están envueltos con `esc_attr()`.
- La descripción del sitio usa `esc_html()`.
- Los bloques **Schema.org JSON-LD** usan `wp_json_encode()` en lugar de `esc_js()`.

### Sanitización y validación de inputs
- **Formulario de contacto:** Bloquea URLs, código HTML, etiquetas SQL y caracteres peligrosos.
- **URLs de anuncios y PDF:** Guardadas con `esc_url_raw()`.
- **Fechas del polling AJAX:** Validadas contra el formato MySQL exacto.
- **AJAX endpoints:** Todos verifican Nonce con `check_ajax_referer()`.

### Protección de archivos PHP
- Todos los archivos en `inc/` tienen el guard `if ( ! defined( 'ABSPATH' ) ) { exit; }`.

---

## ⚡ Performance

### Transient Caching

| Transient | TTL | Contenido |
|-----------|-----|-----------|
| `pro_latest_post_date` | 60 segundos | Fecha del post más reciente (para polling) |
| `pro_ticker_posts` | 5 minutos | IDs de los 10 posts del ticker "Último Minuto" |

Ambos transients se **invalidan automáticamente** con el hook `transition_post_status`.

### Cache Busting de Assets
CSS y JS actualizan su versión automáticamente mediante `filemtime()`.

### Optimización de queries AJAX
- `pro_check_new_posts` usa `fields => 'ids'` para recuperar solo IDs.
- Los argumentos de consulta se reconstruyen en el servidor.

---

## 🛠 Instalación y Configuración

1. **Subir el Tema:**
   Copia la carpeta del tema en `wp-content/themes/` de tu WordPress.
2. **Activar el Tema:**
   Ve a *Apariencia > Temas* y activa "Edit-Pro".
3. **Guardar Enlaces Permanentes (OBLIGATORIO):**
   Ve a *Ajustes > Enlaces permanentes* y haz clic en **Guardar cambios**.
4. **Páginas automáticas (Nuclear Install):**
   Al entrar al panel de WordPress por primera vez, el sistema creará automáticamente todas las páginas necesarias (categorías, contacto, páginas legales) y configurará el menú principal sin intervención manual.

---

## 📖 Guía de Uso Rápido

### ¿Cómo subir un Cartel o Edicto?
1. Ve al menú **Carteles > Añadir Nuevo**.
2. Escribe el título del documento.
3. Asigna una **Imagen Destacada**.
4. Pega la URL del PDF en la caja **"Documento PDF del Cartel"**.
5. Publica. Estará disponible en `tusitio.com/carteles/`.

### ¿Cómo ver los Mensajes de Contacto?
1. Crea una Página y asígnale la plantilla **"Página de Contacto"**.
2. Los mensajes llegarán a la pestaña **Mensajes** del panel.
3. Para exportar, haz clic en **"Descargar Excel/CSV"**.

### ¿Cómo iniciar sesión de forma segura?
- Accede siempre a través de `/turpial` (no `/wp-admin/` o `/wp-login.php`).

---

## 💻 Detalles Técnicos

- **AJAX:** Paginación (`pro_load_more_posts`), formulario de contacto (`pro_submit_contact_form`) y buscador predictivo (`pro_ajax_search`).
- **Nonces:** Uso estricto de `wp_create_nonce` / `check_ajax_referer` en todas las peticiones AJAX.
- **Schema.org:** JSON-LD en artículos (`NewsArticle`) y en el footer (`NewsMediaOrganization`).
- **Roles:** El rol `Dirección` hereda capacidades de administrador con panel white-label personalizado.

---

## 🔄 Changelog

### v1.3.0 — Mayo 2026 (sesión actual)

#### Footer
- ✅ Sección "Redes Sociales" actualizada con enlaces oficiales del medio
- ✅ Título "**Más información**" reemplaza "Enlaces Útiles"
- ✅ Categoría "**Local**" excluida del bloque "Secciones Populares" vía filtro por slug
- ✅ **Política de Privacidad** eliminada del footer (solo permanecen Términos y Cookies)

#### Páginas Legales (Nuclear Install)
- ✅ Plantilla de **Términos y Condiciones** — creada con contenido legal completo
- ✅ Plantilla de **Política de Cookies** — nueva plantilla añadida
- ✅ Ambas páginas se crean **automáticamente** al entrar al admin (versión nuclear `v5`), sin pasos manuales en WordPress
- ✅ Los links del footer apuntan correctamente a sus slugs

#### Firma de Autor
- ✅ **Comentario HTML** de autoría visible en el código fuente de cada página
- ✅ **Firma estilizada en la consola del navegador** (DevTools → Console) con estilos de color corporativo

### v1.2.0 — Auditoría y funcionalidades (Mayo 2026)
- ✅ Nuclear Install v4: creación automática de páginas, menús y configuración de portada estática
- ✅ Lightbox interactivo con Zoom & Pan para portadas
- ✅ Rol `Dirección` con restricción de menús del panel
- ✅ Buscador predictivo AJAX con validaciones de seguridad
- ✅ Sistema de ticker "Último Minuto" con transient caching

### v1.1.0 — Auditoría de seguridad (Mayo 2026)
- ✅ Sanitización completa de inputs, nonces y guards ABSPATH
- ✅ JSON-LD migrado a `wp_json_encode()`
- ✅ Login URL personalizada `/turpial` con matching exacto
- ✅ Cache Busting con `filemtime()` en todos los assets

---

*Diseñado y desarrollado por **Merchan.Dev & Espressivo Venezuela, C.A** — Framework editorial SaaS premium para medios digitales e impresos.*
*Web: [merchan.dev](https://merchan.dev)*
