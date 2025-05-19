<template>
    <div>
        <h1>Bienvenido a Laravel 12 y Vue 3</h1>
        <router-link to="/home" class="btn btn-success">Home</router-link>
        <router-link to="/producto" class="btn btn-warning">Producto</router-link>
        <hr/>
         <router-view></router-view>
    </div>
</template>