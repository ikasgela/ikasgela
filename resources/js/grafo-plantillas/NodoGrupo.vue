<script setup>
import { inject } from 'vue'

defineProps({
    id: { type: String, required: true },
    data: { type: Object, required: true },
})

const textos = inject('textos', {})
</script>

<template>
    <div class="nodo-grupo">
        <span class="nodo-grupo__titulo">{{ data.titulo || textos.sin_conexion }}</span>
    </div>
</template>
