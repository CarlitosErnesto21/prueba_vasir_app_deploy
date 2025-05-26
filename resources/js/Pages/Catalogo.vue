<template>
    <header class="bg-gradient-to-r from-white to-white text-black shadow-md fixed top-0 left-0 w-full z-50">
        <div class="px-6 py-3 flex justify-between items-center">
            <!-- Botón menú hamburguesa SOLO en móvil -->
            <button @click="isSidebarOpen = !isSidebarOpen"
                class="block md:hidden text-black mr-3"
                style="font-size: 150%;">
                <FontAwesomeIcon :icon="faList"/>
            </button>
            <div class="text-xl font-semibold text-black">
                <Link :href="route('dashboard')" class="text-xl font-semibold text-black flex items-center cursor-pointer select-none">
                    VAS
                    <img src="../../../imagenes/logovasir.png" class="w-5 h-5 inline-block align-middle" />
                    R
                </Link>
            </div>
            <!--Datos de la sesion-->
            <div class="flex items-center space-x-4">
                <div class="relative group">
                    <button @click="login" class="text-black text-xl" title="Usuario">
                        <FontAwesomeIcon :icon="faUser"/>
                    </button>
                </div>
                <div class="relative group">
                    <button @click="logout" class="text-black text-xl" title="Cerrar sesión">
                        <FontAwesomeIcon :icon="faDoorOpen"/>
                    </button>
                </div>
            </div>
        </div>
    </header>
</template>