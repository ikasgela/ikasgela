@if($criterio)
    {!! str_starts_with(\Route::current()->getName(),'results.pdf')
        ? '[<span style="color:green">' . trans_choice('messages.passed', 1) . '</span>]'
        : '<i class="fas fa-check text-success"></i>' !!}
@else
    {!! str_starts_with(\Route::current()->getName(),'results.pdf')
        ? '[<span style="color:green">'. trans_choice('messages.not_passed', 1) .'</span>]'
        : '<i class="fas fa-times text-danger"></i>' !!}
@endif
