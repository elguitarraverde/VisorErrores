<?php

namespace FacturaScripts\Plugins\VisorErrores\Controller;

use FacturaScripts\Core\Base\Controller;
use FacturaScripts\Core\Kernel;
use FacturaScripts\Core\KernelException;
use FacturaScripts\Core\Model\LogMessage;
use FacturaScripts\Core\Tools;
use FacturaScripts\Core\Where;

class VisorErrores extends Controller
{
    public array $datosArchivos;
    public array $logsErrors;

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

        $action = $this->request->query('action', '');

        // ELIMINAR UN ERROR CRITICO
        if ($action === 'delete') {
            $hash = $this->request->query('hash', '');
            $this->deleteError($hash);
            return;
        }

        // ELIMINAR TODOS LOS ERRORES CRITICOS
        if ($action === 'delete-all') {
            $this->deleteAllErrors();
            return;
        }

        // ELIMINAR TODOS LOS LOGS DE ERRORES
        if ($action === 'delete-all-logs') {
            $deleted = LogMessage::deleteWhere([
                Where::notEq('channel', 'audit'),
                Where::eq('level', 'error')
            ]);

            if($deleted){
                Tools::log()->notice('Se eliminaron todos los logs de errores correctamente');
            }else{
                Tools::log()->error('No se pudieron eliminar todos los logs de errores');
            }

            // redirigir a la misma página
            $this->redirect($this->url());

            return;
        }

        $this->getCriticalErrors();

        $this->getLogsErrors();
    }

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

    private function getCriticalErrors(): void
    {
        $pathsArchivos = glob(Tools::folder('MyFiles', 'crash*.json'));

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
    }

    private function getLogsErrors(): void
    {
        $this->logsErrors = LogMessage::all([Where::eq('level', 'error')]);

        foreach ($this->logsErrors as $log) {
            if (is_string($log->context)) {
                // 1. Convertimos &quot; a "
                $cleanJson = htmlspecialchars_decode($log->context);

                // 2. Decodificamos el JSON
                $log->context = json_decode($cleanJson, true);
            }
        }
    }
}
