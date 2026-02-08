<td @style(['min-width:.5rem', 'width:.5rem'])
    @class(['p-0', $fondo => $condicion])
    @isset($titulo)
        title="{{ $condicion ? $titulo : '' }}"
    @endisset
></td>
