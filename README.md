# Tema WordPress "Edit-Pro" — Portal de Noticias Avanzado y Framework SaaS

Este es un tema y framework de WordPress diseñado y desarrollado a medida para portales de noticias modernos, rápidos, profesionales y de alto rendimiento. Construido desde cero bajo una arquitectura de marca blanca (white-label) y SaaS, está optimizado para su fácil duplicación, reventa y distribución a editoriales y medios de comunicación digitales. Evita el uso excesivo de plugins de terceros y ofrece una experiencia de usuario (UX) premium de alto impacto comercial.

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
- **Lightbox Premium con Glassmorphism:** Al hacer clic en una portada, se abre un visor interactivo a pantalla completa con fondo oscurecido y desenfocado con desenfoque nativo.
- **Zoom y Panning Fluido:** Controles flotantes para acercar (Zoom In), alejar (Zoom Out) y restaurar (100%), con soporte para zoom mediante la rueda del ratón y arrastre interactivo (click-and-drag / touch) para móviles y PCs.
- **Descarga Directa de Alta Resolución:** Botón superior para descargar de forma instantánea el archivo original de la portada de revista.

---

## 🔐 Seguridad

### Login URL personalizada (`/turpial`)
- La URL de inicio de sesión por defecto (`/wp-login.php`) fue movida a `/turpial`.
- Cualquier acceso directo a `/wp-login.php` o `/wp-admin/` por usuarios no autenticados recibe una respuesta **404**, ocultando la existencia del CMS.
- La acción `$_REQUEST['action']` del formulario de login es sanitizada con `sanitize_key()` y la `REQUEST_URI` se procesa con `esc_url_raw()` antes de ser usada.
- El matching de la ruta `/turpial` es exacto (`===`) para evitar que rutas como `/turpial-noticias/` activen el login accidentalmente.

### Protección contra XSS
- Todos los `get_search_query()` en formularios de búsqueda están envueltos con `esc_attr()`.
- La descripción del sitio usa `esc_html()`.
- Los bloques **Schema.org JSON-LD** en `single.php` y `footer.php` usan `wp_json_encode()` en lugar de `esc_js()`, produciendo JSON válido y seguro aunque el título contenga comillas o caracteres especiales.

### Sanitización y validación de inputs
- **Formulario de contacto:** Bloquea URLs, código HTML, etiquetas SQL (`union select`, `drop table`, etc.) y caracteres peligrosos mediante doble capa: regex del servidor + `sanitize_textarea_field()`.
- **URLs de anuncios y PDF:** Guardadas con `esc_url_raw()` (no `sanitize_text_field`).
- **Fechas del polling AJAX:** Validadas contra el formato MySQL exacto (`YYYY-MM-DD HH:MM:SS`) antes de usarse en `date_query`.
- **AJAX endpoints:** Todos los endpoints públicos (`wp_ajax_nopriv_*`) verifican Nonce con `check_ajax_referer()`.

### Protección de archivos PHP
- Todos los archivos en `inc/` (`security.php`, `ad-manager.php`, `admin-whitelabel.php`) tienen el guard:
  ```php
  if ( ! defined( 'ABSPATH' ) ) { exit; }
  ```
  Esto impide el acceso directo a los archivos si el servidor web está mal configurado.

### Otros
- La versión de WordPress es eliminada del `<head>` con `remove_action('wp_head', 'wp_generator')`.
- El selector de colores del panel y los widgets de noticias de WordPress son ocultados para el rol `Dirección` y `Autor`.

---

## ⚡ Performance

### Transient Caching
El tema implementa una capa de caché con **WordPress Transients** para eliminar consultas repetidas en cada carga de página:

| Transient | TTL | Contenido |
|-----------|-----|-----------|
| `pro_latest_post_date` | 60 segundos | Fecha del post más reciente (para el polling de nuevas noticias) |
| `pro_ticker_posts` | 5 minutos | IDs de los 10 posts del ticker de "Último Minuto" |

Ambos transients se **invalidan automáticamente** cuando se publica o despublica un post mediante el hook `transition_post_status`.

### Cache Busting de Assets
CSS y JS actualizan su versión automáticamente mediante `filemtime()` para evitar que los usuarios guarden versiones antiguas del diseño en la caché de su navegador.

### Optimización de queries AJAX
- El endpoint `pro_check_new_posts` usa `fields => 'ids'` para recuperar solo IDs en lugar de objetos completos.
- El endpoint de polling de noticias reconstruye los argumentos de consulta en el servidor (nunca acepta `query_vars` del cliente).

---

## 🛠 Instalación y Configuración

1. **Subir el Tema:**
   Copia esta carpeta `edit-pro/` (o el nombre que asignes a la carpeta del tema) en el directorio `wp-content/themes/` de tu instalación de WordPress.
2. **Activar el Tema:**
   Ve a *Apariencia > Temas* en el panel de WordPress y activa el tema "Edit-Pro".
3. **Guardar Enlaces Permanentes (OBLIGATORIO):**
   Ve a *Ajustes > Enlaces permanentes* y haz clic en **Guardar cambios** sin modificar nada. Esto es crucial para que WordPress reconozca la nueva URL de `/carteles/` y los endpoints de contacto.

---

## 📖 Guía de Uso Rápido

### ¿Cómo subir un Cartel o Edicto?
1. Ve al menú **Carteles > Añadir Nuevo**.
2. Escribe el título del documento.
3. Asigna una **Imagen Destacada** (esta será la portada o primera página que se verá en la cuadrícula).
4. Baja hasta la caja **"Documento PDF del Cartel"** y pega ahí la URL directa del archivo PDF (previamente subido a Medios).
5. Publica. El cartel ya estará disponible en `tusitio.com/carteles/`.

### ¿Cómo ver los Mensajes de Contacto?
1. Para configurar el formulario en tu web, crea una Página y asígnale la plantilla **"Página de Contacto"**.
2. Cuando los usuarios escriban, sus mensajes llegarán a la pestaña **Mensajes** (ícono de correo en el panel izquierdo).
3. Para exportar todo a Excel, entra en la lista de Mensajes y haz clic en el botón azul **"Descargar Excel/CSV"** ubicado arriba a la izquierda.

### ¿Cómo iniciar sesión de forma segura?
- Accede siempre a través de `/turpial` (no a través de `/wp-admin/` o `/wp-login.php`).
- Los accesos directos a las rutas de WordPress devuelven un **404** como medida de seguridad.

---

## 💻 Detalles Técnicos

- **AJAX:** Implementado tanto para la paginación de noticias (`functions.php` > `pro_load_more_posts`) como para el formulario de contacto (`pro_submit_contact_form`) y el buscador predictivo (`pro_ajax_search`).
- **Cache Busting:** CSS y JS actualizan su versión automáticamente mediante `filemtime()`.
- **Nonces:** Uso estricto de `wp_create_nonce` / `check_ajax_referer` para validar todas las peticiones AJAX.
- **Schema.org:** JSON-LD en artículos (`NewsArticle`) y en el footer (`NewsMediaOrganization`), generado con `wp_json_encode()` para garantizar JSON válido ante cualquier carácter.
- **Roles:** El rol personalizado `Dirección` hereda las capacidades necesarias y tiene el panel de administración con white-label (logo y colores personalizados).

---

## 🔄 Changelog de Seguridad

### v1.1.0 — Auditoría completa (Mayo 2026)
- ✅ Sanitización de `$_SERVER['REQUEST_URI']` con `esc_url_raw()` y `wp_unslash()`
- ✅ Matching exacto de la ruta `/turpial` (corregido bypass por substring)
- ✅ `sanitize_key()` aplicado al parámetro `action` del formulario de login
- ✅ `esc_attr()` aplicado a todos los `get_search_query()` en formularios de búsqueda
- ✅ `esc_html()` aplicado a la descripción del sitio en el header
- ✅ JSON-LD migrado de `esc_js()` + comillas literales a `wp_json_encode()` seguro
- ✅ URLs de anuncios y PDFs migradas de `sanitize_text_field()` / `sanitize_url()` a `esc_url_raw()`
- ✅ Validación de formato de fecha antes de usar en `date_query`
- ✅ `wp_die()` redundantes eliminados (ya incluido en `wp_send_json_error`)
- ✅ Guard `ABSPATH` añadido a todos los archivos `inc/*.php`
- ✅ Transient caching implementado para ticker y polling
- ✅ Invalidación automática de transients al publicar/despublicar posts
- ✅ `deprecated_function_trigger_error` suprimido para evitar warnings de terceros en producción

---
*Desarrollado y distribuido por Arturo Merchan | Merchan.Dev — Framework editorial SaaS premium de alto rendimiento para medios digitales e impresos.*
