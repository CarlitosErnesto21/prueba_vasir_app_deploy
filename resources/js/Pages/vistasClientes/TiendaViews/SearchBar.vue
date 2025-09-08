<script setup>
// Props del componente
const props = defineProps({
  searchTerm: {
    type: String,
    required: true
  },
  allFilteredProducts: {
    type: Array,
    required: true
  },
  selectedCategories: {
    type: Array,
    required: true
  },
  minPrice: {
    type: [String, Number],
    required: true
  },
  maxPrice: {
    type: [String, Number],
    required: true
  }
})

// Emits del componente
const emit = defineEmits([
  'update:searchTerm'
])

// Métodos
const updateSearchTerm = (value) => {
  emit('update:searchTerm', value)
}
</script>

<template>
  <!-- Información de resultados con buscador -->
  <div class="mb-6">
    <div class="bg-gradient-to-br from-white via-blue-50 to-red-50 rounded-xl p-4 sm:p-6 shadow-xl border border-gray-200">
      <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <!-- Buscador -->
        <div class="w-full lg:flex-1 lg:max-w-md">
          <IconField iconPosition="left">
            <InputIcon class="pi pi-search text-gray-400" />
            <InputText 
              :modelValue="searchTerm"
              @update:modelValue="updateSearchTerm"
              placeholder="Buscar productos..."
              class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-300 shadow-sm hover:shadow-md bg-white/90 font-medium"
            />
          </IconField>
        </div>
        
        <!-- Información de productos -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg px-4 py-2 shadow-md">
          <span class="text-sm sm:text-base font-bold">
            {{ allFilteredProducts.length }} producto{{ allFilteredProducts.length !== 1 ? 's' : '' }} encontrado{{ allFilteredProducts.length !== 1 ? 's' : '' }}
          </span>
        </div>
      </div>
        
      <div v-if="selectedCategories.length > 0 || minPrice || maxPrice || searchTerm" 
           class="flex flex-wrap gap-2 mt-4">
        <span class="bg-gradient-to-r from-green-100 to-blue-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium shadow-sm border border-green-200">
          ✅ Filtros activos: {{ 
            [
              searchTerm ? `Búsqueda: "${searchTerm}"` : '',
              selectedCategories.length > 0 ? `Categorías (${selectedCategories.length})` : '',
              minPrice ? `Min: $${minPrice}` : '',
              maxPrice ? `Max: $${maxPrice}` : ''
            ].filter(Boolean).join(', ') 
          }}
        </span>
      </div>
    </div>
  </div>
</template>
