<?php

namespace FacturaScripts\Plugins\VisorErrores\Lib;

use FacturaScripts\Core\Tools;
use FacturaScripts\Core\Where;
use FacturaScripts\Dinamic\Model\LogMessage;

class GestorErrores
{
    public static function eliminar(string $hash): bool
    {
        if (1 !== preg_match('/^[a-f0-9]{32}$/D', $hash)) {
            return false;
        }

        $path = Tools::folder('MyFiles', 'crash_' . $hash . '.json');
        return is_file($path) && unlink($path);
    }

    /** @return int[] [eliminados, fallidos] */
    public static function eliminarTodos(): array
    {
        $eliminados = 0;
        $fallidos = 0;
        foreach (self::archivos() as $path) {
            unlink($path) ? $eliminados++ : $fallidos++;
        }
        return [$eliminados, $fallidos];
    }

    public static function listarCriticos(): array
    {
        $errores = [];
        foreach (self::archivos() as $path) {
            $datos = json_decode((string)file_get_contents($path), true);
            if (false === is_array($datos)) {
                continue;
            }

            $errores[] = [
                'fecha' => $datos['created_at'] ?? date('Y-m-d H:i:s', (int)filemtime($path)),
                'hash' => $datos['hash'] ?? substr(basename($path, '.json'), 6),
                'archivo' => basename($path),
                'datos' => $datos,
            ];
        }

        usort($errores, fn($a, $b) => strtotime($b['fecha']) - strtotime($a['fecha']));
        return $errores;
    }

    public static function listarLogs(int $limite = 50): array
    {
        $logs = LogMessage::all([Where::eq('level', 'error')], ['time' => 'DESC'], 0, $limite);
        foreach ($logs as $log) {
            $log->context = json_decode(htmlspecialchars_decode((string)$log->context), true);
        }
        return $logs;
    }

    private static function archivos(): array
    {
        return glob(Tools::folder('MyFiles', 'crash_*.json')) ?: [];
    }
}
