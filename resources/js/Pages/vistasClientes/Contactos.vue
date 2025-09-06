<script setup>
import Catalogo from '../Catalogo.vue'
import { ref, computed } from 'vue'
import { useToast } from 'primevue/usetoast'

// Inicializar toast
const toast = useToast()

const nombre = ref('')
const email = ref('')
const mensaje = ref('')
const busquedaFAQ = ref('')

// Estado para controlar qué FAQ está abierta
const faqAbierta = ref(null)

// Preguntas frecuentes organizadas por categorías
const preguntasFrecuentes = ref([
  {
    categoria: '🏢 Servicios Generales',
    preguntas: [
      {
        id: 1,
        pregunta: '¿Qué tipo de experiencias turísticas ofrece VASIR?',
        respuesta: 'Ofrecemos tours culturales, de naturaleza, gastronómicos y de aventura en El Salvador, con un enfoque sostenible y auténtico. Nuestros itinerarios buscan que vivás el país desde adentro, conectando con su gente, sus tradiciones y su sabor.'
      },
      {
        id: 2,
        pregunta: '¿VASIR solo ofrece tours o también transporte privado?',
        respuesta: 'Además de nuestros tours completos, también brindamos servicio de transporte privado con chofer para grupos o viajeros individuales, tanto dentro como fuera de El Salvador. Este servicio se adapta a tu itinerario y necesidades.'
      },
      {
        id: 9,
        pregunta: '¿Qué otros servicios ofrecen?',
        respuesta: 'Además de tours y transporte, ofrecemos venta de boletos aéreos, reservas de hospedaje, trámites de visa americana, organización de eventos corporativos, viajes educativos y experiencias para grupos especiales.'
      }
    ]
  },
  {
    categoria: '📍 Ubicación y Logística',
    preguntas: [
      {
        id: 3,
        pregunta: '¿Dónde están ubicados y desde dónde salen los tours?',
        respuesta: 'Nuestra base está en Chalatenango, El Salvador, pero operamos salidas desde San Salvador y otros puntos del país según la ruta y el grupo. También podemos coordinar recogidas en aeropuertos y hoteles.'
      },
      {
        id: 7,
        pregunta: '¿Cómo puedo reservar?',
        respuesta: 'Puedes reservar directamente desde nuestra página web, por WhatsApp o redes sociales.'
      },
      {
        id: 8,
        pregunta: '¿Atienden a viajeros internacionales?',
        respuesta: 'Sí. Nuestros tours están diseñados tanto para visitantes locales como internacionales, y los ofrecemos en español e inglés. Si necesitas otro idioma, podemos coordinarlo.'
      }
    ]
  },
  {
    categoria: '💳 Pagos y Precios',
    preguntas: [
      {
        id: 4,
        pregunta: '¿Qué métodos de pago aceptan?',
        respuesta: 'Aceptamos pagos en efectivo, transferencia bancaria, depósito en cuenta, pagos con tarjeta (Visa, Mastercard, American Express, Diners Club, Discover Network) y plataformas electrónicas como PayPal. Para clientes internacionales recomendamos PayPal o tarjeta para mayor facilidad.'
      },
      {
        id: 11,
        pregunta: '¿Qué precio tiene el trámite de Visa Americana?',
        respuesta: '✅ Asesoría $15.00\n✅ Paquete básico (llenado del formulario y programación de cita) $55.00\n✅ Paquete full (llenado de formulario, programación de cita, pago en Banco, asesoría personalizada y seguimiento) $80.00\n\nAparte de esto, lo que se cancela a la Embajada es de $185.00'
      }
    ]
  },
  {
    categoria: '🎯 Personalización y Destinos',
    preguntas: [
      {
        id: 5,
        pregunta: '¿Puedo personalizar mi viaje?',
        respuesta: 'Sí. Diseñamos experiencias a tu medida: tours de un día, paquetes de varios días, viajes en pareja, familiares o corporativos, con actividades adaptadas a tus intereses.'
      },
      {
        id: 6,
        pregunta: '¿Ofrecen viajes internacionales o conexiones con otros países?',
        respuesta: 'Sí. Además de destinos en El Salvador, podemos coordinar experiencias hacia cualquier parte del mundo, incluyendo transporte, guías y actividades en el destino.'
      },
      {
        id: 10,
        pregunta: '¿Puedo reservar para Hoteles Decameron?',
        respuesta: 'Claro que sí, somos una Agencia autorizada por Hoteles Decameron.'
      }
    ]
  }
])

// Información de contacto
const contactoInfo = ref([
  {
    icono: '📍',
    titulo: 'Dirección',
    contenido: '2a Calle Oriente casa #12, Chalatenango, El Salvador',
    enlace: null
  },
  {
    icono: '⏰',
    titulo: 'Horarios',
    contenido: 'Lunes a Viernes: 8:00 AM - 4:00 PM\nSábados: 8:00 AM - 12:00 PM',
    enlace: null
  },
  {
    icono: '📱',
    titulo: 'WhatsApp',
    contenido: '+503 1234-5678',
    enlace: 'https://wa.me/50312345678'
  },
  {
    icono: '📧',
    titulo: 'Email',
    contenido: 'info@vasir.com',
    enlace: 'mailto:info@vasir.com'
  }
])

// Funciones
function enviarFormulario(e) {
  e.preventDefault()
  toast.add({
    severity: 'success',
    summary: 'Mensaje enviado',
    detail: 'Mensaje enviado correctamente. Te responderemos pronto.',
    life: 5000
  })
  nombre.value = ''
  email.value = ''
  mensaje.value = ''
}

function toggleFAQ(id) {
  faqAbierta.value = faqAbierta.value === id ? null : id
}

// Computed para filtrar FAQs
const faqsFiltradas = computed(() => {
  if (!busquedaFAQ.value) return preguntasFrecuentes.value
  
  const termino = busquedaFAQ.value.toLowerCase()
  return preguntasFrecuentes.value.map(categoria => ({
    ...categoria,
    preguntas: categoria.preguntas.filter(p => 
      p.pregunta.toLowerCase().includes(termino) || 
      p.respuesta.toLowerCase().includes(termino)
    )
  })).filter(categoria => categoria.preguntas.length > 0)
})
</script>

<template>
  <Catalogo>
    <Toast />
    <div class="p-4 bg-gray-50 min-h-screen">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
          <h1 class="text-4xl font-bold mb-4 text-red-700">📞 Contáctanos</h1>
          <p class="text-lg text-gray-600 mb-2">¿Tienes dudas? ¡Estamos aquí para ayudarte!</p>
          <p class="text-sm text-gray-500">Encuentra respuestas rápidas o envíanos un mensaje</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Columna izquierda: FAQs -->
          <div class="space-y-6">
            <!-- Preguntas Frecuentes -->
            <div class="bg-white rounded-xl p-6 shadow-md border border-gray-200">
              <h2 class="text-2xl font-bold text-red-700 mb-6 flex items-center">
                <span class="text-3xl mr-3">❓</span>
                Preguntas Frecuentes
              </h2>
              
              <!-- Buscador de FAQs -->
              <div class="mb-6">
                <InputText
                  v-model="busquedaFAQ"
                  placeholder="Buscar en preguntas frecuentes..."
                  class="w-full border border-gray-300 focus:border-red-600 focus:ring-2 focus:ring-red-200 rounded-lg px-4 py-3"
                />
              </div>

              <!-- Lista de FAQs por categoría -->
              <div class="space-y-4">
                <div v-for="categoria in faqsFiltradas" :key="categoria.categoria" class="space-y-2">
                  <h3 class="text-lg font-bold text-gray-800 border-b border-gray-200 pb-2">
                    {{ categoria.categoria }}
                  </h3>
                  
                  <div v-for="faq in categoria.preguntas" :key="faq.id" class="border border-gray-200 rounded-lg overflow-hidden">
                    <button
                      @click="toggleFAQ(faq.id)"
                      class="w-full text-left p-4 bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center"
                    >
                      <span class="font-medium text-gray-800 pr-4">{{ faq.pregunta }}</span>
                      <span class="text-red-600 text-xl font-bold flex-shrink-0">
                        {{ faqAbierta === faq.id ? '−' : '+' }}
                      </span>
                    </button>
                    
                    <div v-if="faqAbierta === faq.id" class="p-4 bg-white border-t border-gray-200">
                      <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ faq.respuesta }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Mensaje cuando no hay resultados -->
              <div v-if="faqsFiltradas.length === 0" class="text-center py-8">
                <div class="text-4xl mb-2">🔍</div>
                <p class="text-gray-500">No se encontraron preguntas que coincidan con tu búsqueda.</p>
                <button @click="busquedaFAQ = ''" class="text-red-600 hover:underline mt-2">
                  Ver todas las preguntas
                </button>
              </div>
            </div>

            <!-- Información de contacto -->
            <div class="bg-white rounded-xl p-6 shadow-md border border-gray-200">
              <h3 class="text-xl font-bold text-red-700 mb-4 flex items-center">
                <span class="text-2xl mr-2">📋</span>
                Información de Contacto
              </h3>
              
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div v-for="info in contactoInfo" :key="info.titulo" class="flex items-start space-x-3">
                  <span class="text-2xl">{{ info.icono }}</span>
                  <div>
                    <h4 class="font-semibold text-gray-800">{{ info.titulo }}</h4>
                    <div v-if="info.enlace">
                      <a :href="info.enlace" target="_blank" class="text-red-600 hover:underline text-sm">
                        {{ info.contenido }}
                      </a>
                    </div>
                    <div v-else class="text-gray-600 text-sm whitespace-pre-line">
                      {{ info.contenido }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Columna derecha: Formulario de contacto -->
          <div class="space-y-6">
            <!-- Formulario -->
            <div class="bg-white rounded-xl p-6 shadow-md border border-gray-200">
              <h2 class="text-2xl font-bold text-red-700 mb-6 flex items-center">
                <span class="text-3xl mr-3">✉️</span>
                Envíanos un Mensaje
              </h2>
              
              <form class="space-y-5" @submit="enviarFormulario">
                <div>
                  <label class="block mb-2 font-semibold text-gray-700">Nombre completo</label>
                  <InputText 
                    v-model="nombre" 
                    class="w-full border border-gray-300 focus:border-red-600 focus:ring-2 focus:ring-red-200 rounded-lg px-4 py-3" 
                    placeholder="Tu nombre completo" 
                    required
                  />
                </div>
                
                <div>
                  <label class="block mb-2 font-semibold text-gray-700">Correo electrónico</label>
                  <InputText 
                    v-model="email" 
                    type="email" 
                    class="w-full border border-gray-300 focus:border-red-600 focus:ring-2 focus:ring-red-200 rounded-lg px-4 py-3" 
                    placeholder="tu@email.com" 
                    required
                  />
                </div>
                
                <div>
                  <label class="block mb-2 font-semibold text-gray-700">Mensaje</label>
                  <Textarea 
                    v-model="mensaje" 
                    class="w-full border border-gray-300 focus:border-red-600 focus:ring-2 focus:ring-red-200 rounded-lg px-4 py-3" 
                    rows="5" 
                    placeholder="Escribe tu mensaje aquí... Cuéntanos sobre el tour que te interesa, fechas, número de personas, etc." 
                    autoResize 
                    required
                  />
                </div>
                
                <Button 
                  type="submit" 
                  label="Enviar Mensaje" 
                  class="!bg-red-600 !border-none !px-6 !py-3 !text-white font-semibold rounded-lg shadow-md hover:!bg-red-700 transition-all w-full" 
                />
              </form>
            </div>

            <!-- Métodos de contacto directo -->
            <div class="bg-white rounded-xl p-6 shadow-md border border-gray-200">
              <h3 class="text-xl font-bold text-red-700 mb-4 flex items-center">
                <span class="text-2xl mr-2">🚀</span>
                Contacto Directo
              </h3>
              
              <div class="space-y-3">
                <a 
                  href="https://wa.me/50312345678" 
                  target="_blank"
                  class="flex items-center p-3 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors"
                >
                  <span class="text-2xl mr-3">💬</span>
                  <div>
                    <p class="font-semibold text-green-700">WhatsApp</p>
                    <p class="text-sm text-green-600">Respuesta inmediata</p>
                  </div>
                </a>
                
                <a 
                  href="mailto:info@vasir.com"
                  class="flex items-center p-3 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors"
                >
                  <span class="text-2xl mr-3">📧</span>
                  <div>
                    <p class="font-semibold text-blue-700">Email</p>
                    <p class="text-sm text-blue-600">info@vasir.com</p>
                  </div>
                </a>
                
                <div class="flex items-center p-3 bg-red-50 border border-red-200 rounded-lg">
                  <span class="text-2xl mr-3">📱</span>
                  <div>
                    <p class="font-semibold text-red-700">Redes Sociales</p>
                    <p class="text-sm text-red-600">Síguenos en Facebook, Instagram y TikTok</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Mapa -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
              <div class="p-4 bg-red-50 border-b border-red-200">
                <h3 class="text-lg font-bold text-red-700 flex items-center">
                  <span class="text-xl mr-2">📍</span>
                  Nuestra Ubicación
                </h3>
              </div>
              <iframe
                src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d241.91319397188843!2d-88.9363812!3d14.0410515!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8f636570efc5e09d%3A0xe884d67df04d7ff5!2sVASIR!5e0!3m2!1ses-419!2ssv!4v1749418509387!5m2!1ses-419!2ssv"
                width="100%"
                height="250"
                style="border:0;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                title="Ubicación VASIR"
              >
              </iframe>
            </div>
          </div>
        </div>

        <!-- CTA Final -->
        <div class="mt-12 bg-white rounded-xl p-8 shadow-md border border-gray-200 text-center">
          <h2 class="text-2xl font-bold text-red-700 mb-4">¿No encontraste lo que buscabas?</h2>
          <p class="text-gray-600 mb-6">
            Nuestro equipo está disponible para responder cualquier pregunta específica sobre nuestros tours y servicios.
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a 
              href="https://wa.me/50312345678" 
              target="_blank"
              class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors shadow-md inline-flex items-center justify-center"
            >
              <span class="mr-2">💬</span>
              Chatear por WhatsApp
            </a>
            <a 
              href="/sobre-nosotros" 
              class="bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors shadow-md inline-flex items-center justify-center"
            >
              <span class="mr-2">🏢</span>
              Conocer más sobre VASIR
            </a>
          </div>
        </div>
      </div>
    </div>
  </Catalogo>
</template>