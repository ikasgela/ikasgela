<td @style(['min-width:.5em'])
    @class(['p-0', $fondo => $condicion])
    @isset($titulo)
        title="{{ $condicion ? $titulo : '' }}"
    @endisset
></td>
