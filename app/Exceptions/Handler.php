<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\QueryException;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        \Log::error('Erro: ' . $exception->getMessage());

        if ($exception instanceof QueryException || $exception instanceof \PDOException) {
            return response()->json([
                'error' => 'Erro ao acessar o banco de dados',
            ], 500);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Ocorreu um erro no servidor',
                'message' => $exception->getMessage(),
            ],500);
        }

        return parent::render($request, $exception);
    }
}