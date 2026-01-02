<?php

namespace FacturaScripts\Plugins\VisorErrores\Controller;

use FacturaScripts\Core\Base\Controller;
use FacturaScripts\Core\Kernel;
use FacturaScripts\Core\KernelException;
use FacturaScripts\Core\Tools;

class VisorErrores extends Controller
{
    public array $datosArchivos;
    public bool $kernelVersion2024;

    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data['menu'] = 'admin';
        $data['title'] = 'Visor de Errores';
        $data['icon'] = 'fa-solid fa-triangle-exclamation';
        return $data;
    }

    /**
     * @throws KernelException
     */
    public function privateCore(&$response, $user, $permissions)
    {
        parent::privateCore($response, $user, $permissions);

        $pathsArchivos = glob(Tools::folder('MyFiles', 'crash*.json'));

        // gestionar la acción de eliminar
        $action = $this->request->get('action', '');
        if ($action === 'delete') {
            $hash = $this->request->get('hash', '');
            $this->deleteError($hash);
            return;
        }

        if ($action === 'delete-all') {
            $this->deleteAllErrors();
            return;
        }

        $this->datosArchivos = [];
        foreach ($pathsArchivos as $pathArchivo) {
            $datos = json_decode(file_get_contents($pathArchivo), true);
            $this->datosArchivos[] = [
                'fecha' => $datos['created_at'] ?? date('Y-m-d H:i:s', filemtime($pathArchivo)),
                'hash' => $datos['hash'] ?? '',
                'archivo' => basename($pathArchivo),
                'datos' => $datos
            ];
        }

        // ordenar por fecha (más recientes primero)
        usort($this->datosArchivos, function ($a, $b) {
            return strtotime($b['fecha']) - strtotime($a['fecha']);
        });

        $this->kernelVersion2024 = strpos(Kernel::version(), '2024') !== false;
    }

    /**
     * @throws KernelException
     */
    private function deleteError(string $hash): void
    {
        $pathsArchivos = glob(Tools::folder('MyFiles', 'crash*.json'));
        foreach ($pathsArchivos as $pathArchivo) {
            $datos = json_decode(file_get_contents($pathArchivo), true);
            if (isset($datos['hash']) && $datos['hash'] === $hash) {
                if (unlink($pathArchivo)) {
                    Tools::log()->notice('Error eliminado correctamente');
                } else {
                    Tools::log()->error('No se pudo eliminar el archivo de error');
                }
                break;
            }
        }

        // redirigir a la misma página
        $this->redirect($this->url());
    }

    /**
     * @throws KernelException
     */
    private function deleteAllErrors(): void
    {
        $pathsArchivos = glob(Tools::folder('MyFiles', 'crash*.json'));
        $eliminados = 0;
        $errores = 0;

        foreach ($pathsArchivos as $pathArchivo) {
            if (unlink($pathArchivo)) {
                $eliminados++;
            } else {
                $errores++;
            }
        }

        if ($eliminados > 0) {
            Tools::log()->notice('Se eliminaron ' . $eliminados . ' errores correctamente');
        }
        if ($errores > 0) {
            Tools::log()->error('No se pudieron eliminar ' . $errores . ' archivos de error');
        }

        // redirigir a la misma página
        $this->redirect($this->url());
    }
}
