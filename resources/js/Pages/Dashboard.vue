<template>
  <div class="min-h-screen bg-gray-100 flex flex-col items-center justify-center py-12 px-4">
    <div class="max-w-md w-full bg-white shadow-lg rounded-lg p-8">
      <h2 class="text-3xl font-extrabold text-gray-900 mb-4">¡Bienvenido!</h2>
      <div class="mb-4 text-sm">
        <span class="text-blue-500">Estás autenticado en el dashboard usando</span> <span class="font-semibold text-indigo-600">Vue</span> e <span class="font-semibold text-indigo-600">Inertia</span>.
      </div>
      <div class="mt-6">
        <p class="text-gray-700">Este es el panel principal de tu aplicación. Puedes personalizarlo como desees.</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Dashboard',
}
</script>