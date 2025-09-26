<script setup>
    import { ref, watchEffect } from "vue";

    const props = defineProps({
    message: String,
    type: { type: String, default: "info" },
    duration: { type: Number, default: 3000 },
    show: Boolean,
    });

    const visible = ref(false);

    watchEffect(() => {
    if (props.show) {
        visible.value = true;
        setTimeout(() => (visible.value = false), props.duration);
    }
    });
</script>
<template>
  <transition name="fade">
    <div
      v-if="visible"
      class="fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg text-white"
      :class="{
        'bg-green-600': type === 'success',
        'bg-red-600': type === 'error',
        'bg-blue-600': type === 'info',
      }"
    >
      {{ message }}
    </div>
  </transition>
</template>

<style scoped>
    .fade-enter-active,
    .fade-leave-active {
    transition: opacity 0.5s;
    }
    .fade-enter-from,
    .fade-leave-to {
    opacity: 0;
    }
</style>