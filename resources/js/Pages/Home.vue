<script setup>
import Authenticated from "@/Layouts/AuthenticatedLayout";
import "../../css/main.css";
import { usePage, useForm , router } from "@inertiajs/vue3";
import { watch } from "vue";
import ProductCard from "@/Components/ProductCard.vue";

const { props } = usePage();
const products = props.products.data;
const form = useForm({
    category: props.filters?.category || "",
});
let actualPage = props.products.current_page;
let lastPage = props.products.last_page;

watch(() => form.category, () => {
    actualPage = 1;
    filterProducts();
});

defineProps({
  categories: Array
})

function filterProducts() {
    router.get("/home", { category: form.category, page: actualPage }, { 
    preserveState: false, 
    replace: true        
  });
}
</script>
<template>
    <Authenticated>
        <div class="form-group-cargar">
            <select v-model="form.category" class="select-cargar" @change="filterProducts"> 
                <option value="">
                    Todas las categorías
                </option>
                <option
                    v-for="category in categories"
                    :key="category.id"
                    :value="category.id"
                >
                    {{ category.name.charAt(0).toUpperCase() + category.name.slice(1).toLowerCase() }}
                </option>
            </select>
        </div>
        <div class="product-grid">
            <ProductCard
                v-for="product in products"
                :key="product.id"
                :product="product"
            />
        </div>
        <div style="display: flex; justify-content: center;">
            <div class="pagination-container" v-if="products.length != 0 && lastPage > 1">
                <button v-if="actualPage > 1" @click="actualPage--; filterProducts()" class="pagination-button">
                    <
                </button>
                    {{ actualPage }}
                <button v-if="actualPage < lastPage" @click="actualPage++; filterProducts()" class="pagination-button">
                    >
                </button>
            </div>
        </div>
        <div v-if="products.length === 0" class="no-products">
            No se encontraron productos.
        </div>

    </Authenticated>
</template>
