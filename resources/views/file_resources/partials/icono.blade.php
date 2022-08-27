@switch($extension)
    @case('pdf')
        {{-- https://www.schemecolor.com/adobe-inc-logo-colors.php --}}
        <i class="fas fa-file-pdf" style="color:#ed2224"></i>
        @break
    @case('doc')
    @case('docx')
    @case('odt')
        <i class="fas fa-file-word" style="color:#104eb7"></i>
        @break
    @case('xls')
    @case('xlsx')
    @case('ods')
        {{-- https://www.schemecolor.com/microsoft-excel-logo-color.php --}}
        <i class="fas fa-file-excel" style="color:#1D6F42"></i>
        @break
    @case('zip')
        <i class="fas fa-file-zipper" style="color:#eaa947"></i>
        @break
    @default
        <i class="fas fa-file" style="color:gray"></i>
@endswitch
