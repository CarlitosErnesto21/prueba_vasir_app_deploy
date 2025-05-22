<template>
  <div class="min-h-screen flex bg-red-200">
    <!-- Sidebar -->
    <aside class="w-56 bg-red-500 shadow-lg flex flex-col">
      <div class="h-16 flex items-center justify-center border-b">
        <span class="font-bold text-lg text-indigo-700">Catálogo</span>
      </div>
      <nav class="flex-1 px-4 py-6">
        <ul class="space-y-2">
          <li>
            <a href="/dashboard" class="flex items-center px-3 py-2 rounded hover:bg-indigo-100 text-gray-700">
              <span class="pi pi-home mr-2"></span> Dashboard
            </a>
          </li>
          <li>
            <a href="/catalogos/MetodosPago" class="flex items-center px-3 py-2 rounded hover:bg-indigo-100 text-gray-700">
              <span class="pi pi-credit-card mr-2"></span> Métodos de Pago
            </a>
          </li>
          <!-- Agrega más enlaces aquí -->
        </ul>
      </nav>
      <div class="p-4 border-t">
        <span class="text-xs text-black">© {{ new Date().getFullYear() }} Agencia Vasir</span>
      </div>
    </aside>

    <!-- Main content -->
    <main class="flex-1 p-8">
      <h1 class="text-2xl font-bold mb-6 text-indigo-800">Bienvenido al Catálogo</h1>
      <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-700">
          Selecciona una opción del menú lateral para gestionar los catálogos de tu aplicación.
        </p>
      </div>
      <Dashboard />
    </main>
  </div>
</template>

<script setup>
import Dashboard from './Dashboard.vue';

// Si usas PrimeVue para los íconos, asegúrate de tener primeicons en tu proyecto
</script>