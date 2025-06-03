<script setup>
import Header from "@/Components/Header.vue";
import "../../css/main.css";
</script>
<template>
    <div>
        <Header />
        <main>
            <slot />
        </main>
    </div>
</template>
