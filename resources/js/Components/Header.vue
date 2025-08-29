<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import "../../css/main.css";
const { auth } = usePage().props;
</script>
<template>
    <header class="header">
        <Link class="header-title-distrimaq" href="/home">
            <div class="corte-logo">
                <img
                    class="img-header"
                    src="/images/Distrimaq.png"
                    alt="Distrimaq"
                />
            </div>
        </Link>
        <div class="header-load-wrapper">
            <Link
                class="header-button-products"
                v-if="auth.user"
                href="/editar_productos"
            >
                Editar productos
            </Link>
            <Link
                class="header-button-products"
                v-if="auth.user"
                href="/cargar_productos"
            >
                Cargar productos
            </Link>
        </div>
    </header>
</template>
