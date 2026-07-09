<script setup>
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
    <div class="container-carrito " v-if="carrito.length === 0">
      <p>El carrito está vacío.</p>
    </div>
    <div class="container-carrito" v-else>
        <h1 class="title-carrito">Carrito</h1>
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
            <div class="carrito-controllers">
                <div>
                    <h2 class="carrito-total"><b> Total: </b> $ {{ total }}</h2>
                </div>
                <div class="buttons-carrito">
                    <button class="boton-borrar-carrito" @click="clearCarrito">
                        Borrar Carrito
                    </button>
                    <button class="boton-enviar-carrito" @click="sendCarrito">
                        Enviar Pedido
                    </button>
                </div>

            </div>

        </div>

  </AuthenticatedLayout>
</template>
