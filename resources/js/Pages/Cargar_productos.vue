<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import axios from "axios";

const form = useForm({
    description: "",
    sku: "",
    price: "",
    images: [""],
    category: "",
});
let productoCargado = ref(false);
const archive = useForm({
    file: null,
});
const loading = ref(false);
const product = ref([]);
const search = ref("");
const hasSearched = ref(false);

defineProps({
  categories: Array
})

function handleFileChange(event) {
    archive.file = event.target.files[0];
    cargados.value = false;
}

function submit() {
    form.post("/cargar_productos", {
        onSuccess: () => {
            form.reset();
            form.clearErrors();
            productoCargado.value = true;
            setTimeout(() => {
                productoCargado.value = false;
            }, 4000);
        },
    });
}

let errores = ref([]);
let cargados = ref(false);

function importSubmit() {
    const formData = new FormData();
    formData.append("file", archive.file);

    axios
        .post("/import-products", formData)
        .then((response) => {
            alert(response.data.message);
            archive.reset();
            errores.value = [];
            cargados.value = true;
            setTimeout(() => {
                cargados.value = false;
            }, 4000);
        })
        .catch((error) => {
            cargados.value = false;
            if (error.response && error.response.status === 422) {
                errores.value = error.response.data.errors;
            } else {
                errores.value = ["Ocurrió un error inesperado."];
            }
        });
}

const eliminarProducto = async (id) => {
    if (!confirm("¿Estás seguro de que querés eliminar este producto?")) return;
    try {
        await axios.delete(`/cargar_productos/delete/${id}`);
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
        const response = await axios.get(`cargar_productos/search`, {
            params: { query: search.value },
        });
        product.value = response.data;
    } catch (error) {
        console.error("Error buscando producto:", error);
    }
});

function getImageError(index) {
    return form.errors[`images.${index}`] || "";
}

const buscarProducto = async () => {
    product.value = [];
    loading.value = true;
    hasSearched.value = true;
    try {
        const response = await axios.get(`cargar_productos/search`, {
            params: { query: search.value },
        });
        product.value = response.data;
    } catch (error) {
        console.error("Error buscando producto:", error);
    } finally {
        loading.value = false;
    }
};
const productoEditando = ref(null); 

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
        await axios.put(`cargar_productos/update/${formEditar.value.id}`, {
            description: formEditar.value.description,
            price: formEditar.value.price,
            images: formEditar.value.images,
        });

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
        <div class="general-container-cargar-editar">
            <div class="cargar-container">
                <div class="form-container-cargar">
                    <h1 class="title-cargar">Agregar producto</h1>

                    <form @submit.prevent="submit">
                        <div class="form-group-cargar">
                            <label class="label-cargar"for="description">Descripción</label>
                            <input
                                class="form-input-cargar"
                                v-model="form.description"
                                type="text"
                                id="description"
                                required
                                placeholder="Descripción.."
                            />
                        </div>

                        <div class="form-group-cargar">
                            <label class="label-cargar" for="categories">Categoría</label><br />
                            <select v-model="form.category" class="select-cargar">
                                <option value="">
                                    Seleccioná una categoría
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

                        <div class="form-group-cargar">
                            <label class="label-cargar" for="sku">SKU</label>
                            <input
                                class="form-input-cargar"
                                v-model="form.sku"
                                type="text"
                                id="sku"
                                required
                                placeholder="SKU.."
                            />
                            <div v-if="form.errors.sku" class="text-red-500 text-sm">
                                {{ form.errors.sku }}
                            </div>
                        </div>

                        <div class="form-group-cargar">
                            <label class="label-cargar" for="price">Precio</label>
                            <div class="input-with-symbol">
                                <span class="currency-symbol">$</span>
                                <input
                                    class="form-input-cargar-precio"
                                    v-model="form.price"
                                    type="number"
                                    step="0.01"
                                    inputmode="decimal"
                                    id="price"
                                    required
                                    placeholder="1234.56"
                                />
                            </div>
                        </div>

                        <div
                            v-for="(image, index) in form.images"
                            :key="index"
                            class="form-group-cargar"
                        >
                            <label class="label-cargar" :for="'image-' + index"
                                >Imagen {{ index + 1 }}</label
                            >
                            <div style="display: flex; gap: 10px; align-items: center">
                                <input
                                    class="form-input-cargar"
                                    v-model="form.images[index]"
                                    type="text"
                                    :id="'image-' + index"
                                    placeholder="https://img1.jpg"
                                />
                                <img
                                    v-if="form.images[index]"
                                    :src="form.images[index]"
                                    alt="Preview"
                                    style="
                                        max-height: 50px;
                                        border: 1px solid #ccc;
                                        padding: 2px;
                                    "
                                />
                                <button
                                    class="trash-cargar"
                                    @click.prevent="form.images.splice(index, 1)"
                                    style="color: red"
                                >
                                    🗑️
                                </button>
                            </div>
                            <div
                                v-if="getImageError(index)"
                                class="text-red-500 text-sm"
                            >
                                {{ getImageError(index) }}
                            </div>
                        </div>
                        <button
                            class="add-image-cargar"
                            @click.prevent="form.images.push('')"
                            style="margin-top: 10px; color: rgb(172, 3, 3);"
                        >
                            ➕ Agregar imagen
                        </button>
                        <br />
                        <div class="button-container-cargar">
                            <button class="form-button-cargar" type="submit">
                                Cargar
                            </button>
                        </div>
                        <div
                            v-if="productoCargado"
                            class="bg-green-100 text-green-500 p-4 rounded mb-4"
                        >
                            Producto cargado exitosamente.
                        </div>
                    </form>
                </div>
                <div class="form-container-cargar">
                    <div class="importar-container">
                        <h1 class="title-cargar">Importar/editar productos con EXCEL/CSV:</h1>
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
                            v-if="cargados"
                            class="bg-green-100 text-green-500 p-4 rounded mb-4"
                        >
                            Productos cargados exitosamente.
                        </div>
                        <div
                            v-if="errores.length"
                            class="bg-red-100 text-red-700 p-4 rounded mb-4"
                        >
                            <ul>
                                <li v-for="(error, index) in errores" :key="index">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="editar-container">
                <h1 class="title-editar">Editar producto</h1>
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
                <div v-if="product?.length > 0" class="result-edit">
                    <h2 class="edit-title">Resultados:</h2>
                    <div v-for="(item, index) in product" :key="index" class="edit-data">
                        <div  v-if="productoEditando !== item.id" class="info-edit">
                            <img
                                v-if="item.images?.length" 
                                :src="item.images[0].url"
                                alt="Imagen del producto"
                                class="card-image-edit"
                            />
                            <img
                                v-else
                                src="/images/no-imagen2.jpg"
                                alt="Imagen por defecto"
                                class="card-image-edit"
                            />
                            <div>
                                <p><strong>SKU:</strong> {{ item.sku }}</p>
                                <p>
                                    <strong>Descripción:</strong> {{ item.description }}
                                </p>
                                <p><strong>Precio:</strong> ${{ item.price }}</p>
                            </div>
                            
                        </div>
                        <div v-else>
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
                            <button class="button-edit-trash" @click="eliminarProducto(item.id)">🗑</button>
                            <button class="button-edit-trash" @click="empezarEdicion(item)">✏</button>
                        </div>
                    </div>
                </div>
                <p v-if="loading">Buscando...</p>
                <p v-if="hasSearched && !loading && product.length === 0">
                    No se encontraron productos.
                </p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
