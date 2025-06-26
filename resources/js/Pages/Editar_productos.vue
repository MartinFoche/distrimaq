<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { onMounted, ref } from "vue";
import axios from "axios";
import { useForm } from "@inertiajs/vue3";

const search = ref("");
const product = ref([]);
const hasSearched = ref(false);
const loading = ref(false);
const archive = useForm({
    file: null,
});
let actualizados = ref(false);

function handleFileChange(event) {
    archive.file = event.target.files[0];
}

function importSubmit() {
    const formData = new FormData();
    formData.append("file", archive.file);
    axios
        .post("/import-products/update", formData)
        .then(() => {
            actualizados.value = true;
            archive.reset();
            archive.file = null;
            buscarProducto();
            setTimeout(() => {
                actualizados.value = false;
            }, 4000);
        })
        .catch((error) => {
            actualizados.value = false;
            if (
                error.response &&
                error.response.data &&
                error.response.data.message
            ) {
                alert("Error: " + error.response.data.message);
            } else {
                alert("Ocurrió un error al importar los productos.");
            }
        });
}

const eliminarProducto = async (id) => {
    if (!confirm("¿Estás seguro de que querés eliminar este producto?")) return;
    try {
        await axios.delete(`/editar_productos/delete/${id}`);
        // Actualizá la lista quitando el producto eliminado
        product.value = product.value.filter((item) => item.id !== id);
    } catch (error) {
        console.error("Error eliminando producto:", error);
        alert("No se pudo eliminar el producto.");
    }
};

onMounted(async () => {
    product.value = [];
    try {
        const response = await axios.get(`/editar_productos/search`, {
            params: { query: search.value },
        });
        product.value = response.data;
    } catch (error) {
        console.error("Error buscando producto:", error);
    }
});

const buscarProducto = async () => {
    product.value = [];
    loading.value = true;
    hasSearched.value = true;
    try {
        const response = await axios.get(`/editar_productos/search`, {
            params: { query: search.value },
        });
        product.value = response.data;
    } catch (error) {
        console.error("Error buscando producto:", error);
    } finally {
        loading.value = false;
    }
};

const productoEditando = ref(null); // null o el ID del producto editado

const formEditar = ref({
    id: null,
    sku: "",
    description: "",
    price: "",
    images: [],
});

function empezarEdicion(item) {
    productoEditando.value = item.id;
    productoEditando.value = item.id;
    console.log("item.images:");
    formEditar.value = {
        id: item.id,
        sku: item.sku,
        description: item.description,
        price: item.price,
        images:
            item.images?.map((img) =>
                typeof img === "string" ? img : img.url
            ) || [],
    };
}

const errorsEditar = ref({});

async function guardarEdicion() {
    try {
        await axios.put(`/editar_productos/update/${formEditar.value.id}`, {
            description: formEditar.value.description,
            price: formEditar.value.price,
            images: formEditar.value.images,
        });

        // Actualizar en la lista
        const index = product.value.findIndex(
            (p) => p.id === formEditar.value.id
        );
        if (index !== -1) {
            product.value[index] = { ...formEditar.value };
        }
        productoEditando.value = null;
    } catch (error) {
        console.error("Error actualizando producto:", error);
        if (error.response && error.response.data.errors) {
            errorsEditar.value = error.response.data.errors;
        }
    }
}
</script>
<template>
    <AuthenticatedLayout>
        <div class="form-container-cargar">
            <div class="importar-container">
                <h1 class="title-cargar">
                    Actualizar productos con EXCEL/CSV:
                </h1>
                <form @submit.prevent="importSubmit" class="importar-form">
                    <input
                        type="file"
                        name="file"
                        class="importar-input"
                        @change="handleFileChange"
                        accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                        required
                    />
                    <button class="form-button-cargar" type="submit">
                        Importar
                    </button>
                </form>
                <div
                    v-if="actualizados"
                    class="bg-green-100 text-green-500 p-4 rounded mb-4"
                >
                    Productos actualizados exitosamente.
                </div>
            </div>
        </div>
        <div>
            <div class="search-product">
                <h2>Buscar producto:</h2>
                <div class="input-container">
                    <input
                        class="input-editar"
                        v-model="search"
                        @keyup.enter="buscarProducto"
                        placeholder="SKU o nombre..."
                    />
                    <button class="button-buscar" @click="buscarProducto">
                        Buscar
                    </button>
                </div>
            </div>
            <div v-if="product.length > 0" class="result-edit">
                <h3 class="edit-title">Resultados:</h3>
                <div v-for="(item, index) in product" :key="index">
                    <div class="edit-data" v-if="productoEditando !== item.id">
                        <p><strong>SKU:</strong> {{ item.sku }}</p>
                        <p>
                            <strong>Descripción:</strong> {{ item.description }}
                        </p>
                        <p><strong>Precio:</strong> ${{ item.price }}</p>
                    </div>
                    <div v-else class="form-editar">
                        <p><strong>SKU:</strong> {{ formEditar.sku }}</p>
                        <input
                            v-model="formEditar.description"
                            placeholder="Descripción"
                        />
                        <input
                            v-model="formEditar.price"
                            type="number"
                            placeholder="Precio"
                        />
                        <div
                            v-for="(image, index) in formEditar.images"
                            :key="'edit-image-' + index"
                            class="form-group-cargar"
                        >
                            <label :for="'edit-image-' + index"
                                >Imagen {{ index + 1 }}</label
                            >
                            <div
                                style="
                                    display: flex;
                                    gap: 10px;
                                    align-items: center;
                                "
                            >
                                <input
                                    v-model="formEditar.images[index]"
                                    type="text"
                                    :id="'edit-image-' + index"
                                    placeholder="https://img1.jpg"
                                />
                                <img
                                    v-if="formEditar.images[index]"
                                    :src="formEditar.images[index]"
                                    alt="Preview"
                                    style="
                                        max-height: 50px;
                                        max-width: 50px;
                                        border: 1px solid #ccc;
                                        padding: 2px;
                                    "
                                />
                                <div
                                    v-if="errorsEditar[`images.${index}`]"
                                    class="text-red-500 text-sm"
                                >
                                    {{ errorsEditar[`images.${index}`][0] }}
                                </div>
                                <button
                                    @click.prevent="
                                        formEditar.images.splice(index, 1)
                                    "
                                    style="color: red"
                                >
                                    🗑️
                                </button>
                            </div>
                        </div>
                        <button @click.prevent="formEditar.images.push('')">
                            ➕ Añadir Imagen
                        </button>
                        <button @click="guardarEdicion">💾 Guardar</button>
                        <button @click="productoEditando = null">
                            ❌ Cancelar
                        </button>
                    </div>

                    <div class="buttons-edit-trash">
                        <button @click="eliminarProducto(item.id)">🗑</button>
                        <button @click="empezarEdicion(item)">✏</button>
                    </div>
                    <hr />
                </div>
            </div>
        </div>

        <p v-if="loading">Buscando...</p>
        <p v-if="hasSearched && !loading && product.length === 0">
            No se encontraron productos.
        </p>
    </AuthenticatedLayout>
</template>
