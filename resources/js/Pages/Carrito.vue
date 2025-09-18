<script setup lang="ts">
import { onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { getCarrito } from '@/utils/useCarrito';

const { carrito, total, loadCarrito, clearCarrito, sendCarrito } = getCarrito();
onMounted(() => {
  loadCarrito();
});

</script>

<template>
  <AuthenticatedLayout>
    <div v-if="carrito.length === 0">
      <p>El carrito está vacío.</p>
    </div>
    <div v-else>
        <div class="product-grid">
            <div v-for="item in carrito" :key="item.id" class="card-home">
                    <div class="carrito-detalles">
                        <img
                            v-if="item.images && item.images.length"
                            :src="item.images[0].url"
                            alt="Imagen del producto"
                            class="card-image"
                            />
                            <img
                            v-else
                            src="/images/no-imagen2.jpg"
                            alt="Imagen por defecto"
                            class="card-image"
                        />
                        <h2 class="carrito-nombre">{{ item.description }}</h2>
                        <p class="carrito-precio">Precio: $ {{ item.price }}</p>
                        <p class="carrito-cantidad">Cantidad: {{ item.cantidad }}</p>
                        <p class="carrito-subtotal">
                            Subtotal: $ {{ item.price * item.cantidad }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="carrito-total">
                <h2>Total: $ {{ total }}</h2>
            </div>
            <button class="boton-borrar-carrito" @click="clearCarrito">
                Borrar Carrito
            </button>
            <button class="boton-enviar-carrito" @click="sendCarrito">
                Enviar Pedido
            </button>
        </div>
      
  </AuthenticatedLayout>
</template>
