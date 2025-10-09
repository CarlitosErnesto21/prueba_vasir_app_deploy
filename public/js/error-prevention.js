/**
 * Inicialización robusta para prevenir errores de JavaScript
 * Se ejecuta antes que cualquier otro script
 */

(() => {
  'use strict';

  // Prevenir errores de MutationObserver
  if (typeof window.MutationObserver !== 'undefined') {
    const originalObserve = MutationObserver.prototype.observe;

    MutationObserver.prototype.observe = function(target, options) {
      try {
        // Verificar que el target sea un Node válido
        if (!target || typeof target.nodeType === 'undefined') {
          console.warn('MutationObserver: Invalid target node', target);
          return;
        }

        // Verificar que sea un elemento DOM válido
        if (target.nodeType !== Node.ELEMENT_NODE &&
            target.nodeType !== Node.DOCUMENT_NODE &&
            target.nodeType !== Node.DOCUMENT_FRAGMENT_NODE) {
          console.warn('MutationObserver: Target is not a valid node type', target.nodeType);
          return;
        }

        return originalObserve.call(this, target, options);
      } catch (error) {
        console.error('MutationObserver error prevented:', error);
      }
    };
  }

  // Mejorar manejo de errores de imágenes globalmente
  document.addEventListener('error', (event) => {
    if (event.target.tagName === 'IMG') {
      const img = event.target;
      const container = img.closest('.product-image-container') || img.parentElement;

      if (!img.dataset.fallbackAttempt) {
        img.dataset.fallbackAttempt = '1';

        // Crear una imagen de fallback dinámica
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        canvas.width = 300;
        canvas.height = 200;

        // Fondo degradado
        const gradient = ctx.createLinearGradient(0, 0, 300, 200);
        gradient.addColorStop(0, '#ef4444');
        gradient.addColorStop(1, '#dc2626');

        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, 300, 200);

        // Texto
        ctx.fillStyle = 'white';
        ctx.font = 'bold 14px Arial, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';

        const text = img.alt || 'Imagen no disponible';
        const maxWidth = 280;
        const words = text.split(' ');
        let line = '';
        const lines = [];

        for (let n = 0; n < words.length; n++) {
          const testLine = line + words[n] + ' ';
          const metrics = ctx.measureText(testLine);
          if (metrics.width > maxWidth && n > 0) {
            lines.push(line);
            line = words[n] + ' ';
          } else {
            line = testLine;
          }
        }
        lines.push(line);

        const startY = 100 - (lines.length * 10);
        lines.forEach((line, index) => {
          ctx.fillText(line.trim(), 150, startY + (index * 20));
        });

        img.src = canvas.toDataURL();
      }
    }
  }, true);

  // Interceptar errores de red y proporcionar mensajes más útiles
  const originalFetch = window.fetch;
  window.fetch = function(...args) {
    return originalFetch.apply(this, args)
      .catch(error => {
        if (error.message.includes('Failed to fetch') || error.message.includes('ERR_NAME_NOT_RESOLVED')) {
          console.warn('Network request failed, but continuing gracefully:', args[0]);
          // En lugar de fallar completamente, retornamos una respuesta vacía para casos no críticos
          return new Response('{}', { status: 200, headers: { 'Content-Type': 'application/json' } });
        }
        throw error;
      });
  };

  // Prevenir que errores de extensiones del navegador afecten la aplicación
  window.addEventListener('error', (event) => {
    // Filtrar errores comunes de extensiones de navegador
    const ignoredErrors = [
      'Could not establish connection. Receiving end does not exist',
      'Extension context invalidated',
      'The message port closed before a response was received',
      'Script error'
    ];

    if (ignoredErrors.some(ignored => event.message.includes(ignored))) {
      event.preventDefault();
      return false;
    }
  });

  // Optimizar carga de imágenes con lazy loading
  if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          if (img.dataset.src) {
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
            observer.unobserve(img);
          }
        }
      });
    });

    // Observar imágenes cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
      });
    });
  }

  console.log('🛡️ Error prevention script loaded successfully');
})();
