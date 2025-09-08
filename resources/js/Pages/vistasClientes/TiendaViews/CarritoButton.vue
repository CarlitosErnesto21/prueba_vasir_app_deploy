<script setup>
import { useCarritoStore } from '@/stores/carrito'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faShoppingCart } from '@fortawesome/free-solid-svg-icons'

const carritoStore = useCarritoStore()
</script>

<template>
  <button 
    v-show="!carritoStore.isVisible"
    @click="carritoStore.toggleVisibility()"
    class="carrito-button"
    title="Ver carrito de compras"
  >
    <!-- Icono del carrito -->
    <div class="carrito-icon">
      <FontAwesomeIcon :icon="faShoppingCart" class="w-6 h-6" />
    </div>
    
    <!-- Badge con contador -->
    <Transition name="bounce">
      <span 
        v-if="carritoStore.itemCount > 0"
        class="carrito-badge"
      >
        {{ carritoStore.itemCount > 99 ? '99+' : carritoStore.itemCount }}
      </span>
    </Transition>

    <!-- Efecto de ripple -->
    <div class="ripple-effect"></div>
  </button>
</template>

<style scoped>
.carrito-button {
  position: fixed;
  bottom: 25px;
  right: 25px;
  z-index: 9999;
  overflow: hidden;
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  border: none;
  border-radius: 16px;
  padding: 12px;
  box-shadow: 
    0 4px 15px rgba(59, 130, 246, 0.3),
    0 0 0 1px rgba(255, 255, 255, 0.1) inset;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
  min-width: 56px;
  min-height: 56px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.carrito-button:hover {
  transform: translateY(-2px) scale(1.05);
  box-shadow: 
    0 8px 25px rgba(59, 130, 246, 0.4),
    0 0 0 1px rgba(255, 255, 255, 0.2) inset;
  background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
}

.carrito-button:active {
  transform: translateY(0) scale(0.98);
  transition-duration: 0.1s;
}

.carrito-icon {
  color: white;
  transition: all 0.3s ease;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
}

.carrito-button:hover .carrito-icon {
  transform: rotate(-5deg) scale(1.1);
}

.carrito-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  color: white;
  font-size: 0.75rem;
  font-weight: bold;
  min-width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  box-shadow: 
    0 2px 8px rgba(239, 68, 68, 0.4),
    0 0 0 2px white;
  animation: pulse-glow 2s infinite;
  padding: 0 6px;
}

.ripple-effect {
  position: absolute;
  inset: 0;
  border-radius: 50%;
  opacity: 0;
  pointer-events: none;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.6) 0%, transparent 70%);
  transform: scale(0);
  transition: all 0.6s ease-out;
}

.carrito-button:active .ripple-effect {
  animation: ripple 0.6s ease-out;
}

/* Animaciones */
@keyframes pulse-glow {
  0%, 100% {
    box-shadow: 
      0 2px 8px rgba(239, 68, 68, 0.4),
      0 0 0 2px white;
  }
  50% {
    box-shadow: 
      0 2px 12px rgba(239, 68, 68, 0.6),
      0 0 0 2px white,
      0 0 20px rgba(239, 68, 68, 0.3);
  }
}

@keyframes ripple {
  0% {
    transform: scale(0);
    opacity: 1;
  }
  100% {
    transform: scale(2);
    opacity: 0;
  }
}

/* Transiciones para el badge */
.bounce-enter-active {
  animation: bounceIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.bounce-leave-active {
  animation: bounceOut 0.3s ease-in;
}

@keyframes bounceIn {
  0% {
    transform: scale(0);
    opacity: 0;
  }
  50% {
    transform: scale(1.2);
    opacity: 0.8;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes bounceOut {
  0% {
    transform: scale(1);
    opacity: 1;
  }
  100% {
    transform: scale(0);
    opacity: 0;
  }
}

/* Responsive */
@media (max-width: 768px) {
  .carrito-button {
    min-width: 52px;
    min-height: 52px;
    padding: 12px;
    border-radius: 16px;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
  }
  
  .carrito-icon {
    width: 22px;
    height: 22px;
  }
  
  .carrito-badge {
    top: -6px;
    right: -6px;
    font-size: 0.75rem;
    min-width: 20px;
    height: 20px;
  }
}

@media (max-width: 480px) {
  .carrito-button {
    min-width: 50px;
    min-height: 50px;
    padding: 10px;
    border-radius: 15px;
    bottom: 15px;
    right: 15px;
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5);
    z-index: 9999;
  }
  
  .carrito-icon {
    width: 20px;
    height: 20px;
  }
  
  .carrito-badge {
    top: -4px;
    right: -4px;
    font-size: 0.7rem;
    min-width: 18px;
    height: 18px;
  }
}

/* Estados especiales */
.carrito-button:focus {
  outline: none;
  box-shadow: 
    0 8px 25px rgba(59, 130, 246, 0.4),
    0 0 0 3px rgba(59, 130, 246, 0.2);
}

.carrito-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
}

/* Efecto de carga/loading si lo necesitas */
.carrito-button.loading {
  pointer-events: none;
}

.carrito-button.loading .carrito-icon {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
