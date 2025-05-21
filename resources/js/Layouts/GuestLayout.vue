<template>
    <div class="flex min-h-screen items-center justify-center bg-gradient-to-b from-orange-300 to-orange-500 p-4 sm:p-6 md:p-12">
        <!-- Contenedor principal con imagen y formulario -->
        <div class="relative flex flex-col md:flex-row w-full max-w-3xl bg-orange-100 shadow-2xl rounded-2xl overflow-hidden"
            style="background-color: #F4EDDD; border-radius: 50px; border: solid #9e9e9e 1px;">
            
            <!-- Imagen al lado izquierdo (visible solo en md y lg) -->
            <div class="hidden md:flex w-2/4 items-center justify-center p-6">
                    <img src="../../../imagenes/logo.akc.png" class="max-h-[600px] w-full object-contain" alt="Logo"
                        style="margin-left: 30px;"/>
            </div>
            <!-- Contenedor del formulario -->
            <div class="w-full md:w-2/3 p-6 sm:p-8 md:p-10 shadow-2xl bg-orange-50 rounded-2xl" style="border-radius: 50px; border: solid #9e9e9e 1px;">
                <slot />
            </div>
        </div>
    </div>
</template>