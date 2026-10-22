<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, provide, ref } from 'vue'
import { MarkerType, Panel, VueFlow, useVueFlow } from '@vue-flow/core'
import { Background } from '@vue-flow/background'
import { Controls } from '@vue-flow/controls'
import { MiniMap } from '@vue-flow/minimap'
import NodoGrupo from './NodoGrupo.vue'
import NodoPlantilla from './NodoPlantilla.vue'
import { calcularDisposicion, calcularGrupo } from './disposicion.js'

const props = defineProps({
    datos: { type: Object, required: true },
})

const datosGrafo = ref(props.datos)

const textos = computed(() => datosGrafo.value.textos || {})
provide('textos', textos)

const nodes = ref([])
const edges = ref([])

// Encadenado deseado por nodo: { [idNodo]: idDestino|null }
const siguientes = ref({})

const guardando = ref(false)
const aviso = ref(null)

const nodeTypes = { plantilla: NodoPlantilla, grupo: NodoGrupo }

const defaultEdgeOptions = {
    type: 'smoothstep',
    markerEnd: MarkerType.ArrowClosed,
    style: { strokeWidth: 2 },
}

const {
    addEdges,
    removeEdges,
    updateEdge,
    updateNode,
    fitView,
    getSelectedEdges,
} = useVueFlow()

const sinPlantillas = computed(() => (datosGrafo.value.nodos || []).length === 0)

let temporizadorGuardado = null
let temporizadorAviso = null

onMounted(() => {
    construirGrafo()
    window.addEventListener('keydown', alPulsarTecla)
})

onBeforeUnmount(() => {
    window.removeEventListener('keydown', alPulsarTecla)
    clearTimeout(temporizadorGuardado)
    clearTimeout(temporizadorAviso)
})

/**
 * Construye el grafo a partir de los datos del servidor.
 */
function construirGrafo() {
    const datosNodos = datosGrafo.value.nodos || []
    const datosEnlaces = datosGrafo.value.enlaces || []

    const encadenado = {}
    datosNodos.forEach((nodo) => {
        encadenado[String(nodo.id)] = nodo.siguiente_id ?? null
    })
    siguientes.value = encadenado

    edges.value = datosEnlaces.map((enlace) => ({
        id: enlace.id,
        source: String(enlace.source),
        target: String(enlace.target),
        ...defaultEdgeOptions,
    }))

    const disposicion = calcularDisposicion(datosNodos, datosEnlaces)

    const conPosicion = datosNodos.length > 0 && datosNodos.every(
        (nodo) => nodo.posicion && nodo.posicion.x !== null && nodo.posicion.y !== null
    )

    const posiciones = conPosicion
        ? Object.fromEntries(datosNodos.map((nodo) => [String(nodo.id), { x: nodo.posicion.x, y: nodo.posicion.y }]))
        : disposicion.posiciones

    const nuevosNodos = datosNodos.map((nodo) => ({
        id: String(nodo.id),
        type: 'plantilla',
        position: {
            x: posiciones[String(nodo.id)]?.x ?? 0,
            y: posiciones[String(nodo.id)]?.y ?? 0,
        },
        data: { ...nodo },
    }))

    const grupo = calcularGrupo(posiciones, conectados())

    if (grupo) {
        nuevosNodos.unshift({
            id: 'grupo-sin-conexion',
            type: 'grupo',
            position: { x: grupo.x, y: grupo.y },
            draggable: false,
            selectable: false,
            connectable: false,
            style: { width: `${grupo.width}px`, height: `${grupo.height}px` },
            data: { titulo: textos.value.sin_conexion },
        })
    }

    nodes.value = nuevosNodos

    if (!conPosicion && datosNodos.length) {
        // Se guarda la disposición inicial para que sea estable entre visitas
        nextTick(() => guardarPosiciones())
    }
}

/**
 * Identificadores de los nodos que tienen alguna conexión.
 */
function conectados() {
    const ids = new Set()

    edges.value.forEach((enlace) => {
        ids.add(String(enlace.source))
        ids.add(String(enlace.target))
    })

    return ids
}

/**
 * Recalcula el recuadro del grupo de nodos sin conexión.
 */
function actualizarGrupo() {
    const grupo = nodes.value.find((nodo) => nodo.type === 'grupo')

    if (!grupo) {
        return
    }

    const posiciones = {}

    nodes.value.forEach((nodo) => {
        if (nodo.type === 'plantilla') {
            posiciones[String(nodo.id)] = { x: nodo.position.x, y: nodo.position.y }
        }
    })

    const nuevo = calcularGrupo(posiciones, conectados())

    if (!nuevo) {
        nodes.value = nodes.value.filter((nodo) => nodo.id !== grupo.id)
        return
    }

    grupo.position.x = nuevo.x
    grupo.position.y = nuevo.y
    grupo.style = {
        width: `${nuevo.width}px`,
        height: `${nuevo.height}px`,
    }
}

/**
 * Crea una conexión entre dos nodos arrastrando desde el handle derecho.
 */
function alConectar(conexion) {
    if (!conexion || !conexion.source || !conexion.target) {
        return
    }

    const origen = String(conexion.source)
    const destino = String(conexion.target)

    if (origen === destino) {
        return
    }

    // Cada plantilla solo puede tener una actividad siguiente
    const existentes = edges.value.filter((enlace) => String(enlace.source) === origen)
    if (existentes.length) {
        removeEdges(existentes)
    }

    addEdges([{
        id: `e${origen}-${destino}`,
        source: conexion.source,
        target: conexion.target,
        ...defaultEdgeOptions,
    }])

    siguientes.value[origen] = destino

    actualizarGrupo()
    guardarConexiones()
}

/**
 * Cambia el destino de una conexión arrastrando el final del enlace.
 */
function alActualizarEnlace(payload) {
    if (!payload || !payload.edge || !payload.connection) {
        return
    }

    const { edge, connection } = payload

    if (!connection.source || !connection.target) {
        return
    }

    updateEdge(edge, connection)

    siguientes.value[String(connection.source)] = String(connection.target)

    actualizarGrupo()
    guardarConexiones()
}

/**
 * Detecta la eliminación de enlaces (tecla Supr/Delete).
 */
function alCambiarEdges(cambios) {
    if (!Array.isArray(cambios)) {
        return
    }

    let eliminado = false

    cambios.forEach((cambio) => {
        if (cambio.type !== 'remove' || !cambio.source) {
            return
        }

        const origen = String(cambio.source)

        if (siguientes.value[origen]) {
            siguientes.value[origen] = null
            eliminado = true
        }
    })

    if (eliminado) {
        actualizarGrupo()
        guardarConexiones()
    }
}

/**
 * Guarda la posición de un nodo al terminar de arrastrarlo.
 */
function alSoltarNodo() {
    actualizarGrupo()
    guardarPosiciones()
}

function alPulsarTecla(evento) {
    if (evento.key !== 'Delete' && evento.key !== 'Backspace') {
        return
    }

    const elemento = evento.target

    if (elemento && ['INPUT', 'TEXTAREA', 'SELECT'].includes(elemento.tagName)) {
        return
    }

    const seleccionadas = getSelectedEdges.value

    if (!seleccionadas || !seleccionadas.length) {
        return
    }

    evento.preventDefault()
    removeEdges(seleccionadas)
}

/**
 * Organiza automáticamente el grafo: los nodos conectados en línea y los
 * que no tienen conexión agrupados.
 */
function organizar() {
    const datosNodos = datosGrafo.value.nodos || []
    const enlaces = edges.value.map((enlace) => ({ source: enlace.source, target: enlace.target }))

    const disposicion = calcularDisposicion(datosNodos, enlaces)

    datosNodos.forEach((nodo) => {
        const posicion = disposicion.posiciones[String(nodo.id)]

        if (!posicion) {
            return
        }

        updateNode(String(nodo.id), { position: { x: posicion.x, y: posicion.y } })
    })

    nextTick(() => {
        actualizarGrupo()
        fitView({ padding: 0.2, duration: 300 })
        guardarPosiciones()
    })
}

/**
 * Vuelve a cargar los datos del grafo desde el servidor.
 */
async function recargar() {
    try {
        const respuesta = await fetch(datosGrafo.value.urls.datos, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })

        if (!respuesta.ok) {
            throw new Error(`Error ${respuesta.status}`)
        }

        datosGrafo.value = await respuesta.json()
        construirGrafo()
        mostrarAviso('ok', textos.value.recargado)
    } catch (error) {
        mostrarAviso('error', textos.value.error)
    }
}

function guardarPosiciones() {
    const posiciones = nodes.value
        .filter((nodo) => nodo.type === 'plantilla')
        .map((nodo) => ({
            actividad_id: Number(nodo.id),
            x: Math.round(nodo.position.x),
            y: Math.round(nodo.position.y),
        }))

    if (!posiciones.length) {
        return
    }

    enviar('posiciones', { posiciones })
}

function guardarConexiones() {
    clearTimeout(temporizadorGuardado)

    temporizadorGuardado = setTimeout(() => {
        const conexiones = Object.entries(siguientes.value).map(([origen_id, destino_id]) => ({
            origen_id: Number(origen_id),
            destino_id: destino_id ? Number(destino_id) : null,
        }))

        if (!conexiones.length) {
            return
        }

        enviar('conexiones', { conexiones })
    }, 300)
}

async function enviar(tipo, cuerpo) {
    guardando.value = true

    try {
        const respuesta = await fetch(datosGrafo.value.urls[tipo], {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': tokenCsrf(),
            },
            body: JSON.stringify(cuerpo),
        })

        if (!respuesta.ok) {
            throw new Error(`Error ${respuesta.status}`)
        }

        mostrarAviso('ok', textos.value.guardado)

        return true
    } catch (error) {
        mostrarAviso('error', textos.value.error)

        return false
    } finally {
        guardando.value = false
    }
}

function tokenCsrf() {
    const meta = document.querySelector('meta[name="csrf-token"]')

    return meta ? meta.getAttribute('content') : ''
}

function mostrarAviso(tipo, texto) {
    aviso.value = { tipo, texto }

    clearTimeout(temporizadorAviso)
    temporizadorAviso = setTimeout(() => {
        aviso.value = null
    }, 3000)
}
</script>

<template>
    <div class="grafo-plantillas">
        <div class="grafo-plantillas__barra">
            <button type="button" class="btn btn-sm btn-primary" @click="organizar">
                <i class="bi bi-magic me-1"></i>{{ textos.organizar }}
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" @click="recargar">
                <i class="bi bi-arrow-clockwise me-1"></i>{{ textos.recargar }}
            </button>

            <span v-if="guardando" class="text-muted ms-3">
                <i class="bi bi-hourglass-split me-1"></i>{{ textos.guardando }}
            </span>
            <span v-else-if="aviso" class="ms-3" :class="aviso.tipo === 'error' ? 'text-danger' : 'text-success'">
                <i :class="aviso.tipo === 'error' ? 'bi bi-exclamation-triangle' : 'bi bi-check-circle'" class="me-1"></i>
                {{ aviso.texto }}
            </span>
        </div>

        <div v-if="sinPlantillas" class="alert alert-info m-3">
            {{ textos.sin_plantillas }}
        </div>

        <div v-else class="grafo-plantillas__lienzo">
            <VueFlow
                v-model:nodes="nodes"
                v-model:edges="edges"
                :node-types="nodeTypes"
                :default-edge-options="defaultEdgeOptions"
                :edges-updatable="true"
                :delete-key-code="null"
                :min-zoom="0.2"
                :max-zoom="2"
                :snap-to-grid="true"
                :snap-grid="[10, 10]"
                :fit-view-on-init="true"
                @connect="alConectar"
                @edge-update="alActualizarEnlace"
                @edges-change="alCambiarEdges"
                @node-drag-stop="alSoltarNodo"
            >
                <Background pattern-color="#adb5bd" :gap="20" />
                <Controls />
                <MiniMap pannable zoomable />

                <Panel position="bottom-left">
                    <div class="grafo-plantillas__ayuda">
                        <div><i class="bi bi-arrow-right-circle me-1"></i>{{ textos.arrastra }}</div>
                        <div><i class="bi bi-pencil me-1"></i>{{ textos.reconectar }}</div>
                        <div><i class="bi bi-trash me-1"></i>{{ textos.borrar_enlace }}</div>
                    </div>
                </Panel>
            </VueFlow>
        </div>
    </div>
</template>
