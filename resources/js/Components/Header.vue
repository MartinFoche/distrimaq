<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import "../../css/main.css";
const { auth } = usePage().props;
</script>
<template>
    <header class="header">
        <div class="buttons-header">
            <div class="carrito-container">   
                <Link class="carrito-header" href="/carrito">
                    <img
                        class="img-carrito"
                        src="/images/carrito-distrimaq.png"
                        alt="Carrito de compras"
                    />
                </Link>
            </div> 
            <div class="logo-container">
                <Link class="header-title-distrimaq" href="/home">
                    <div class="corte-logo">
                        <img
                            class="img-header"
                            src="/images/logo-distrimaq.png"
                            alt="Distrimaq"
                        />
                    </div>
                </Link>
            </div>
            
            <div class="header-load-wrapper">
                <Link
                    class="header-button-products"
                    v-if="auth.user"
                    href="/cargar_productos"
                >
                    Cargar/Editar
                </Link>
            </div>
        </div>
    </header>
</template>
