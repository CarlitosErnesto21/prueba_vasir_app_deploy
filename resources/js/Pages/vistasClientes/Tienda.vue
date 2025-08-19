<script setup>
import Catalogo from '../Catalogo.vue'
import Card from 'primevue/card'
import Button from 'primevue/button'
import { ref, computed } from 'vue'

// Datos estáticos de productos recomendables
const products = ref([
  {
    id: 1,
    nombre: 'Maleta de Viaje Premium',
    descripcion: 'Maleta de alta calidad con ruedas giratorias 360°, ideal para viajes largos.',
    precio: 150.00,
    imagen: '/images/productos/producto1.jpg',
    categoria: 'Equipaje'
  },
  {
    id: 2,
    nombre: 'Kit de Aseo Personal',
    descripcion: 'Set completo de productos de higiene personal para viajeros.',
    precio: 25.99,
    imagen: '/images/productos/producto2.jpg',
    categoria: 'Cuidado Personal'
  },
  {
    id: 3,
    nombre: 'Cámara Instantánea',
    descripcion: 'Captura tus mejores momentos de viaje al instante.',
    precio: 89.50,
    imagen: '/images/productos/producto3.jpg',
    categoria: 'Fotografía'
  },
  {
    id: 4,
    nombre: 'Mochila Aventurera',
    descripcion: 'Mochila resistente de 40L perfecta para excursiones.',
    precio: 75.00,
    imagen: '/images/productos/producto4.jpg',
    categoria: 'Equipaje'
  },
  {
    id: 5,
    nombre: 'Guía de Viajes Mundial',
    descripcion: 'Libro completo con los mejores destinos del mundo.',
    precio: 35.99,
    imagen: '/images/productos/producto5.jpg',
    categoria: 'Libros'
  },
  {
    id: 6,
    nombre: 'Adaptador Universal',
    descripcion: 'Adaptador de corriente compatible con más de 150 países.',
    precio: 22.50,
    imagen: '/images/productos/producto6.jpg',
    categoria: 'Electrónicos'
  },
  {
    id: 7,
    nombre: 'Botella Térmica',
    descripcion: 'Mantiene bebidas frías o calientes por 24 horas.',
    precio: 28.99,
    imagen: '/images/productos/producto1.jpg',
    categoria: 'Cuidado Personal'
  },
  {
    id: 8,
    nombre: 'Almohada de Viaje',
    descripcion: 'Almohada ergonómica para viajes largos en avión.',
    precio: 19.99,
    imagen: '/images/productos/producto2.jpg',
    categoria: 'Equipaje'
  }
])

// Filtros
const selectedCategories = ref([])
const minPrice = ref('')
const maxPrice = ref('')

// Categorías disponibles
const categories = [
  'Equipaje',
  'Cuidado Personal', 
  'Fotografía',
  'Libros',
  'Electrónicos'
]

// Productos filtrados
const filteredProducts = computed(() => {
  let filtered = [...products.value]
  
  // Filtrar por categorías
  if (selectedCategories.value.length > 0) {
    filtered = filtered.filter(product => 
      selectedCategories.value.includes(product.categoria)
    )
  }
  
  // Filtrar por precio mínimo
  if (minPrice.value && !isNaN(minPrice.value)) {
    filtered = filtered.filter(product => 
      product.precio >= parseFloat(minPrice.value)
    )
  }
  
  // Filtrar por precio máximo
  if (maxPrice.value && !isNaN(maxPrice.value)) {
    filtered = filtered.filter(product => 
      product.precio <= parseFloat(maxPrice.value)
    )
  }
  
  return filtered
})

// Funciones para filtros
const toggleCategory = (category) => {
  const index = selectedCategories.value.indexOf(category)
  if (index > -1) {
    selectedCategories.value.splice(index, 1)
  } else {
    selectedCategories.value.push(category)
  }
}

const clearFilters = () => {
  selectedCategories.value = []
  minPrice.value = ''
  maxPrice.value = ''
}

const applyPriceFilter = () => {
  // Trigger reactivity
  console.log('Filtros aplicados')
}

// Funciones para los botones
const comprarProducto = (producto) => {
  alert(`¡Producto "${producto.nombre}" agregado al carrito!`)
}

const verDetalles = (producto) => {
  alert(`Detalles del producto: ${producto.nombre}\nPrecio: $${producto.precio}\nCategoría: ${producto.categoria}`)
}
</script>
<template>
  <Catalogo>
    <div class="p-4 bg-gray-50 min-h-screen">
      <h1 class="text-2xl font-bold mb-2 text-red-700">🛍️ Productos</h1>
      <p class="mb-4 text-gray-600">Encuentra los mejores productos para tu viaje</p>
      
      <div class="flex gap-6">
        <!-- Panel de Filtros -->
        <div class="w-80 bg-white rounded-lg shadow-lg p-6 h-fit border border-gray-200">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Filtros</h3>
            <button 
              @click="clearFilters"
              class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition shadow-sm"
            >
              Limpiar filtros
            </button>
          </div>
          
          <!-- Filtro de Precio -->
          <div class="mb-6">
            <h4 class="font-semibold text-gray-700 mb-3">Precio</h4>
            <div class="flex gap-2 items-center mb-3">
              <div class="flex-1">
                <label class="text-sm text-gray-600">Min</label>
                <input 
                  v-model="minPrice"
                  type="number" 
                  placeholder="$0.00"
                  class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:border-red-500 focus:outline-none bg-gray-50 focus:bg-white"
                  min="0"
                  step="0.01"
                />
              </div>
              <div class="flex-1">
                <label class="text-sm text-gray-600">Max</label>
                <input 
                  v-model="maxPrice"
                  type="number" 
                  placeholder="$0.00"
                  class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:border-red-500 focus:outline-none bg-gray-50 focus:bg-white"
                  min="0"
                  step="0.01"
                />
              </div>
              <button 
                @click="applyPriceFilter"
                class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition mt-5 shadow-sm"
              >
                Ir
              </button>
            </div>
          </div>
          
          <!-- Filtro de Categorías -->
          <div>
            <h4 class="font-semibold text-gray-700 mb-3">Categorías</h4>
            <div class="space-y-2">
              <label 
                v-for="category in categories" 
                :key="category"
                class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded transition"
              >
                <input 
                  type="checkbox"
                  :checked="selectedCategories.includes(category)"
                  @change="toggleCategory(category)"
                  class="mr-3 accent-red-600"
                />
                <span class="text-sm text-gray-700 flex-1">{{ category }}</span>
                <span class="text-xs text-gray-500">›</span>
              </label>
            </div>
          </div>
        </div>
        
        <!-- Grid de Productos -->
        <div class="flex-1">
          <div class="mb-4 text-sm text-gray-600 bg-white rounded-lg px-4 py-2 shadow-sm border border-gray-200">
            Mostrando {{ filteredProducts.length }} de {{ products.length }} productos
          </div>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <Card
              v-for="producto in filteredProducts"
              :key="producto.id"
              class="border border-gray-300 bg-white shadow-md hover:shadow-lg transition-all duration-300 flex flex-col h-full transform hover:-translate-y-1"
            >
              <template #header>
                <div class="w-full flex justify-center items-center h-32 bg-gradient-to-br from-gray-100 to-gray-200 rounded-t-lg overflow-hidden group relative">
                  <img
                    :src="producto.imagen"
                    :alt="producto.nombre"
                    class="object-cover h-full w-full group-hover:scale-110 transition-transform duration-500"
                    @error="$event.target.src = 'https://via.placeholder.com/200x200/ef4444/ffffff?text=' + encodeURIComponent(producto.nombre.substring(0, 10))"
                  />
                  <div class="absolute top-2 right-2 bg-red-600 text-white px-2 py-1 rounded text-xs font-medium shadow-sm">
                    {{ producto.categoria }}
                  </div>
                </div>
              </template>
              
              <template #title>
                <span class="text-base font-semibold text-gray-800 leading-tight line-clamp-2">{{ producto.nombre }}</span>
              </template>
              
              <template #content>
                <div class="flex-grow">
                  <div class="text-gray-600 text-xs mb-3 line-clamp-2">
                    {{ producto.descripcion }}
                  </div>
                  <div class="text-lg font-bold text-green-600 mb-3">
                    ${{ producto.precio.toFixed(2) }}
                  </div>
                </div>
              </template>
              
              <template #footer>
                <div class="flex gap-2 mt-auto">
                  <Button
                    label="Agregar"
                    @click="comprarProducto(producto)"
                    class="!bg-red-600 !border-none !px-3 !py-1 !text-white !text-xs font-medium rounded hover:!bg-red-700 transition-all flex-1 shadow-sm"
                    size="small"
                  />
                  <Button
                    icon="pi pi-plus"
                    @click="comprarProducto(producto)"
                    class="!bg-red-600 !border-none !text-white hover:!bg-red-700 transition-all !w-8 !h-8 !p-0 shadow-sm"
                    size="small"
                  />
                </div>
              </template>
            </Card>
          </div>
          
          <!-- Mensaje cuando no hay productos -->
          <div v-if="filteredProducts.length === 0" class="text-center py-12 bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="text-gray-400 text-6xl mb-4">🔍</div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No se encontraron productos</h3>
            <p class="text-gray-500 mb-4">Intenta ajustar los filtros para ver más resultados</p>
            <button 
              @click="clearFilters"
              class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition shadow-sm"
            >
              Limpiar filtros
            </button>
          </div>
        </div>
      </div>
    </div>
  </Catalogo>
</template>

<style scoped>
/* Clase para limitar líneas de texto */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Estilos para el panel de filtros */
.filter-panel {
  background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
}

/* Estilos para checkboxes */
input[type="checkbox"]:checked {
  background-color: #dc2626;
  border-color: #dc2626;
}

/* Animaciones para los cards */
.product-card {
  transition: all 0.3s ease;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
}

/* Efectos de hover para las imágenes */
.product-image {
  transition: transform 0.5s ease;
}

.product-image:hover {
  transform: scale(1.1);
}

/* Estilos para los inputs de precio */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="number"] {
  -moz-appearance: textfield;
  appearance: textfield;
}

/* Responsive para el grid */
@media (max-width: 768px) {
  .filter-panel {
    width: 100%;
    margin-bottom: 1rem;
  }
}
</style>
