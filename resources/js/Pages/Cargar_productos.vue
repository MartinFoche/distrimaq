<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
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

function handleFileChange(event) {
    archive.file = event.target.files[0];
    cargados.value = false;
}

const categories = [
    { id: 0, name: "Caramelos" },
    { id: 1, name: "Chupetines" },
    { id: 2, name: "Chocolates" },
    { id: 3, name: "Gomitas" },
    { id: 4, name: "Galletitas" },
    { id: 5, name: "Malvaviscos" },
    { id: 6, name: "Alfajores" },
    { id: 7, name: "Barritas" },
    { id: 8, name: "Pastillas" },
    { id: 9, name: "Turrones" },
    { id: 10, name: "Medicamentos" },
];
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

function getImageError(index) {
    return form.errors[`images.${index}`] || "";
}
</script>
<template>
    <AuthenticatedLayout>
        <div class="form-container-cargar">
            <h1 class="title-cargar">Agregar producto</h1>

            <form @submit.prevent="submit">
                <div class="form-group-cargar">
                    <label for="description">Descripción</label>
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
                    <label for="categories">Categoría</label><br />
                    <select v-model="form.category" class="select-cargar">
                        <option disabled value="">
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
                    <label for="sku">SKU</label>
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
                    <label for="price">Precio</label>
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
                    <label :for="'image-' + index"
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
                    @click.prevent="form.images.push('')"
                    style="margin-top: 10px"
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
                <h1 class="title-cargar">Importar productos con EXCEL/CSV:</h1>
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
    </AuthenticatedLayout>
</template>
