<script setup>
import { ref, onMounted, watch, computed } from "vue";

const props = defineProps({
  product: { type: Object, required: true },
});
// cantidad del producto actual
const cantidad = ref(0);

// clave única para cada producto (podés usar el id)
const product = props.product;

const key = computed(() => {
  if (!product) return null;
  const id = product.id ?? product._id ?? product.product_id ?? product.uuid;
  if (id == null) {
    console.warn("Producto sin campo id conocido:", product);
    return null;
  }
  return `carrito_${id}`;
});

// al montar, ver si ya había algo en localStorage
onMounted(() => {
  if (!key.value) return;
  const saved = localStorage.getItem(key.value);
  if (saved) cantidad.value = parseInt(saved, 10);
});

// cada vez que cambia la cantidad, se guarda en localStorage
watch(cantidad, (newValue) => {
  if (!key.value) return;

  if (newValue > 0) {
    // guardamos la cantidad
    localStorage.setItem(key.value, String(newValue));

    // guardamos también los datos del producto
    localStorage.setItem(`${key.value}_data`, JSON.stringify(product));
  } else {
    localStorage.removeItem(key.value);
    localStorage.removeItem(`${key.value}_data`);
  }
});

// funciones para sumar/restar
function aumentar() {
  if (cantidad.value < 99) cantidad.value++;
}

function disminuir() {
  if (cantidad.value > 0) {
    cantidad.value--;
  }
}
</script>

<template>
    <div class="card-home">
        <img
            v-if="product.images?.length" 
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
            <h3 class="nombre-producto">{{ product.description }}</h3>
            <p class="precio-producto">
                $ {{ product.price }}
            </p>
        </div>
        <div class="card-footer">
            <button class="boton-carrito-product-card" @click="disminuir">
                -
            </button>
            <span class="cantidad-product-card">{{ cantidad }}</span>
            <button class="boton-carrito-product-card" @click="aumentar">
                +
            </button>
        </div>
    </div>
    
</template>
