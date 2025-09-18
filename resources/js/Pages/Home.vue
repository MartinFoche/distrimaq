<script setup>
import Authenticated from "@/Layouts/AuthenticatedLayout";
import "../../css/main.css";
import { usePage, useForm , router } from "@inertiajs/vue3";
import ProductCard from "@/Components/ProductCard.vue";

const { props } = usePage();
const products = props.products;
const form = useForm({
    category: props.filters?.category || "",
});

defineProps({
  categories: Array
})

function filterProducts() {
    router.get("/home", { category: form.category }, { 
    preserveState: false, // mantiene el estado de la página
    replace: true         // reemplaza la URL sin recargar
  });
}
</script>
<template>
    <Authenticated>
        <div class="form-group-cargar">
            <label for="categories">Categoría</label><br />
            <select v-model="form.category" class="select-cargar" @change="filterProducts"> 
                <option value="">
                    Todas las categorías
                </option>
                <option
                    v-for="category in categories"
                    :key="category.id"
                    :value="category.id"
                >
                    {{ category.name }}
                </option>
            </select>
        </div>
        <div class="product-grid">
            <ProductCard
                v-for="product in products"
                :key="product.id"
                :product="product"
            />
            <div v-if="products.length === 0" class="no-products">
                No se encontraron productos.
            </div>
        </div>
    </Authenticated>
</template>
