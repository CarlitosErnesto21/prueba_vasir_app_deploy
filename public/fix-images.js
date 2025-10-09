/**
 * Script para verificar y mostrar información sobre imágenes faltantes
 * Ejecutar en la consola del navegador para identificar problemas
 */

(() => {
  console.log('🔍 Verificando imágenes faltantes...');

  const images = document.querySelectorAll('img');
  const missingImages = [];

  images.forEach((img, index) => {
    // Verificar si la imagen no se cargó
    if (!img.complete || img.naturalHeight === 0) {
      missingImages.push({
        index,
        src: img.src,
        alt: img.alt,
        element: img
      });
    }
  });

  if (missingImages.length > 0) {
    console.warn(`⚠️ Se encontraron ${missingImages.length} imágenes con problemas:`);
    missingImages.forEach(({index, src, alt}) => {
      console.log(`  ${index + 1}. ${src} (${alt})`);
    });

    // Intentar arreglar automáticamente
    console.log('🔧 Intentando reparar imágenes...');
    missingImages.forEach(({element, alt}) => {
      const fallbackSrc = `data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='200'%3E%3Crect width='300' height='200' fill='%23ef4444'/%3E%3Ctext x='50%25' y='50%25' fill='white' text-anchor='middle' dy='.3em' font-family='Arial, sans-serif' font-size='14' font-weight='bold'%3E${encodeURIComponent(alt || 'Sin Imagen')}%3C/text%3E%3C/svg%3E`;
      element.src = fallbackSrc;
    });

  } else {
    console.log('✅ Todas las imágenes se cargaron correctamente');
  }

  // Verificar si hay errores de JavaScript
  console.log('🔍 Verificando errores JavaScript...');
  const originalConsoleError = console.error;
  let jsErrors = 0;

  console.error = function(...args) {
    jsErrors++;
    originalConsoleError.apply(console, args);
  };

  setTimeout(() => {
    console.log(`📊 Resumen: ${jsErrors} errores JavaScript encontrados`);
    console.error = originalConsoleError;
  }, 2000);
})();
