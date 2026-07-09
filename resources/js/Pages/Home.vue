<script setup>
import Authenticated from "@/Layouts/AuthenticatedLayout";
import "../../css/main.css";
import { usePage, useForm , router } from "@inertiajs/vue3";
import { computed, watch, ref } from "vue";
import ProductCard from "@/Components/ProductCard.vue";

const page = usePage();
const products = computed(() => page.props.products.data);
const search = ref(page.props.filters?.search || "");
const form = useForm({
    category: page.props.filters?.category || "",
});
const actualPage = ref(page.props.products.current_page);
const lastPage = computed(() => page.props.products.last_page);

watch(() => form.category, () => {
    filterProducts();
});

defineProps({
  categories: Array
})

function filterProducts() {
    actualPage.value = 1;
    router.get("/home", { category: form.category, search: search.value, page: actualPage.vlaue }, {
    preserveState: true,
    replace: true,
  });
}
function changePage() {
    router.get("/home", {
        category: form.category,
        search: search.value,
        page: actualPage.value
    }, {
        preserveState: true,
        replace: true,
    });
}
</script>
<template>
    <Authenticated>
        <div class="search-container">
            <div class="form-group-cargar-home">
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
            <div class="search-container-name">
                <div class="input-container">
                        <input
                            class="input-editar"
                            v-model="search"
                            @keyup.enter="filterProducts"
                            placeholder="SKU o nombre..."
                        />
                        <button class="button-buscar" @click="filterProducts">
                            Buscar
                        </button>
                    </div>
            </div>

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
                <button v-if="actualPage > 1"  @click="actualPage--; changePage()"  class="pagination-button">
                    <
                </button>
                    {{ actualPage }}
                <button v-if="actualPage < lastPage" @click="actualPage++; changePage()"  class="pagination-button">
                    >
                </button>
            </div>
        </div>
        <div v-if="products.length === 0" class="no-products">
            No se encontraron productos.
        </div>

    </Authenticated>
</template>
