<script setup>
import Catalogo from '../Catalogo.vue'
import Card from 'primevue/card'
import Button from 'primevue/button'
import Toast from 'primevue/toast'
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useToast } from 'primevue/usetoast'

const toast = useToast()

// 📊 Estados reactivos
const products = ref([])
const categories = ref([])
const loading = ref(true)
const loadingCategories = ref(true)

// 🔍 Filtros
const selectedCategories = ref([])
const minPrice = ref('')
const maxPrice = ref('')
const searchTerm = ref('')

// 📊 Cargar datos al montar el componente
onMounted(async () => {
  await Promise.all([
    cargarProductos(),
    cargarCategorias()
  ])
})

// 🚀 Función para cargar productos desde la API
const cargarProductos = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/productos')
    
    // Mapear los datos para que coincidan con la estructura esperada
    products.value = (response.data.data || response.data || []).map(producto => ({
      id: producto.id,
      nombre: producto.nombre,
      descripcion: producto.descripcion,
      precio: parseFloat(producto.precio),
      stock_actual: producto.stock_actual,
      categoria: producto.categoria?.nombre || 'Sin categoría',
      categoria_id: producto.categoria_id,
      // Manejar imágenes
      imagen: producto.imagenes && producto.imagenes.length > 0 
        ? `/images/productos/${producto.imagenes[0].nombre}`
        : '/images/productos/default.jpg',
      imagenes: producto.imagenes || []
    })).filter(producto => producto.stock_actual > 0) // Solo productos en stock
    
    console.log('Productos cargados:', products.value.length)
  } catch (error) {
    console.error('Error al cargar productos:', error)
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'No se pudieron cargar los productos',
      life: 3000
    })
  } finally {
    loading.value = false
  }
}

// 🏷️ Función para cargar categorías desde la API
const cargarCategorias = async () => {
  try {
    loadingCategories.value = true
    const response = await axios.get('/api/categorias-productos')
    
    categories.value = (response.data.data || response.data || []).map(categoria => ({
      id: categoria.id,
      nombre: categoria.nombre,
      // Contar productos por categoría
      cantidad: 0 // Se calculará en el computed
    }))
    
    console.log('Categorías cargadas:', categories.value.length)
  } catch (error) {
    console.error('Error al cargar categorías:', error)
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'No se pudieron cargar las categorías',
      life: 3000
    })
  } finally {
    loadingCategories.value = false
  }
}

// 🔍 Categorías con conteo de productos
const categoriasConConteo = computed(() => {
  return categories.value.map(categoria => ({
    ...categoria,
    cantidad: products.value.filter(producto => 
      producto.categoria === categoria.nombre
    ).length
  })).filter(categoria => categoria.cantidad > 0) // Solo mostrar categorías con productos
})

// 🔍 Productos filtrados
const filteredProducts = computed(() => {
  let filtered = [...products.value]
  
  // Filtrar por término de búsqueda
  if (searchTerm.value) {
    const search = searchTerm.value.toLowerCase()
    filtered = filtered.filter(product => 
      product.nombre.toLowerCase().includes(search) ||
      product.descripcion.toLowerCase().includes(search) ||
      product.categoria.toLowerCase().includes(search)
    )
  }
  
  // Filtrar por categorías seleccionadas
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

// 📊 Estadísticas de precios para ayudar al usuario
const preciosStats = computed(() => {
  if (products.value.length === 0) return { min: 0, max: 0, promedio: 0 }
  
  const precios = products.value.map(p => p.precio)
  return {
    min: Math.min(...precios),
    max: Math.max(...precios),
    promedio: precios.reduce((a, b) => a + b, 0) / precios.length
  }
})

// 🔧 Funciones para filtros
const toggleCategory = (categoryName) => {
  const index = selectedCategories.value.indexOf(categoryName)
  if (index > -1) {
    selectedCategories.value.splice(index, 1)
  } else {
    selectedCategories.value.push(categoryName)
  }
}

const clearFilters = () => {
  selectedCategories.value = []
  minPrice.value = ''
  maxPrice.value = ''
  searchTerm.value = ''
  
  toast.add({
    severity: 'info',
    summary: 'Filtros limpiados',
    detail: 'Se han removido todos los filtros',
    life: 2000
  })
}

const applyPriceFilter = () => {
  // Los filtros se aplican automáticamente por la reactividad
  toast.add({
    severity: 'success',
    summary: 'Filtros aplicados',
    detail: `Mostrando ${filteredProducts.value.length} productos`,
    life: 2000
  })
}

// 🛒 Funciones para los botones
const comprarProducto = (producto) => {
  if (producto.stock_actual <= 0) {
    toast.add({
      severity: 'warn',
      summary: 'Sin stock',
      detail: `El producto "${producto.nombre}" no está disponible`,
      life: 3000
    })
    return
  }

  // Aquí podrías integrar con un carrito de compras real
  toast.add({
    severity: 'success',
    summary: '¡Agregado al carrito!',
    detail: `"${producto.nombre}" se agregó al carrito`,
    life: 3000
  })
  
  console.log('Producto agregado al carrito:', producto)
}

const verDetalles = (producto) => {
  // Aquí podrías navegar a una página de detalles del producto
  toast.add({
    severity: 'info',
    summary: 'Detalles del producto',
    detail: `${producto.nombre} - $${producto.precio.toFixed(2)} - ${producto.categoria} - Stock: ${producto.stock_actual}`,
    life: 5000
  })
}

// 🖼️ Función para manejar errores de imagen
const handleImageError = (event, producto) => {
  // Fallback a imagen por defecto con placeholder personalizado
  event.target.src = `https://via.placeholder.com/300x200/ef4444/ffffff?text=${encodeURIComponent(producto.nombre.substring(0, 15))}`
}

// 🔄 Función para recargar datos (solo para el botón de estado vacío)
const recargarDatos = async () => {
  await Promise.all([
    cargarProductos(),
    cargarCategorias()
  ])
  
  toast.add({
    severity: 'success',
    summary: 'Datos actualizados',
    detail: 'La información se ha actualizado correctamente',
    life: 2000
  })
}
</script>

<template>
  <Catalogo>
    <!-- Toast para notificaciones -->
    <Toast class="z-[9999]" />
    
    <div class="p-4 bg-gray-50 min-h-screen">
      <!-- Encabezado simplificado -->
      <div class="mb-4">
        <h1 class="text-2xl font-bold mb-2 text-red-700">🛍️ Productos</h1>
        <p class="text-gray-600">Encuentra los mejores productos para tu viaje</p>
      </div>
      
      <!-- Barra de búsqueda -->
      <div class="mb-6">
        <div class="relative">
          <input 
            v-model="searchTerm"
            type="text"
            placeholder="🔍 Buscar productos por nombre, descripción o categoría..."
            class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:border-red-500 focus:outline-none bg-white shadow-sm"
          />
          <i class="pi pi-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>
      </div>

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
          
          <!-- Estadísticas de precios -->
          <div class="mb-4 p-3 bg-gray-50 rounded-lg border">
            <h5 class="text-sm font-medium text-gray-700 mb-2">Rango de precios</h5>
            <div class="text-xs text-gray-600 space-y-1">
              <div>Mínimo: ${{ preciosStats.min.toFixed(2) }}</div>
              <div>Máximo: ${{ preciosStats.max.toFixed(2) }}</div>
              <div>Promedio: ${{ preciosStats.promedio.toFixed(2) }}</div>
            </div>
          </div>
          
          <!-- Filtro de Precio -->
          <div class="mb-6">
            <h4 class="font-semibold text-gray-700 mb-3">Filtrar por precio</h4>
            <div class="flex gap-2 items-center mb-3">
              <div class="flex-1">
                <label class="text-sm text-gray-600">Min</label>
                <input 
                  v-model="minPrice"
                  type="number" 
                  :placeholder="`$${preciosStats.min.toFixed(2)}`"
                  class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:border-red-500 focus:outline-none bg-gray-50 focus:bg-white [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                  min="0"
                  step="0.01"
                  :max="preciosStats.max"
                />
              </div>
              <div class="flex-1">
                <label class="text-sm text-gray-600">Max</label>
                <input 
                  v-model="maxPrice"
                  type="number" 
                  :placeholder="`$${preciosStats.max.toFixed(2)}`"
                  class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:border-red-500 focus:outline-none bg-gray-50 focus:bg-white [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                  min="0"
                  step="0.01"
                  :max="preciosStats.max"
                />
              </div>
              <button 
                @click="applyPriceFilter"
                class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition mt-5 shadow-sm"
              >
                Aplicar
              </button>
            </div>
          </div>
          
          <!-- Filtro de Categorías -->
          <div>
            <h4 class="font-semibold text-gray-700 mb-3">
              Categorías 
              <span v-if="loadingCategories" class="text-xs text-gray-500">(cargando...)</span>
            </h4>
            
            <div v-if="loadingCategories" class="space-y-2">
              <div v-for="i in 3" :key="i" class="animate-pulse">
                <div class="h-8 bg-gray-200 rounded"></div>
              </div>
            </div>
            
            <div v-else class="space-y-2">
              <label 
                v-for="category in categoriasConConteo" 
                :key="category.id"
                class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded transition"
              >
                <input 
                  type="checkbox"
                  :checked="selectedCategories.includes(category.nombre)"
                  @change="toggleCategory(category.nombre)"
                  class="mr-3 accent-red-600"
                />
                <span class="text-sm text-gray-700 flex-1">{{ category.nombre }}</span>
                <span class="text-xs text-gray-500 bg-gray-200 px-2 py-1 rounded-full">
                  {{ category.cantidad }}
                </span>
              </label>
              
              <div v-if="categoriasConConteo.length === 0" class="text-center py-4 text-gray-500 text-sm">
                No hay categorías disponibles
              </div>
            </div>
          </div>
        </div>
        
        <!-- Grid de Productos -->
        <div class="flex-1">
          <!-- Información de resultados -->
          <div class="mb-4 flex justify-between items-center">
            <div class="text-sm text-gray-600 bg-white rounded-lg px-4 py-2 shadow-sm border border-gray-200">
              Mostrando {{ filteredProducts.length }} de {{ products.length }} productos
            </div>
            
            <div v-if="selectedCategories.length > 0 || minPrice || maxPrice || searchTerm" 
                 class="text-xs text-gray-500">
              <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">
                Filtros activos: {{ 
                  [
                    selectedCategories.length > 0 ? `${selectedCategories.length} categorías` : '',
                    minPrice ? 'precio mín.' : '',
                    maxPrice ? 'precio máx.' : '',
                    searchTerm ? 'búsqueda' : ''
                  ].filter(Boolean).join(', ') 
                }}
              </span>
            </div>
          </div>
          
          <!-- Loading state -->
          <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <div v-for="i in 8" :key="i" class="animate-pulse">
              <div class="bg-white rounded-lg border border-gray-300 p-4">
                <div class="h-32 bg-gray-200 rounded mb-4"></div>
                <div class="h-4 bg-gray-200 rounded mb-2"></div>
                <div class="h-3 bg-gray-200 rounded mb-2"></div>
                <div class="h-6 bg-gray-200 rounded mb-4"></div>
                <div class="h-8 bg-gray-200 rounded"></div>
              </div>
            </div>
          </div>
          
          <!-- Grid de productos -->
          <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
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
                    @error="handleImageError($event, producto)"
                  />
                  <div class="absolute top-2 right-2 bg-red-600 text-white px-2 py-1 rounded text-xs font-medium shadow-sm">
                    {{ producto.categoria }}
                  </div>
                  <div class="absolute top-2 left-2 bg-green-600 text-white px-2 py-1 rounded text-xs font-medium shadow-sm">
                    Stock: {{ producto.stock_actual }}
                  </div>
                </div>
              </template>
              
              <template #title>
                <span class="text-base font-semibold text-gray-800 leading-tight line-clamp-2">
                  {{ producto.nombre }}
                </span>
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
                    :disabled="producto.stock_actual <= 0"
                    class="!bg-red-600 !border-none !px-3 !py-1 !text-white !text-xs font-medium rounded hover:!bg-red-700 transition-all flex-1 shadow-sm disabled:!bg-gray-400"
                    size="small"
                  />
                  <Button
                    icon="pi pi-eye"
                    @click="verDetalles(producto)"
                    class="!bg-blue-600 !border-none !text-white hover:!bg-blue-700 transition-all !w-8 !h-8 !p-0 shadow-sm"
                    size="small"
                    v-tooltip.top="'Ver detalles'"
                  />
                </div>
              </template>
            </Card>
          </div>
          
          <!-- Mensaje cuando no hay productos -->
          <div v-if="!loading && filteredProducts.length === 0" class="text-center py-12 bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="text-gray-400 text-6xl mb-4">🔍</div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No se encontraron productos</h3>
            <p class="text-gray-500 mb-4">
              <span v-if="products.length === 0">
                No hay productos disponibles en este momento
              </span>
              <span v-else>
                Intenta ajustar los filtros para ver más resultados
              </span>
            </p>
            <button 
              v-if="selectedCategories.length > 0 || minPrice || maxPrice || searchTerm"
              @click="clearFilters"
              class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition shadow-sm"
            >
              Limpiar filtros
            </button>
            <button 
              v-else
              @click="recargarDatos"
              class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition shadow-sm"
            >
              Recargar productos
            </button>
          </div>
        </div>
      </div>
    </div>
  </Catalogo>
</template>

<style scoped>
/* Sin CSS personalizado - Solo clases Tailwind */
</style>