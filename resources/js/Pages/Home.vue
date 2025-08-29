<script setup>
import Authenticated from "@/Layouts/AuthenticatedLayout";
import "../../css/main.css";
import { usePage } from "@inertiajs/vue3";
import ProductCard from "@/Components/ProductCard.vue";

const { props } = usePage();
const products = props.products;
</script>
<template>
    <Authenticated>
        <div class="product-grid">
            <ProductCard
                v-for="product in products"
                :key="product.id"
                :product="product"
            />
        </div>
    </Authenticated>
</template>
