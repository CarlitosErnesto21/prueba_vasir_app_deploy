<template>
  <div>
    <h1 class="text-2xl font-bold">Categorías</h1>
    <ul>
      <li v-for="categoria in categorias" :key="categoria.id">
        {{ categoria.nombre }} - {{ categoria.descripcion }}
      </li>
    </ul>
  </div>
</template>

<script setup>
defineProps({
  categorias: Array,
});
</script>
