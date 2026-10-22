<script setup>
import { inject } from 'vue'
import { Handle, Position } from '@vue-flow/core'

defineProps({
    id: { type: String, required: true },
    data: { type: Object, required: true },
    selected: { type: Boolean, default: false },
})

const textos = inject('textos', {})
</script>

<template>
    <div class="nodo-plantilla" :class="{ 'nodo-plantilla--seleccionado': selected }">
        <Handle type="target" :position="Position.Left" class="nodo-plantilla__handle" />

        <div class="nodo-plantilla__cabecera">
            <span class="nodo-plantilla__orden">{{ data.orden }}</span>
            <span class="nodo-plantilla__unidad" :title="data.unidad">{{ data.unidad }}</span>
        </div>

        <div class="nodo-plantilla__nombre" :title="data.nombre">{{ data.nombre }}</div>

        <div v-if="data.etiquetas.length" class="nodo-plantilla__etiquetas">
            <span v-for="etiqueta in data.etiquetas" :key="etiqueta" class="badge bg-secondary">{{ etiqueta }}</span>
        </div>

        <div class="nodo-plantilla__pie">
            <span class="nodo-plantilla__dato" :title="textos.resources">
                <i class="bi bi-paperclip"></i> {{ data.recursos }}
            </span>
            <span v-if="data.final" class="nodo-plantilla__dato" :title="textos.final">
                <i class="bi bi-flag-fill"></i>
            </span>
            <span v-if="data.auto_avance" class="nodo-plantilla__dato" :title="textos.auto">
                <i class="bi bi-forward-fill"></i>
            </span>
            <span v-if="data.destacada" class="nodo-plantilla__dato">
                <i class="bi bi-star-fill"></i>
            </span>

            <span class="nodo-plantilla__dato" :title="data.siguiente_id ? textos.con_conexion : textos.sin_conexion">
                <i :class="data.siguiente_id ? 'bi bi-link-45deg' : 'bi bi-unlink'"></i>
            </span>

            <span class="nodo-plantilla__acciones">
                <a :href="data.url_preview" target="_blank" rel="noopener" class="nodo-plantilla__boton"
                   :title="textos.preview" @click.stop>
                    <i class="bi bi-eye"></i>
                </a>
                <a :href="data.url_edit" class="nodo-plantilla__boton" :title="textos.edit" @click.stop>
                    <i class="bi bi-pencil"></i>
                </a>
            </span>
        </div>

        <Handle type="source" :position="Position.Right" class="nodo-plantilla__handle" />
    </div>
</template>
