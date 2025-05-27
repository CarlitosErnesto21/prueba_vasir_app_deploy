<template>
  <aside
    class="fixed top-0 left-0 h-full w-64 bg-white shadow-lg z-40 transition-transform"
    :class="{ '-translate-x-full': !isOpen, 'translate-x-0': isOpen }"
  >
    <div class="p-4 font-bold text-lg border-b">Menú</div>
    <nav class="p-4">
      <ul>
        <li class="mb-2"><a href="#" class="text-gray-700 hover:underline">Inicio</a></li>
        <li class="mb-2"><a href="#" class="text-gray-700 hover:underline">Productos</a></li>
        <li class="mb-2"><a href="#" class="text-gray-700 hover:underline">Usuarios</a></li>
      </ul>
    </nav>
    <button class="absolute top-2 right-2 text-gray-500" @click="$emit('close')">✕</button>
  </aside>
</template>

<script setup>
defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
});
</script>