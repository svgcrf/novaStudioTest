# Nova Studio - Sistema de Diseño CSS

## Estructura de Archivos CSS

```
assets/css/
├── landing.css           → Estilos específicos landing page
├── form-premium.css      → Estilos del formulario premium
├── ux-enhancements.css   → Componentes UX (WhatsApp, back-to-top, scroll progress)
└── utilities.css         → Clases de utilidad reutilizables

style.css                 → Design system principal y variables
```

## Orden de Carga

1. **hello-elementor** (tema padre)
2. **nova-studio-fonts** (Google Fonts)
3. **nova-studio** (style.css - Design System)
4. **nova-utilities** (utilities.css - Global)
5. **nova-ux-enhancements** (ux-enhancements.css - Global)
6. **nova-landing** (landing.css - Solo en landing/front page)
7. **nova-form-premium** (form-premium.css - Solo en página de formulario)

## Variables CSS Disponibles

### Colores
```css
--nova-primary: #2563EB;
--nova-primary-dark: #1D4ED8;
--nova-primary-light: #3B82F6;
--nova-accent: #F59E0B;
--nova-accent-dark: #D97706;
--nova-accent-light: #FBBF24;
--nova-dark: #0F172A;
--nova-gray-900: #1E293B;
--nova-gray-700: #334155;
--nova-gray-500: #64748B;
--nova-gray-300: #CBD5E1;
--nova-gray-100: #F1F5F9;
--nova-light: #F8FAFC;
--nova-white: #FFFFFF;
```

### Tipografía
```css
--nova-font-heading: 'Plus Jakarta Sans', sans-serif;
--nova-font-body: 'Inter', sans-serif;

--nova-text-xs: 0.75rem;
--nova-text-sm: 0.875rem;
--nova-text-base: 1rem;
--nova-text-lg: 1.125rem;
--nova-text-xl: 1.25rem;
--nova-text-2xl: 1.5rem;
--nova-text-3xl: 1.875rem;
--nova-text-4xl: 2.25rem;
--nova-text-5xl: 3rem;
--nova-text-6xl: 3.75rem;
```

### Espaciado
```css
--nova-spacing-xs: 0.5rem;
--nova-spacing-sm: 1rem;
--nova-spacing-md: 1.5rem;
--nova-spacing-lg: 2rem;
--nova-spacing-xl: 3rem;
--nova-spacing-2xl: 4rem;
--nova-spacing-3xl: 6rem;
--nova-spacing-section: 100px;
```

### Sombras
```css
--nova-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
--nova-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
--nova-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
--nova-shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
--nova-shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
```

### Bordes
```css
--nova-radius-sm: 0.375rem;
--nova-radius-md: 0.5rem;
--nova-radius-lg: 0.75rem;
--nova-radius-xl: 1rem;
--nova-radius-full: 9999px;
```

### Transiciones
```css
--nova-transition-fast: 150ms ease;
--nova-transition-base: 250ms ease;
--nova-transition-slow: 350ms ease;
```

## Clases de Utilidad (utilities.css)

### Espaciado
- `.nova-mt-{xs|sm|md|lg|xl|2xl}` - margin-top
- `.nova-mb-{xs|sm|md|lg|xl|2xl}` - margin-bottom
- `.nova-py-{sm|md|lg|xl}` - padding vertical
- `.nova-px-{sm|md|lg}` - padding horizontal

### Flexbox
- `.nova-flex`, `.nova-flex-col`, `.nova-flex-wrap`
- `.nova-items-{center|start|end}`
- `.nova-justify-{center|between|around}`
- `.nova-gap-{xs|sm|md|lg|xl}`

### Grid
- `.nova-grid`, `.nova-grid-cols-{2|3|4}`

### Texto
- `.nova-text-{center|left|right}`
- `.nova-text-{xs|sm|base|lg|xl|2xl|3xl}`
- `.nova-font-{normal|medium|semibold|bold|extrabold}`
- `.nova-text-{primary|accent|dark|gray|light|white}`

### Fondo
- `.nova-bg-{primary|accent|dark|light|white|transparent}`
- `.nova-bg-gradient-{primary|accent}`

### Bordes y Sombras
- `.nova-border`, `.nova-border-{light|primary}`
- `.nova-rounded-{sm|md|lg|xl|full}`
- `.nova-shadow-{sm|md|lg|xl|none}`

### Componentes
- `.nova-card`, `.nova-card-dark`
- `.nova-badge`, `.nova-badge-{primary|accent|success}`
- `.nova-icon-box`, `.nova-icon-box-{lg|sm}`
- `.nova-divider`, `.nova-divider-dark`

## Componentes UX (ux-enhancements.css)

### Botón WhatsApp
```html
<a id="nova-whatsapp-btn" href="https://wa.me/NUMERO" target="_blank">
    <svg>...</svg>
    <span class="nova-wa-tooltip">¡Escríbenos!</span>
</a>
```
- Posición: Fixed, bottom-right
- Tamaño: 56px (52px en móvil)
- Animación: Pulse al hacer visible
- Tooltip: Aparece en hover (oculto en móvil)

### Botón Back-to-Top
```html
<button id="nova-back-to-top">
    <svg>...</svg>
</button>
```
- Posición: Fixed, arriba del WhatsApp
- Visible: Después de 300px de scroll
- Animación: Bounce sutil

### Barra de Progreso de Scroll
```html
<div id="nova-scroll-progress"></div>
```
- Posición: Fixed, top of page
- Color: Gradiente primary a accent
- Altura: 4px con blur

## Convenciones de Código

1. **Nomenclatura**: Prefijo `nova-` para todas las clases
2. **Variables**: Siempre usar variables CSS cuando existan
3. **Responsive**: Mobile-first, breakpoints: 768px, 1024px
4. **Animaciones**: Preferir `cubic-bezier` para transiciones suaves
5. **Accesibilidad**: Focus states visibles, prefers-reduced-motion
