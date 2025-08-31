@switch($extension)
    @case('pdf')
        {{-- https://www.schemecolor.com/adobe-inc-logo-colors.php --}}
        <i class="bi bi-file-earmark-pdf-fill" style="color:#ed2224"></i>
        @break
    @case('doc')
    @case('docx')
    @case('odt')
        <i class="bi bi-file-earmark-word-fill" style="color:#104eb7"></i>
        @break
    @case('xls')
    @case('xlsx')
    @case('ods')
        {{-- https://www.schemecolor.com/microsoft-excel-logo-color.php --}}
        <i class="bi bi-file-earmark-excel-fill" style="color:#1D6F42"></i>
        @break
    @case('zip')
        <i class="bi bi-file-earmark-zip-fill" style="color:#eaa947"></i>
        @break
    @default
        <i class="bi bi-file-earmark-fill" style="color:gray"></i>
@endswitch
