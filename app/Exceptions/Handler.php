<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

function render($request, Throwable $exception)
{
    // Jika error 403 (Forbidden)
    if ($exception instanceof HttpException && $exception->getStatusCode() === 403) {
        return response()->view('errors.403', [], 403);
    }

    // Jika error 404 (Not Found)
    if ($exception instanceof NotFoundHttpException || $exception instanceof ModelNotFoundException) {
        return response()->view('errors.404', [], 404);
    }

    // Jika error 419 (Session Expired)
    if ($exception->getStatusCode() === 419) {
        return response()->view('errors.419', [], 419);
    }

    // Jika error 500 (Internal Server Error)
    if ($exception instanceof HttpException && $exception->getStatusCode() === 500) {
        return response()->view('errors.500', [], 500);
    }

    // Jika error 503 (Service Unavailable)
    if ($exception instanceof HttpException && $exception->getStatusCode() === 503) {
        return response()->view('errors.503', [], 503);
    }
}
