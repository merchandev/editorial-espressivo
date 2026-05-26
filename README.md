# Tema WordPress "Pro" - Portal de Noticias Avanzado

Este es un tema de WordPress diseñado y desarrollado a medida para un portal de noticias moderno, rápido y profesional. Está construido desde cero para optimizar el rendimiento, evitar el uso excesivo de plugins y ofrecer una experiencia de usuario (UX) premium.

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

---

## 🛠 Instalación y Configuración

1. **Subir el Tema:**
   Copia esta carpeta `pro/` en el directorio `wp-content/themes/` de tu instalación de WordPress.
2. **Activar el Tema:**
   Ve a *Apariencia > Temas* en el panel de WordPress y activa el tema "Pro".
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

---

## 💻 Detalles Técnicos

- **AJAX:** Implementado tanto para la paginación de noticias (`functions.php` > `pro_load_more_posts`) como para el formulario de contacto (`pro_submit_contact_form`).
- **Cache Busting:** CSS y JS actualizan su versión automáticamente mediante `filemtime()` para evitar que los usuarios guarden versiones antiguas del diseño en la caché de su navegador.
- **Seguridad:** Uso estricto de Nonces (`wp_create_nonce`) para validar todas las peticiones AJAX y envíos de formularios.

---
*Desarrollado a medida para maximizar el rendimiento y la organización editorial.*
