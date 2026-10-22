import { createApp } from 'vue'
import GrafoPlantillas from './GrafoPlantillas.vue'

import '@vue-flow/core/dist/style.css'
import '@vue-flow/core/dist/theme-default.css'

const contenedor = document.getElementById('grafo-plantillas')
const datos = document.getElementById('datos-grafo-plantillas')

if (contenedor && datos) {
    createApp(GrafoPlantillas, {
        datos: JSON.parse(datos.textContent),
    }).mount(contenedor)
}
