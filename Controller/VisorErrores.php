<?php

namespace FacturaScripts\Plugins\VisorErrores\Controller;

use FacturaScripts\Core\Base\Controller;
use FacturaScripts\Core\KernelException;
use FacturaScripts\Core\Tools;
use FacturaScripts\Plugins\VisorErrores\Lib\GestorErrores;

class VisorErrores extends Controller
{
    public array $datosArchivos = [];

    public array $logsErrors = [];

    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data['menu'] = 'admin';
        $data['title'] = 'visor-errores';
        $data['icon'] = 'fa-solid fa-triangle-exclamation';
        return $data;
    }

    /**
     * @throws KernelException
     */
    public function privateCore(&$response, $user, $permissions)
    {
        parent::privateCore($response, $user, $permissions);

        $action = $this->request->input('action', '');
        if (in_array($action, ['delete', 'delete-all'], true)) {
            $this->eliminarAction($action);
            $this->redirect($this->url());
            return;
        }

        $this->datosArchivos = GestorErrores::listarCriticos();
        $this->logsErrors = GestorErrores::listarLogs();
    }

    private function eliminarAction(string $action): void
    {
        if (false === $this->permissions->allowDelete) {
            Tools::log()->warning('not-allowed-delete');
            return;
        }
        if (false === $this->validateFormToken()) {
            return;
        }

        if ('delete' === $action) {
            GestorErrores::eliminar((string)$this->request->input('hash', '')) ?
                Tools::log()->notice('visor-errores-error-eliminado') :
                Tools::log()->error('visor-errores-error-no-eliminado');
            return;
        }

        [$eliminados, $fallidos] = GestorErrores::eliminarTodos();
        if ($eliminados > 0) {
            Tools::log()->notice('visor-errores-errores-eliminados', ['%eliminados%' => $eliminados]);
        }
        if ($fallidos > 0) {
            Tools::log()->error('visor-errores-errores-no-eliminados', ['%fallidos%' => $fallidos]);
        }
    }
}
