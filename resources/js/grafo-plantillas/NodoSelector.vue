<script setup>
import { inject } from 'vue'
import { Handle, Position } from '@vue-flow/core'

defineProps({
    id: { type: String, required: true },
    data: { type: Object, required: true },
})

const textos = inject('textos', {})
</script>

<template>
    <div class="nodo-selector">
        <Handle type="source" :position="Position.Right" class="nodo-selector__handle" />

        <a class="nodo-selector__enlace nodrag" :href="data.url_edit" target="_blank" rel="noopener"
           :title="data.titulo || textos.selector" @click.stop>
            <i class="bi bi-shuffle"></i>
            <span v-if="data.reglas && data.reglas.length > 1" class="nodo-selector__contador">
                {{ data.reglas.length }}
            </span>
        </a>

        <span class="nodo-selector__titulo">{{ data.titulo || textos.selector }}</span>
    </div>
</template>
