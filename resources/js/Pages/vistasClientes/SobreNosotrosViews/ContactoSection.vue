<template>
  <!-- Información de contacto y ubicación -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="bg-gradient-to-br from-white via-blue-50 to-red-50 rounded-xl p-6 sm:p-8 md:p-10 shadow-xl border border-gray-200">
        <div class="bg-gradient-to-r from-red-600 to-blue-600 text-white text-center py-4 sm:py-6 rounded-xl mb-6 sm:mb-8">
          <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold">📍 Encuéntranos</h2>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 mb-6 sm:mb-8">
          <div class="space-y-4 sm:space-y-6">
            <div class="bg-white rounded-xl p-4 sm:p-6 shadow-lg border border-gray-200 hover:border-red-200 transition-all duration-300 hover:shadow-xl">
              <div class="flex items-start">
                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center mr-4 shadow-lg">
                  <span class="text-white text-xl">📍</span>
                </div>
                <div class="flex-1">
                  <h3 class="text-lg sm:text-xl font-bold bg-gradient-to-r from-red-600 to-blue-600 bg-clip-text text-transparent mb-2">Dirección</h3>
                  <p class="text-sm sm:text-base text-gray-700 leading-relaxed">2a Calle Oriente casa #12, Chalatenango, El Salvador</p>
                </div>
              </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 sm:p-6 shadow-lg border border-gray-200 hover:border-blue-200 transition-all duration-300 hover:shadow-xl">
              <div class="flex items-start">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-4 shadow-lg">
                  <span class="text-white text-xl">⏰</span>
                </div>
                <div class="flex-1">
                  <h3 class="text-lg sm:text-xl font-bold bg-gradient-to-r from-blue-600 to-red-600 bg-clip-text text-transparent mb-2">Horario de Atención</h3>
                  <p class="text-sm sm:text-base text-gray-700 leading-relaxed">Lunes a Viernes: 8:00 AM - 4:00 PM</p>
                  <p class="text-sm sm:text-base text-gray-700 leading-relaxed">Sábados: 8:00 AM - 12:00 PM</p>
                </div>
              </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 sm:p-6 shadow-lg border border-gray-200 hover:border-red-200 transition-all duration-300 hover:shadow-xl">
              <div class="flex items-start">
                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-blue-500 rounded-full flex items-center justify-center mr-4 shadow-lg">
                  <span class="text-white text-xl">📞</span>
                </div>
                <div class="flex-1">
                  <h3 class="text-lg sm:text-xl font-bold bg-gradient-to-r from-red-600 to-blue-600 bg-clip-text text-transparent mb-2">Contacto</h3>
                  <p class="text-sm sm:text-base text-gray-700 mb-2">¿Listo para tu próxima aventura?</p>
                  <a href="/contactos" class="inline-flex items-center gap-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-4 py-2 rounded-lg font-semibold transition-all duration-300 shadow-md hover:shadow-lg text-sm sm:text-base">
                    <span>📧</span>
                    Contáctanos aquí
                  </a>
                </div>
              </div>
            </div>
          </div>
          
          <div class="bg-gradient-to-br from-red-50 to-blue-50 rounded-xl p-6 sm:p-8 border border-red-200 shadow-lg">
            <div class="text-center mb-4 sm:mb-6">
              <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-blue-500 rounded-full mx-auto mb-4 flex items-center justify-center shadow-lg">
                <span class="text-white text-2xl">🎉</span>
              </div>
              <h3 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-red-600 to-blue-600 bg-clip-text text-transparent mb-3">¡Únete a la familia VASIR!</h3>
              <p class="text-sm sm:text-base text-gray-700 leading-relaxed mb-4 sm:mb-6">
                Más que una agencia de viajes, somos tu compañero de aventuras. 
                Permítenos ser parte de tus mejores recuerdos.
              </p>
            </div>
            
            <div class="space-y-3 sm:space-y-4">
              <a 
                href="/paquetes" 
                class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 shadow-md hover:shadow-lg text-sm sm:text-base transform hover:-translate-y-1"
              >
                <span>🌟</span>
                Ver Nuestros Paquetes
              </a>
              <a 
                href="/tours" 
                class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 shadow-md hover:shadow-lg text-sm sm:text-base transform hover:-translate-y-1"
              >
                <span>🗺️</span>
                Explorar Tours
              </a>
            </div>
          </div>
        </div>
        
        <!-- Mapa -->
        <div class="mt-6 sm:mt-8 rounded-xl overflow-hidden shadow-lg border-2 border-gray-200">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d241.91319397188843!2d-88.9363812!3d14.0410515!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8f636570efc5e09d%3A0xe884d67df04d7ff5!2sVASIR!5e0!3m2!1ses-419!2ssv!4v1749418509387!5m2!1ses-419!2ssv"
            width="100%"
            height="400"
            style="border:0;"
            allowfullscreen
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="Ubicación VASIR"
            class="w-full"
          >
          </iframe>
        </div>
      </div>
    </div>
</template>
