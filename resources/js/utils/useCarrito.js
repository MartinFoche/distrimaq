import { ref, computed } from "vue";

const carrito = ref([]);

function loadCarrito() {
  const items = [];
  for (let i = 0; i < localStorage.length; i++) {
    const key = localStorage.key(i);
    if (key.startsWith("carrito_") && !key.endsWith("_data")) {
      const cantidad = parseInt(localStorage.getItem(key), 10);
      const productData = localStorage.getItem(`${key}_data`);
      if (productData) {
        try {
          const product = JSON.parse(productData);
          if (product && cantidad > 0) {
            items.push({ ...product, cantidad });
          }
        } catch (e) {
          console.warn("Producto corrupto en localStorage", key, e);
        }
      }
    }
  }
  carrito.value = items;
}


function sendCarrito() {
  if (carrito.length === 0) {
    alert("El carrito está vacío.");
    return;
  }

  let mensaje = "Hola Distrimaq, les quería solicitar los siguientes productos:\n\n";
  carrito.value.forEach((item) => {
    mensaje += `${item.cantidad} - ${item.description}\n`;
  });
  mensaje += `\nTotal: $${total.value}\n\nMuchas gracias desde ya.`;

  const url = `https://wa.me/5493447498284?text=${encodeURIComponent(mensaje)}`;
  
  window.open(url, "_blank"); 
}

function clearCarrito() {
  // borrar todas las claves del carrito en localStorage
  for (let i = localStorage.length - 1; i >= 0; i--) {
    const key = localStorage.key(i);
    if (key.startsWith("carrito_")) {
      localStorage.removeItem(key);
    }
  }
  // vaciar el ref
  carrito.value = [];
} 

const total = computed(() =>
  carrito.value.reduce((acc, item) => acc + item.price * item.cantidad, 0)
);

// cargar al inicio
loadCarrito();

export function getCarrito() {
  return { carrito, total, loadCarrito, clearCarrito, sendCarrito };
}
