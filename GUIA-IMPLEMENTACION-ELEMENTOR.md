# Nova Studio - Guía de Implementación en Elementor

## 🎯 PASO 1: Crear Página Home

1. Ve a **Páginas → Añadir nueva**
2. Título: `Home`
3. **Publicar** la página (no añadas contenido aún)
4. Ve a **Ajustes → Lectura**
5. Selecciona "Una página estática" y elige `Home` como página principal
6. **Guardar cambios**

---

## 🎨 PASO 2: Configurar Elementor Site Settings

1. Ve a **Elementor → Site Settings** (o desde el editor: hamburger menu → Site Settings)
2. **Global Colors:**
   - Primary: `#2563EB` (Azul)
   - Secondary: `#0F172A` (Gris oscuro)
   - Text: `#334155` (Gris texto)
   - Accent: `#F59E0B` (Naranja)
   
3. **Global Fonts:**
   - Primary Heading: `Plus Jakarta Sans` (weight: 700)
   - Secondary Heading: `Plus Jakarta Sans` (weight: 600)
   - Body Text: `Inter` (weight: 400)
   - Accent Text: `Inter` (weight: 600)

---

## 📐 PASO 3: Editar Página Home con Elementor

1. Ve a **Páginas → Home**
2. Clic en **"Editar con Elementor"**
3. En Ajustes de página (⚙️), configura:
   - Layout: **Elementor Canvas** (sin header/footer)
   - Ocultar título de página: **SÍ**

---

## 🏗️ PASO 4: Construir Secciones

### 1️⃣ SECCIÓN HERO

**Estructura:**
```
Section (Full Width, Min Height: 100vh)
├── Container (Center, Max Width: 1200px)
    ├── Column (50%)
    │   ├── Heading (H1): "Diseñamos sitios web que convierten visitantes en clientes"
    │   ├── Text Editor: "Creamos experiencias digitales memorables que impulsan tu negocio"
    │   └── Button: "Solicita tu presupuesto"
    └── Column (50%)
        └── Image (ilustración o mockup)
```

**Estilos:**
- **Section:**
  - Background: Linear Gradient (#F8FAFC → #FFFFFF)
  - Padding: 100px 50px
  
- **Heading:**
  - Tipografía: Plus Jakarta Sans, 60px (mobile: 36px)
  - Color: #0F172A
  - Line Height: 1.2
  
- **Texto:**
  - Tipografía: Inter, 20px
  - Color: #64748B
  
- **Botón:**
  - Background: #F59E0B
  - Color texto: #0F172A
  - Padding: 18px 40px
  - Border Radius: 12px
  - Hover: Background #D97706

---

### 2️⃣ SECCIÓN SERVICIOS

**Estructura:**
```
Section
├── Container (Center)
    ├── Heading (H2): "Nuestros Servicios"
    ├── Text: "Soluciones digitales que impulsan tu negocio"
    └── Inner Section (3 columnas)
        ├── Column
        │   ├── Icon Box
        │   ├── Heading (H3): "Diseño Web"
        │   └── Text: "Diseño UI/UX centrado en conversión"
        ├── Column
        │   ├── Icon Box
        │   ├── Heading: "Desarrollo Web"
        │   └── Text: "WordPress, React, desarrollos custom"
        └── Column
            ├── Icon Box
            ├── Heading: "Estrategia Digital"
            └── Text: "SEO, Ads, Analytics y conversión"
```

**Estilos:**
- **Section:** Background #FFFFFF, Padding 100px 50px
- **Cards (Columnas):**
  - Background: #FFFFFF
  - Border: 1px solid #E2E8F0
  - Border Radius: 16px
  - Padding: 40px
  - Box Shadow: 0 4px 6px rgba(0,0,0,0.1)
  - Hover: Transform translateY(-8px), Shadow aumentada
- **Iconos:** Color #2563EB, Size 48px

---

### 3️⃣ SECCIÓN DIFERENCIAL

**Estructura:**
```
Section (Background: #F8FAFC)
├── Container (2 columnas)
    ├── Column (50%)
    │   └── Image (o video ilustrativo)
    └── Column (50%)
        ├── Heading (H2): "¿Por qué elegirnos?"
        ├── Icon List
        │   ├── Item: "Enfoque en ROI y resultados medibles"
        │   ├── Item: "Diseño centrado en conversión"
        │   └── Item: "Soporte continuo post-lanzamiento"
```

**Estilos:**
- Padding: 100px 50px
- Icon List: Iconos color #2563EB, spacing 24px

---

### 4️⃣ SECCIÓN PROCESO

**Estructura:**
```
Section
├── Container
    ├── Heading (H2): "Nuestro Proceso"
    └── Inner Section (4 columnas)
        ├── Counter + Icon + Heading + Text (Paso 1: Descubrir)
        ├── Counter + Icon + Heading + Text (Paso 2: Diseñar)
        ├── Counter + Icon + Heading + Text (Paso 3: Desarrollar)
        └── Counter + Icon + Heading + Text (Paso 4: Lanzar)
```

**Estilos:**
- Counters: Color #2563EB, Font size 48px
- Líneas conectoras entre pasos (opcional con dividers)

---

### 5️⃣ SECCIÓN TESTIMONIO

**Estructura:**
```
Section (Background: Gradient #2563EB → #1D4ED8)
├── Container (Center, Max Width: 800px)
    ├── Icon (Comillas)
    ├── Testimonial Widget o Text
    ├── Image (Avatar circular)
    ├── Heading: "María García"
    └── Text: "CEO, TechStartup"
```

**Estilos:**
- Padding: 120px 50px
- Todo el texto en blanco
- Quote: Font size 32px, Italic

---

### 6️⃣ SECCIÓN CTA FINAL

**Estructura:**
```
Section (Full Width, Background: #F59E0B)
├── Container (Center, Text Align: Center)
    ├── Heading (H2): "¿Listo para transformar tu presencia digital?"
    ├── Text: "Agenda una llamada gratuita y descubre cómo podemos ayudarte"
    └── Button: "Solicita tu presupuesto GRATIS"
```

**Estilos:**
- Padding: 80px 50px
- Heading: Color #0F172A
- Button: Background #0F172A, Color #FFFFFF

---

### 7️⃣ FOOTER

**Estructura:**
```
Section (Background: #0F172A)
├── Container (3 columnas)
    ├── Column: Logo + Descripción
    ├── Column: Contacto (Email, Teléfono)
    └── Column: Redes Sociales (iconos)
├── Divider
└── Text: "© 2026 Nova Studio. Todos los derechos reservados"
```

**Estilos:**
- Todo el texto en #CBD5E1
- Padding: 60px 50px 30px

---

## 📱 PASO 5: Revisar Responsive

1. En Elementor, usa el switcher de dispositivos (💻 📱)
2. Ajusta para **Tablet** y **Mobile**:
   - Hero: Cambia de 2 columnas a 1 columna (stack)
   - Servicios: De 3 columnas a 1 columna
   - Tamaños de fuente más pequeños
   - Padding reducido

---

## ✅ CHECKLIST FINAL

- [ ] Todas las secciones creadas
- [ ] Colores consistentes con el sistema de diseño
- [ ] Tipografías correctas (Plus Jakarta Sans + Inter)
- [ ] Botones con hover effects
- [ ] Responsive en mobile y tablet
- [ ] Imágenes optimizadas
- [ ] CTAs claros y visibles
- [ ] Footer con información de contacto

---

## 🎨 RECURSOS ADICIONALES

**Iconos:** Usa Elementor Icon Library o:
- https://fontawesome.com
- https://heroicons.com

**Imágenes placeholder:**
- https://unsplash.com (buscar: web design, office, technology)
- https://www.pexels.com

**Google Fonts (ya configuradas en el tema):**
- Plus Jakarta Sans: https://fonts.google.com/specimen/Plus+Jakarta+Sans
- Inter: https://fonts.google.com/specimen/Inter

---

**¡Listo para empezar a diseñar en Elementor!** 🚀

Sigue los pasos en orden y tendrás la landing page de Nova Studio completa.
