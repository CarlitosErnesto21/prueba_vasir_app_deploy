# Tours - Estructura Modular

## 📁 Estructura de Archivos

```
tours/
├── composables/
│   └── useToursCommon.js           # Composable con lógica reutilizable
├── components/
│   ├── ToursGrid.vue               # Componente para mostrar grid de tours
│   └── ImageGallery.vue            # Componente para galería de imágenes
├── ToursLayout.vue                 # Layout base para vistas de tours
├── ToursNacionales.vue             # Vista simplificada para tours nacionales
├── ToursInternacionales.vue        # Vista simplificada para tours internacionales
├── DetalleTour.vue                 # Vista de detalle de tour (movida aquí)
└── README.md                       # Este archivo
```

## 🎯 Beneficios de la Refactorización

### ✅ Código Reutilizable
- **useToursCommon.js**: Composable con toda la lógica común entre tours nacionales e internacionales
- **ToursGrid.vue**: Componente reutilizable para mostrar las cards de tours
- **ImageGallery.vue**: Componente reutilizable para la galería de imágenes
- **ToursLayout.vue**: Layout base que puede ser usado por ambas vistas de tours

### ✅ Separación de Responsabilidades
- **Composables**: Lógica de negocio y estado reactivo
- **Components**: Elementos de UI reutilizables  
- **Views**: Solo configuración específica de cada vista

### ✅ Mantenimiento Simplificado
- Cambios en la lógica común solo requieren editar un archivo
- Cada componente tiene una responsabilidad específica
- Fácil de testear y debuggear

### ✅ Escalabilidad
- Fácil agregar nuevos tipos de tours (ej: tours premium, tours familiares)
- Componentes modulares permiten composición flexible
- Estructura preparada para futuras funcionalidades

## 🔧 Funcionalidades Incluidas

### Composable `useToursCommon.js`
- ✅ Gestión de estado de tours
- ✅ Funciones de formateo (fechas, duración, precios)
- ✅ Lógica de carruseles automáticos
- ✅ Gestión de reservas
- ✅ Verificación de reservas pendientes
- ✅ Galería de imágenes
- ✅ Estados de tours (disponible, agotado, etc.)

### Componente `ToursGrid.vue`
- ✅ Grid responsivo de tours
- ✅ Cards optimizadas con altura fija (360px)
- ✅ Badges de estado posicionados estratégicamente
- ✅ Carruseles automáticos en las cards
- ✅ Indicadores de carrusel centrados
- ✅ Botones de reserva y más información

### Componente `ImageGallery.vue`
- ✅ Modal de galería con navegación
- ✅ Autoplay configurable
- ✅ Miniaturas para navegación rápida
- ✅ Controles de reproducción
- ✅ Responsive design

### Layout `ToursLayout.vue`
- ✅ Header con título y estadísticas
- ✅ Estados de carga y error
- ✅ Integración de todos los componentes
- ✅ Gestión de modales

## 🚀 Migración Completada

### Archivos Eliminados/Reemplazados
Los archivos originales `ToursNacionales.vue` y `ToursInternacionales.vue` han sido reemplazados por versiones simplificadas que usan la nueva estructura modular.

### Archivos Movidos
- `DetalleTour.vue` → `tours/DetalleTour.vue`

### Nuevos Archivos Creados
- `tours/composables/useToursCommon.js`
- `tours/components/ToursGrid.vue` 
- `tours/components/ImageGallery.vue`
- `tours/ToursLayout.vue`
- `tours/ToursNacionales.vue` (nueva versión)
- `tours/ToursInternacionales.vue` (nueva versión)

## 📋 Líneas de Código Reducidas

### Antes de la Refactorización
- `ToursNacionales.vue`: ~901 líneas
- `ToursInternacionales.vue`: ~888 líneas
- **Total**: ~1,789 líneas

### Después de la Refactorización
- `ToursNacionales.vue`: ~74 líneas
- `ToursInternacionales.vue`: ~74 líneas
- `useToursCommon.js`: ~500 líneas (código reutilizable)
- `ToursGrid.vue`: ~200 líneas (componente reutilizable)
- `ImageGallery.vue`: ~150 líneas (componente reutilizable)
- `ToursLayout.vue`: ~200 líneas (layout reutilizable)
- **Total**: ~1,198 líneas

### 📊 Resultado
- **Reducción de ~33% en líneas de código total**
- **Eliminación de ~1,600 líneas de código duplicado**
- **Mejor organización y mantenibilidad**

## 🔄 Uso del Composable

```javascript
// En cualquier nueva vista de tours
import { useToursCommon } from './composables/useToursCommon.js'

const {
  tours,
  loading,
  obtenerTours,
  reservarTour,
  // ... todas las funciones y estados necesarios
} = useToursCommon()
```

## 🎨 Uso de Componentes

```vue
<!-- Usar el grid de tours -->
<ToursGrid
  :tours="tours"
  :tipo-tour="'nacional'"
  :obtener-estado-info="obtenerEstadoInfo"
  :reservar-tour="reservarTour"
  <!-- ... otras props -->
/>

<!-- Usar la galería -->
<ImageGallery
  :visible="showImageDialog"
  :imagenes="selectedTourImages"
  :cerrar-galeria="cerrarGaleria"
  <!-- ... otras props -->
/>
```

## 🚀 Extensibilidad

Para agregar un nuevo tipo de tours (ej: tours premium):

1. Crear `ToursPremium.vue`
2. Usar `useToursCommon()` 
3. Configurar URL API específica
4. Pasar props a `ToursLayout`

```vue
<script setup>
import { useToursCommon } from './composables/useToursCommon.js'

const {
  // ... composable
} = useToursCommon()

const URL_API = "/api/tours?categoria=premium"
const TITULO = "Tours Premium"
</script>

<template>
  <ToursLayout
    :titulo="TITULO"
    :url-api="URL_API"
    :tipo-tour="'premium'"
    <!-- ... props del composable -->
  />
</template>
```

## 🔧 Mantenimiento

### Para modificar lógica común:
- Editar `composables/useToursCommon.js`
- Los cambios se aplican automáticamente a todas las vistas

### Para modificar diseño de cards:
- Editar `components/ToursGrid.vue`
- Los cambios se aplican a todas las vistas de tours

### Para modificar galería:
- Editar `components/ImageGallery.vue`
- Los cambios se aplican a todas las vistas

¡La nueva estructura es mucho más mantenible y escalable! 🎉
