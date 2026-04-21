<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Override;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        CannotUpdateLockedPropertyException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Throwable $exception
     * @return void
     */
    #[Override]
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    #[Override]
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof CannotUpdateLockedPropertyException && $request->hasHeader('X-Livewire')) {
            return response()->json(['message' => 'Invalid request.'], 422);
        }

        return parent::render($request, $exception);
    }
}
