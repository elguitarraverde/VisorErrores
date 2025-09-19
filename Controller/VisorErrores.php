<?php

namespace FacturaScripts\Plugins\VisorErrores\Controller;

use FacturaScripts\Core\Base\Controller;
use FacturaScripts\Core\KernelException;
use FacturaScripts\Core\Tools;

class VisorErrores extends Controller
{
    public array $datosArchivos;

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

        $this->datosArchivos = [];
        foreach ($pathsArchivos as $pathArchivo) {
            // obtenemos la fecha de creación del archivo
            $fecha = date('Y-m-d H:i:s', filectime($pathArchivo));
            $datos = json_decode(file_get_contents($pathArchivo), true);
            $this->datosArchivos[] = [
                'fecha' => $fecha,
                'datos' => $datos
            ];
        }

        // ordenar por fecha
        usort($this->datosArchivos, function ($a, $b) {
            return strtotime($b['fecha']) - strtotime($a['fecha']);
        });
    }
}