<script setup>
import Header from "@/Components/Header.vue";
import "../../css/main.css";
import Footer from "@/Components/Footer.vue";
</script>
<template>
    <div  id="app">
        <Header />
        <main class="main-content">
            <slot />
        </main>
        <Footer />
    </div>
</template>
