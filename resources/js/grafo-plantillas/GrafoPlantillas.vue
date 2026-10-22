<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, provide, ref } from 'vue'
import { MarkerType, Panel, VueFlow, useVueFlow } from '@vue-flow/core'
import { Background } from '@vue-flow/background'
import { Controls } from '@vue-flow/controls'
import { MiniMap } from '@vue-flow/minimap'
import NodoGrupo from './NodoGrupo.vue'
import NodoPlantilla from './NodoPlantilla.vue'
import NodoSelector from './NodoSelector.vue'
import { ANCHO_NODO, ALTO_NODO, calcularDisposicion, calcularGrupo } from './disposicion.js'

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

// El cuadro de ayuda se puede minimizar y se recuerda la preferencia
const CLAVE_AYUDA = 'ikasgela.grafo.ayuda'
const ayudaVisible = ref(leerPreferenciaAyuda())

const nodeTypes = { plantilla: NodoPlantilla, grupo: NodoGrupo, selector: NodoSelector }

const defaultEdgeOptions = {
    type: 'smoothstep',
    markerEnd: MarkerType.ArrowClosed,
    style: { strokeWidth: 2 },
}

// Conexiones condicionadas de los selectores
const opcionesCondicion = {
    type: 'smoothstep',
    markerEnd: MarkerType.ArrowClosed,
    style: { strokeWidth: 2, strokeDasharray: '6 4', stroke: '#6f42c1' },
}

// Radio del círculo de los selectores
const RADIO_SELECTOR = 20

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

// Información de cada enlace (tipo y regla asociada) para reconocer los condicionados
const infoEdges = new Map()

// Grupos de reglas que se han borrado y hay que eliminar en el servidor
let borradosCondicion = []

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
    const datosCondiciones = datosGrafo.value.enlaces_selectores || []

    const encadenado = {}
    datosNodos.forEach((nodo) => {
        encadenado[String(nodo.id)] = nodo.siguiente_id ?? null
    })
    siguientes.value = encadenado

    infoEdges.clear()
    borradosCondicion = []

    const enlacesBase = datosEnlaces.map((enlace) => {
        const edge = {
            id: enlace.id,
            source: String(enlace.source),
            target: String(enlace.target),
            tipo: 'siguiente',
            ...defaultEdgeOptions,
        }

        infoEdges.set(edge.id, { tipo: 'siguiente' })

        return edge
    })

    const enlacesCondicion = datosCondiciones.map((enlace) => {
        const edge = {
            id: enlace.id,
            source: String(enlace.source),
            target: String(enlace.target),
            tipo: 'condicion',
            ...opcionesCondicion,
            data: {
                selector_id: Number(enlace.selector_id),
                regla_id: Number(enlace.regla_id),
                actividad_id: Number(enlace.actividad_id),
            },
        }

        infoEdges.set(edge.id, { tipo: 'condicion', ...edge.data })

        return edge
    })

    edges.value = [...enlacesBase, ...enlacesCondicion]

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

    // Círculos de los selectores, que se dibujan encima de la conexión
    datosNodos.forEach((nodo) => {
        const selectores = nodo.selectores || []

        selectores.forEach((selector, indice) => {
            nuevosNodos.push({
                id: idSelector(nodo.id, selector.id),
                type: 'selector',
                position: { x: 0, y: 0 },
                draggable: false,
                selectable: false,
                data: {
                    ...selector,
                    actividad_id: String(nodo.id),
                    indice,
                    total: selectores.length,
                },
            })
        })
    })

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

    nextTick(() => {
        actualizarPosicionesSelectores()

        if (!conPosicion && datosNodos.length) {
            // Se guarda la disposición inicial para que sea estable entre visitas
            guardarPosiciones()
        }
    })
}

/**
 * Recupera la preferencia de mostrar el cuadro de ayuda.
 */
function leerPreferenciaAyuda() {
    try {
        const valor = window.localStorage.getItem(CLAVE_AYUDA)

        return valor === null ? true : valor === '1'
    } catch (error) {
        return true
    }
}

/**
 * Muestra u oculta el cuadro de ayuda y recuerda la preferencia.
 */
function alternarAyuda() {
    ayudaVisible.value = !ayudaVisible.value

    try {
        window.localStorage.setItem(CLAVE_AYUDA, ayudaVisible.value ? '1' : '0')
    } catch (error) {
        // Si el navegador no permite guardar preferencias, se ignora
    }
}

/**
 * Identificador del nodo (círculo) de un selector.
 */
function idSelector(actividad_id, selector_id) {
    return `selector-${actividad_id}-${selector_id}`
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

    // Los selectores son conexiones adicionales: su plantilla no está suelta
    nodes.value.forEach((nodo) => {
        if (nodo.type === 'plantilla' && (nodo.data.selectores || []).length) {
            ids.add(String(nodo.id))
        }
    })

    return ids
}

/**
 * Coloca los círculos de los selectores encima de su conexión base.
 */
function actualizarPosicionesSelectores() {
    const porId = new Map(nodes.value.map((nodo) => [nodo.id, nodo]))

    const bases = new Map()
    edges.value.filter((enlace) => enlace.tipo === 'siguiente').forEach((enlace) => {
        const lista = bases.get(String(enlace.source)) || []
        lista.push(enlace)
        bases.set(String(enlace.source), lista)
    })

    nodes.value.filter((nodo) => nodo.type === 'selector').forEach((nodo) => {
        const actividad_id = String(nodo.data.actividad_id)
        const origen = porId.get(actividad_id)
        const base = (bases.get(actividad_id) || [])[0]
        const destino = base ? porId.get(String(base.target)) : null

        let centro = null

        if (origen && destino) {
            // Repartidos a lo largo de la conexión base
            const p1 = handleDerecho(origen)
            const p2 = handleIzquierdo(destino)
            const total = nodo.data.total || 1
            const paso = (nodo.data.indice + 1) / (total + 1)

            centro = {
                x: p1.x + (p2.x - p1.x) * paso,
                y: p1.y + (p2.y - p1.y) * paso,
            }
        } else if (origen) {
            // Sin conexión base, el círculo se coloca al lado de la plantilla
            centro = {
                x: origen.position.x + ancho(origen) + 60,
                y: origen.position.y + alto(origen) / 2,
            }
        }

        if (!centro) {
            return
        }

        updateNode(nodo.id, {
            position: {
                x: centro.x - RADIO_SELECTOR,
                y: centro.y - RADIO_SELECTOR,
            },
        })
    })
}

function ancho(nodo) {
    return nodo.dimensions?.width || ANCHO_NODO
}

function alto(nodo) {
    return nodo.dimensions?.height || ALTO_NODO
}

function handleDerecho(nodo) {
    return {
        x: nodo.position.x + ancho(nodo),
        y: nodo.position.y + alto(nodo) / 2,
    }
}

function handleIzquierdo(nodo) {
    return {
        x: nodo.position.x,
        y: nodo.position.y + alto(nodo) / 2,
    }
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

    const nodoOrigen = nodes.value.find((nodo) => nodo.id === origen)

    if (!nodoOrigen) {
        return
    }

    // Los selectores crean conexiones condicionadas
    if (nodoOrigen.type === 'selector') {
        conectarCondicion(nodoOrigen, destino)
        return
    }

    if (nodoOrigen.type !== 'plantilla') {
        return
    }

    // Cada plantilla solo puede tener una actividad siguiente
    const existentes = edges.value.filter((enlace) => enlace.tipo === 'siguiente' && String(enlace.source) === origen)
    if (existentes.length) {
        removeEdges(existentes)
    }

    addEdges([{
        id: `e${origen}-${destino}`,
        source: conexion.source,
        target: conexion.target,
        tipo: 'siguiente',
        ...defaultEdgeOptions,
    }])

    infoEdges.set(`e${origen}-${destino}`, { tipo: 'siguiente' })

    siguientes.value[origen] = destino

    nextTick(() => actualizarPosicionesSelectores())
    actualizarGrupo()
    guardarConexiones()
}

/**
 * Crea una conexión condicionada desde el círculo de un selector.
 */
function conectarCondicion(nodoSelector, destino) {
    const nodoDestino = nodes.value.find((nodo) => nodo.id === destino)

    // Solo se puede condicionar hacia otra plantilla
    if (!nodoDestino || nodoDestino.type !== 'plantilla') {
        return
    }

    const selector_id = Number(nodoSelector.data.id)

    // Se usa la primera condición que todavía no está conectada
    const conectadas = new Set(edges.value
        .filter((enlace) => enlace.tipo === 'condicion' && enlace.source === nodoSelector.id)
        .map((enlace) => enlace.data?.regla_id))

    const libre = (nodoSelector.data.reglas || []).find(
        (regla) => !regla.resultado_id && !conectadas.has(regla.id)
    )
    const regla_id = libre ? Number(libre.id) : null

    const id = `s${selector_id}-${regla_id ?? 'nueva'}-${destino}`

    addEdges([{
        id,
        source: nodoSelector.id,
        target: destino,
        tipo: 'condicion',
        ...opcionesCondicion,
        data: {
            selector_id,
            regla_id,
            actividad_id: Number(nodoSelector.data.actividad_id),
        },
    }])

    infoEdges.set(id, { tipo: 'condicion', selector_id, regla_id, actividad_id: Number(nodoSelector.data.actividad_id) })

    actualizarGrupo()
    guardarCondiciones()
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

    const info = infoEdges.get(edge.id)

    const actualizado = updateEdge(edge, {
        ...connection,
        tipo: edge.tipo,
        ...(edge.tipo === 'condicion' ? opcionesCondicion : defaultEdgeOptions),
    })

    if (actualizado) {
        // Al actualizar, el enlace cambia de identificador
        infoEdges.delete(edge.id)
        infoEdges.set(actualizado.id, info
            ? { ...info, ...(edge.data || {}) }
            : { tipo: edge.tipo })
    }

    if (edge.tipo === 'condicion') {
        nextTick(() => actualizarPosicionesSelectores())
        guardarCondiciones()
        return
    }

    siguientes.value[String(connection.source)] = String(connection.target)

    nextTick(() => actualizarPosicionesSelectores())

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
    let eliminadaCondicion = false

    cambios.forEach((cambio) => {
        if (cambio.type !== 'remove' || !cambio.source) {
            return
        }

        const info = infoEdges.get(cambio.id)

        if (info && info.tipo === 'condicion') {
            infoEdges.delete(cambio.id)
            borradosCondicion.push({
                selector_id: info.selector_id,
                regla_id: info.regla_id,
            })
            eliminadaCondicion = true
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

    if (eliminadaCondicion) {
        nextTick(() => actualizarPosicionesSelectores())
        actualizarGrupo()
        guardarCondiciones()
    }
}

/**
 * Guarda la posición de un nodo al terminar de arrastrarlo.
 */
function alSoltarNodo() {
    actualizarPosicionesSelectores()
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
    const enlaces = edges.value
        .filter((enlace) => enlace.tipo === 'siguiente')
        .map((enlace) => ({ source: enlace.source, target: enlace.target }))

    const disposicion = calcularDisposicion(datosNodos, enlaces)

    datosNodos.forEach((nodo) => {
        const posicion = disposicion.posiciones[String(nodo.id)]

        if (!posicion) {
            return
        }

        updateNode(String(nodo.id), { position: { x: posicion.x, y: posicion.y } })
    })

    nextTick(() => {
        actualizarPosicionesSelectores()
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

/**
 * Guarda el resultado de las condiciones de los selectores.
 */
function guardarCondiciones() {
    clearTimeout(temporizadorGuardado)

    temporizadorGuardado = setTimeout(async () => {
        const conexiones = edges.value
            .filter((enlace) => enlace.tipo === 'condicion')
            .map((enlace) => ({
                selector_id: enlace.data?.selector_id,
                regla_id: enlace.data?.regla_id,
                destino_id: Number(enlace.target),
            }))

        const borrados = borradosCondicion.map((borrado) => ({
            selector_id: borrado.selector_id,
            regla_id: borrado.regla_id,
            destino_id: null,
        }))

        borradosCondicion = []

        if (!conexiones.length && !borrados.length) {
            return
        }

        const respuesta = await enviar('resultados', { conexiones: [...conexiones, ...borrados] })

        if (respuesta && respuesta.reglas) {
            sincronizarCondiciones(respuesta.reglas)
        }
    }, 300)
}

/**
 * Actualiza los círculos con los grupos de reglas devueltos por el servidor.
 */
function sincronizarCondiciones(reglas) {
    const porSelector = {}

    reglas.forEach((regla) => {
        const lista = porSelector[regla.selector_id] || []
        lista.push(regla)
        porSelector[regla.selector_id] = lista
    })

    nodes.value.forEach((nodo) => {
        if (nodo.type !== 'selector') {
            return
        }

        const lista = porSelector[nodo.data.id] || []

        nodo.data.reglas = lista.map((regla) => ({
            id: regla.regla_id,
            resultado_id: regla.destino_id ? String(regla.destino_id) : null,
        }))

        // Las condiciones nuevas reciben el identificador del grupo de reglas
        edges.value.forEach((enlace) => {
            if (enlace.tipo !== 'condicion' || enlace.source !== nodo.id) {
                return
            }

            if (enlace.data?.regla_id) {
                return
            }

            const encontrada = lista.find((regla) => String(regla.destino_id) === String(enlace.target))

            if (encontrada) {
                enlace.data.regla_id = encontrada.regla_id
                infoEdges.set(enlace.id, { ...infoEdges.get(enlace.id), regla_id: encontrada.regla_id })
            }
        })
    })
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

        return await respuesta.json()
    } catch (error) {
        mostrarAviso('error', textos.value.error)

        return null
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
            <button type="button" id="grafo-organizar" class="btn btn-sm btn-primary" @click="organizar">
                <i class="bi bi-magic me-1"></i>{{ textos.organizar }}
            </button>
            <button type="button" id="grafo-recargar" class="btn btn-sm btn-outline-secondary" @click="recargar">
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
                @nodes-initialized="actualizarPosicionesSelectores"
            >
                <Background pattern-color="#adb5bd" :gap="20" />
                <Controls />
                <MiniMap pannable zoomable :aria-label="textos.minimapa" />

                <Panel position="bottom-left">
                    <div class="grafo-plantillas__ayuda">
                        <div class="grafo-plantillas__ayuda-cabecera">
                            <span><i class="bi bi-question-circle me-1"></i>{{ textos.ayuda }}</span>
                            <button type="button" id="grafo-ayuda" class="btn btn-sm btn-link p-0 grafo-plantillas__ayuda-boton"
                                :aria-expanded="ayudaVisible ? 'true' : 'false'"
                                :aria-label="ayudaVisible ? textos.ocultar_ayuda : textos.mostrar_ayuda"
                                :title="ayudaVisible ? textos.ocultar_ayuda : textos.mostrar_ayuda"
                                @click="alternarAyuda">
                                <i :class="ayudaVisible ? 'bi bi-chevron-down' : 'bi bi-chevron-up'"></i>
                            </button>
                        </div>
                        <div v-if="ayudaVisible" class="grafo-plantillas__ayuda-contenido">
                            <div><i class="bi bi-arrow-right-circle me-1"></i>{{ textos.arrastra }}</div>
                            <div><i class="bi bi-pencil me-1"></i>{{ textos.reconectar }}</div>
                            <div><i class="bi bi-trash me-1"></i>{{ textos.borrar_enlace }}</div>
                            <div><i class="bi bi-shuffle me-1"></i>{{ textos.condicional }}</div>
                            <div><i class="bi bi-exclamation-triangle me-1"></i>{{ textos.borrar_condicion }}</div>
                        </div>
                    </div>
                </Panel>
            </VueFlow>
        </div>
    </div>
</template>
