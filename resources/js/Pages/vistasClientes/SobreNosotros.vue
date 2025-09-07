<script setup>
import Catalogo from '../Catalogo.vue';
import HeaderSection from './SobreNosotrosViews/HeaderSection.vue';
import ValoresSection from './SobreNosotrosViews/ValoresSection.vue';
import EquipoSection from './SobreNosotrosViews/EquipoSection.vue';
import InstalacionesSection from './SobreNosotrosViews/InstalacionesSection.vue';
import PorQueElegirnosSection from './SobreNosotrosViews/PorQueElegirnosSection.vue';
import ContactoSection from './SobreNosotrosViews/ContactoSection.vue';

const props = defineProps({
    siteSettings: {
        type: Object,
        default: () => ({})
    },
    companyValues: {
        type: Array,
        default: () => []
    }
});
</script>
<template>
    <Catalogo>
        <!-- Sección Header con Estadísticas, Misión y Visión -->
        <HeaderSection :siteSettings="props.siteSettings" />
        
        <!-- Valores de la empresa -->
        <ValoresSection :companyValues="props.companyValues" />
        
        <!-- Nuestro Equipo -->
        <EquipoSection />
        
        <!-- Nuestras Instalaciones -->
        <InstalacionesSection />
        
        <!-- ¿Por qué elegir Vasir? -->
        <PorQueElegirnosSection />
        
        <!-- Encuéntranos/Contacto -->
        <ContactoSection />
    </Catalogo>
</template>