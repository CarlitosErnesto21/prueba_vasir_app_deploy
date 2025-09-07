<script setup>
import Catalogo from '../Catalogo.vue'
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
    
    <div class="bg-gradient-to-br from-gray-50 via-blue-50/30 to-red-50/30 min-h-screen">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <!-- Header Professional -->
        <div class="mb-8 sm:mb-12">
          <div class="bg-gradient-to-br from-white via-blue-50 to-red-50 rounded-xl shadow-xl border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 via-red-500 to-blue-600 text-white text-center py-6 sm:py-8 md:py-10">
              <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-3 sm:mb-4">🛍️ Productos</h1>
              <p class="text-lg sm:text-xl md:text-2xl text-red-100 mb-4 px-4">Encuentra los mejores productos para tu viaje</p>
              <p class="text-base sm:text-lg md:text-xl text-white max-w-4xl mx-auto leading-relaxed px-4">
                Descubre nuestra amplia selección de productos de calidad para hacer tu experiencia de viaje única
              </p>
            </div>
          </div>
        </div>
        
        <!-- Barra de búsqueda -->
        <div class="mb-6 sm:mb-8">
          <div class="bg-gradient-to-br from-white via-blue-50 to-red-50 rounded-xl p-4 sm:p-6 shadow-xl border border-gray-200">
            <div class="relative">
              <input 
                v-model="searchTerm"
                type="text"
                placeholder="🔍 Buscar productos por nombre, descripción o categoría..."
                class="w-full px-4 py-3 sm:py-4 pl-12 border-2 border-gray-300 focus:border-red-500 focus:ring-4 focus:ring-red-200 rounded-xl focus:outline-none bg-white shadow-md transition-all duration-300 text-sm sm:text-base"
              />
              <i class="pi pi-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
            </div>
          </div>
        </div>

        <div class="flex gap-6 sm:gap-8">
          <!-- Panel de Filtros -->
          <div class="w-80 bg-gradient-to-br from-white via-blue-50 to-red-50 rounded-xl shadow-xl p-6 sm:p-8 h-fit border border-gray-200 hover:shadow-2xl transition-all duration-300">
            <div class="bg-gradient-to-r from-red-600 to-blue-600 text-white text-center py-4 sm:py-6 rounded-xl mb-6 sm:mb-8">
              <h3 class="text-lg sm:text-xl md:text-2xl font-bold flex items-center justify-center">
                <span class="text-xl sm:text-2xl mr-2">🔧</span>
                Filtros
              </h3>
            </div>
            
            <div class="flex justify-end mb-4">
              <button 
                @click="clearFilters"
                class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-1"
              >
                Limpiar filtros
              </button>
            </div>
          
          <!-- Estadísticas de precios -->
          <div class="mb-6">
            <div class="bg-gradient-to-br from-yellow-50 to-orange-50 border-2 border-yellow-200 rounded-xl p-4 sm:p-5 shadow-lg">
              <h5 class="text-sm sm:text-base font-bold bg-gradient-to-r from-yellow-600 to-orange-600 bg-clip-text text-transparent mb-3 flex items-center">
                <span class="mr-2">📊</span>
                Rango de precios
              </h5>
              <div class="text-xs sm:text-sm text-gray-700 space-y-2">
                <div class="flex justify-between items-center p-2 bg-white rounded-lg shadow-sm">
                  <span class="font-medium">Mínimo:</span>
                  <span class="font-bold text-green-600">${{ preciosStats.min.toFixed(2) }}</span>
                </div>
                <div class="flex justify-between items-center p-2 bg-white rounded-lg shadow-sm">
                  <span class="font-medium">Máximo:</span>
                  <span class="font-bold text-red-600">${{ preciosStats.max.toFixed(2) }}</span>
                </div>
                <div class="flex justify-between items-center p-2 bg-white rounded-lg shadow-sm">
                  <span class="font-medium">Promedio:</span>
                  <span class="font-bold text-blue-600">${{ preciosStats.promedio.toFixed(2) }}</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Filtro de Precio -->
          <div class="mb-6 sm:mb-8">
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl p-4 sm:p-5 shadow-lg">
              <h4 class="font-bold text-gray-800 mb-4 text-sm sm:text-base flex items-center">
                <span class="mr-2">💰</span>
                Filtrar por precio
              </h4>
              <div class="flex gap-2 items-end mb-3">
                <div class="flex-1">
                  <label class="text-xs sm:text-sm text-gray-600 font-medium mb-1 block">Mínimo</label>
                  <input 
                    v-model="minPrice"
                    type="number" 
                    :placeholder="`$${preciosStats.min.toFixed(2)}`"
                    class="w-full border-2 border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-lg px-3 py-2 text-sm focus:outline-none bg-white transition-all duration-300 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                    min="0"
                    step="0.01"
                    :max="preciosStats.max"
                  />
                </div>
                <div class="flex-1">
                  <label class="text-xs sm:text-sm text-gray-600 font-medium mb-1 block">Máximo</label>
                  <input 
                    v-model="maxPrice"
                    type="number" 
                    :placeholder="`$${preciosStats.max.toFixed(2)}`"
                    class="w-full border-2 border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-lg px-3 py-2 text-sm focus:outline-none bg-white transition-all duration-300 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                    min="0"
                    step="0.01"
                    :max="preciosStats.max"
                  />
                </div>
                <button 
                  @click="applyPriceFilter"
                  class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-1"
                >
                  Aplicar
                </button>
              </div>
            </div>
          </div>
          
          <!-- Filtro de Categorías -->
          <div>
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl p-4 sm:p-5 shadow-lg">
              <h4 class="font-bold text-gray-800 mb-4 text-sm sm:text-base flex items-center">
                <span class="mr-2">🏷️</span>
                Categorías 
                <span v-if="loadingCategories" class="text-xs text-gray-500 ml-2">(cargando...)</span>
              </h4>
              
              <div v-if="loadingCategories" class="space-y-3">
                <div v-for="i in 3" :key="i" class="animate-pulse">
                  <div class="h-10 bg-gray-200 rounded-lg"></div>
                </div>
              </div>
              
              <div v-else class="space-y-3">
                <label 
                  v-for="category in categoriasConConteo" 
                  :key="category.id"
                  class="flex items-center cursor-pointer hover:bg-white/60 p-3 rounded-lg transition-all duration-300 group shadow-sm hover:shadow-md transform hover:-translate-y-0.5"
                >
                  <input 
                    type="checkbox"
                    :checked="selectedCategories.includes(category.nombre)"
                    @change="toggleCategory(category.nombre)"
                    class="mr-3 accent-green-600 scale-110"
                  />
                  <span class="text-sm text-gray-700 flex-1 font-medium group-hover:text-green-700 transition-colors">{{ category.nombre }}</span>
                  <span class="text-xs text-white bg-gradient-to-r from-green-500 to-emerald-500 px-3 py-1 rounded-full font-bold shadow-sm">
                    {{ category.cantidad }}
                  </span>
                </label>
                
                <div v-if="categoriasConConteo.length === 0" class="text-center py-6 text-gray-500 text-sm bg-white rounded-lg shadow-sm">
                  <div class="text-2xl mb-2">📦</div>
                  No hay categorías disponibles
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Grid de Productos -->
        <div class="flex-1">
          <!-- Información de resultados -->
          <div class="mb-6">
            <div class="bg-gradient-to-br from-white via-blue-50 to-red-50 rounded-xl p-4 sm:p-6 shadow-xl border border-gray-200">
              <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg px-4 py-2 shadow-md">
                  <span class="text-sm sm:text-base font-bold">
                    📦 Mostrando {{ filteredProducts.length }} de {{ products.length }} productos
                  </span>
                </div>
                
                <div v-if="selectedCategories.length > 0 || minPrice || maxPrice || searchTerm" 
                     class="flex flex-wrap gap-2">
                  <span class="bg-gradient-to-r from-green-100 to-blue-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium shadow-sm border border-green-200">
                    ✅ Filtros activos: {{ 
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
            </div>
          </div>
          
          <!-- Loading state -->
          <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
            <div v-for="i in 8" :key="i" class="animate-pulse">
              <div class="bg-gradient-to-br from-white via-gray-50 to-gray-100 rounded-xl border-2 border-gray-200 p-4 sm:p-6 shadow-lg">
                <div class="h-32 bg-gradient-to-br from-gray-200 to-gray-300 rounded-lg mb-4"></div>
                <div class="h-4 bg-gradient-to-r from-gray-200 to-gray-300 rounded mb-3"></div>
                <div class="h-3 bg-gradient-to-r from-gray-200 to-gray-300 rounded mb-3 w-3/4"></div>
                <div class="h-6 bg-gradient-to-r from-green-200 to-green-300 rounded mb-4 w-1/2"></div>
                <div class="h-8 bg-gradient-to-r from-red-200 to-blue-200 rounded"></div>
              </div>
            </div>
          </div>
          
          <!-- Grid de productos -->
          <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
            <Card
              v-for="producto in filteredProducts"
              :key="producto.id"
              class="border-2 border-gray-200 bg-gradient-to-br from-white via-blue-50/30 to-red-50/30 shadow-lg hover:shadow-2xl transition-all duration-500 flex flex-col h-full transform hover:-translate-y-2 hover:border-red-300 group"
            >
              <template #header>
                <div class="w-full flex justify-center items-center h-32 bg-gradient-to-br from-gray-100 via-blue-100 to-red-100 rounded-t-lg overflow-hidden group relative">
                  <img
                    :src="producto.imagen"
                    :alt="producto.nombre"
                    class="object-cover h-full w-full group-hover:scale-110 transition-transform duration-700"
                    @error="handleImageError($event, producto)"
                  />
                  <div class="absolute top-2 right-2 bg-gradient-to-r from-red-600 to-red-700 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg border border-white/20">
                    {{ producto.categoria }}
                  </div>
                  <div class="absolute top-2 left-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg border border-white/20">
                    Stock: {{ producto.stock_actual }}
                  </div>
                  <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>
              </template>
              
              <template #title>
                <span class="text-base sm:text-lg font-bold text-gray-800 leading-tight line-clamp-2 group-hover:text-red-700 transition-colors duration-300">
                  {{ producto.nombre }}
                </span>
              </template>
              
              <template #content>
                <div class="flex-grow">
                  <div class="text-gray-600 text-xs sm:text-sm mb-4 line-clamp-2 leading-relaxed">
                    {{ producto.descripcion }}
                  </div>
                  <div class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent mb-4">
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
                    class="!bg-gradient-to-r !from-red-600 !to-red-700 hover:!from-red-700 hover:!to-red-800 !border-none !px-4 !py-2 !text-white !text-sm font-bold rounded-lg transition-all duration-300 flex-1 shadow-md hover:shadow-lg transform hover:-translate-y-1 disabled:!bg-gray-400 disabled:!from-gray-400 disabled:!to-gray-500"
                    size="small"
                  />
                  <Button
                    icon="pi pi-eye"
                    @click="verDetalles(producto)"
                    class="!bg-gradient-to-r !from-blue-600 !to-blue-700 hover:!from-blue-700 hover:!to-blue-800 !border-none !text-white transition-all duration-300 !w-10 !h-10 !p-0 shadow-md hover:shadow-lg transform hover:-translate-y-1 rounded-lg"
                    size="small"
                    v-tooltip.top="'Ver detalles'"
                  />
                </div>
              </template>
            </Card>
          </div>
          
          <!-- Mensaje cuando no hay productos -->
          <div v-if="!loading && filteredProducts.length === 0" class="text-center py-12">
            <div class="bg-gradient-to-br from-white via-blue-50 to-red-50 rounded-xl p-8 sm:p-12 shadow-xl border border-gray-200">
              <div class="w-20 h-20 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-full mx-auto mb-6 flex items-center justify-center shadow-lg">
                <span class="text-white text-4xl">🔍</span>
              </div>
              <h3 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-gray-700 to-gray-900 bg-clip-text text-transparent mb-4">
                No se encontraron productos
              </h3>
              <p class="text-gray-600 mb-6 text-sm sm:text-base leading-relaxed">
                <span v-if="products.length === 0">
                  No hay productos disponibles en este momento. Nuestro equipo está trabajando para actualizar el catálogo.
                </span>
                <span v-else>
                  Intenta ajustar los filtros o realizar una búsqueda diferente para ver más resultados.
                </span>
              </p>
              <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <button 
                  v-if="selectedCategories.length > 0 || minPrice || maxPrice || searchTerm"
                  @click="clearFilters"
                  class="w-full sm:w-auto bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-6 py-3 rounded-xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                >
                  <span class="mr-2">🔄</span>
                  Limpiar filtros
                </button>
                <button 
                  v-else
                  @click="recargarDatos"
                  class="w-full sm:w-auto bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                >
                  <span class="mr-2">🔄</span>
                  Recargar productos
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Catalogo>
</template>

<style scoped>
/* Sin CSS personalizado - Solo clases Tailwind */
</style>