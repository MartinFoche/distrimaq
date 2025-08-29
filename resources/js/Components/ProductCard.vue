<script setup>
defineProps({
    product: Object,
});
</script>

<template>
    <div class="card-home">
        <img
            v-if="product.images.length"
            :src="product.images[0].url"
            alt="Imagen del producto"
            class="card-image"
        />
        <img
            v-else
            src="/images/no-imagen2.jpg"
            alt="Imagen por defecto"
            class="card-image"
        />
        <h2 class="category-home">
            {{
                product.categories && product.categories.length > 0
                    ? product.categories[0].name
                    : "Sin Categoría"
            }}
        </h2>
        <div class="card-container">
            <h3>{{ product.description }}</h3>
            <p>
                <strong>$ {{ product.price }}</strong>
            </p>
        </div>
    </div>
</template>
