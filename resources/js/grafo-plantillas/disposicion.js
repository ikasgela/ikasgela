import dagre from '@dagrejs/dagre'

export const ANCHO_NODO = 300
export const ALTO_NODO = 160

const SEPARACION_X = 90
const SEPARACION_Y = 50
const COLUMNAS_SIN_CONEXION = 4
const MARGEN = 40

/**
 * Calcula el recuadro que engloba a los nodos sin conexión.
 *
 * @param {Object} posiciones    Mapa {id: {x, y}} con la posición de cada nodo
 * @param {Set}    conectados   Identificadores de los nodos que tienen alguna conexión
 * @returns {{x: number, y: number, width: number, height: number}|null}
 */
export function calcularGrupo(posiciones, conectados = new Set()) {
    const sueltos = Object.entries(posiciones).filter(([id]) => !conectados.has(String(id)))

    if (!sueltos.length) {
        return null
    }

    const minX = Math.min(...sueltos.map(([, posicion]) => posicion.x))
    const minY = Math.min(...sueltos.map(([, posicion]) => posicion.y))
    const maxX = Math.max(...sueltos.map(([, posicion]) => posicion.x + ANCHO_NODO))
    const maxY = Math.max(...sueltos.map(([, posicion]) => posicion.y + ALTO_NODO))

    return {
        x: minX - 30,
        y: minY - 40,
        width: maxX - minX + 60,
        height: maxY - minY + 70,
    }
}

/**
 * Calcula la disposición de los nodos del grafo.
 *
 * - Los nodos que están conectados entre sí se colocan en línea (de izquierda a derecha)
 *   siguiendo el encadenado, usando dagre. Cada grupo conectado ocupa su propia banda.
 * - Los nodos sin ninguna conexión se agrupan en una cuadrícula aparte, debajo de las bandas.
 *
 * @param {Array} nodos  Lista de nodos {id}
 * @param {Array} enlaces Lista de enlaces {source, target}
 * @returns {{posiciones: Object}}
 */
export function calcularDisposicion(nodos, enlaces) {
    const ids = nodos.map((nodo) => String(nodo.id))

    const adyacentes = new Map()
    ids.forEach((id) => adyacentes.set(id, new Set()))

    enlaces.forEach((enlace) => {
        const origen = String(enlace.source)
        const destino = String(enlace.target)

        if (!adyacentes.has(origen) || !adyacentes.has(destino)) {
            return
        }

        adyacentes.get(origen).add(destino)
        adyacentes.get(destino).add(origen)
    })

    // Componentes conexas (se buscan sin tener en cuenta la dirección de los enlaces)
    const visitados = new Set()
    const componentes = []

    ids.forEach((id) => {
        if (visitados.has(id)) {
            return
        }

        const pendientes = [id]
        const componente = []
        visitados.add(id)

        while (pendientes.length) {
            const actual = pendientes.shift()
            componente.push(actual)

            adyacentes.get(actual).forEach((vecino) => {
                if (!visitados.has(vecino)) {
                    visitados.add(vecino)
                    pendientes.push(vecino)
                }
            })
        }

        componentes.push(componente)
    })

    const conectadas = componentes.filter((componente) => componente.length > 1)
    const sueltas = componentes.filter((componente) => componente.length === 1)

    const posiciones = {}

    let y = MARGEN

    // Banda por cada grupo de nodos conectados
    conectadas.forEach((componente) => {
        const grafo = new dagre.graphlib.Graph()
        grafo.setDefaultEdgeLabel(() => ({}))
        grafo.setGraph({
            rankdir: 'LR',
            nodesep: SEPARACION_Y,
            ranksep: SEPARACION_X,
            marginx: 0,
            marginy: 0,
        })

        componente.forEach((id) => grafo.setNode(id, { width: ANCHO_NODO, height: ALTO_NODO }))

        enlaces.forEach((enlace) => {
            const origen = String(enlace.source)
            const destino = String(enlace.target)

            if (componente.includes(origen) && componente.includes(destino)) {
                grafo.setEdge(origen, destino)
            }
        })

        dagre.layout(grafo)

        let minX = Infinity
        let minY = Infinity
        let maxY = -Infinity

        componente.forEach((id) => {
            const nodo = grafo.node(id)
            minX = Math.min(minX, nodo.x)
            minY = Math.min(minY, nodo.y)
            maxY = Math.max(maxY, nodo.y)
        })

        componente.forEach((id) => {
            const nodo = grafo.node(id)
            posiciones[id] = {
                x: Math.round(nodo.x - ANCHO_NODO / 2 - minX + MARGEN),
                y: Math.round(nodo.y - ALTO_NODO / 2 - minY + y),
            }
        })

        y += (maxY - minY) + ALTO_NODO + SEPARACION_Y * 2
    })

    // Cuadrícula con los nodos que no están conectados con ningún otro
    if (sueltas.length) {
        const filas = Math.ceil(sueltas.length / COLUMNAS_SIN_CONEXION)

        sueltas.forEach((id, indice) => {
            const fila = Math.floor(indice / COLUMNAS_SIN_CONEXION)
            const columna = indice % COLUMNAS_SIN_CONEXION

            posiciones[id] = {
                x: MARGEN + columna * (ANCHO_NODO + SEPARACION_X),
                y: y + fila * (ALTO_NODO + SEPARACION_Y),
            }
        })

        y += filas * ALTO_NODO + (filas - 1) * SEPARACION_Y + SEPARACION_Y * 2
    }

    return { posiciones }
}
