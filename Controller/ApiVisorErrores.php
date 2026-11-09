<?php

namespace FacturaScripts\Plugins\VisorErrores\Controller;

use FacturaScripts\Core\Response;
use FacturaScripts\Core\Template\ApiController;
use FacturaScripts\Plugins\VisorErrores\Lib\GestorErrores;

/**
 * GET    /api/3/visorerrores/errores[/{hash}]  errores críticos
 * GET    /api/3/visorerrores/logs              últimos logs de error
 * DELETE /api/3/visorerrores/errores/{hash}    elimina un error crítico
 * DELETE /api/3/visorerrores/errores           elimina todos los errores críticos
 */
class ApiVisorErrores extends ApiController
{
    protected function runResource(): void
    {
        $seccion = $this->getUriParam('3');
        $hash = $this->getUriParam('4');

        match ($this->request->method() . ' ' . $seccion) {
            'GET errores' => $hash === '' ? $this->listarErrores() : $this->verError($hash),
            'GET logs' => $this->listarLogs(),
            'DELETE errores' => $hash === '' ? $this->eliminarTodos() : $this->eliminarError($hash),
            default => $this->responderError(Response::HTTP_NOT_FOUND, 'resource-not-found'),
        };
    }

    private function eliminarError(string $hash): void
    {
        GestorErrores::eliminar($hash) ?
            $this->response->json(['status' => 'success']) :
            $this->responderError(Response::HTTP_NOT_FOUND, 'error-not-found');
    }

    private function eliminarTodos(): void
    {
        [$eliminados, $fallidos] = GestorErrores::eliminarTodos();
        $this->response->json(['status' => $fallidos ? 'error' : 'success', 'eliminados' => $eliminados, 'fallidos' => $fallidos]);
    }

    private function listarErrores(): void
    {
        $this->response->json(GestorErrores::listarCriticos());
    }

    private function listarLogs(): void
    {
        $this->response->json(array_map(fn($log) => $log->toArray(), GestorErrores::listarLogs()));
    }

    private function responderError(int $codigo, string $mensaje): void
    {
        $this->response->setHttpCode($codigo)->json(['status' => 'error', 'message' => $mensaje]);
    }

    private function verError(string $hash): void
    {
        foreach (GestorErrores::listarCriticos() as $error) {
            if ($error['hash'] === $hash) {
                $this->response->json($error);
                return;
            }
        }
        $this->responderError(Response::HTTP_NOT_FOUND, 'error-not-found');
    }
}
