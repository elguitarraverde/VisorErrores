# Visor de Errores

Plugin para FacturaScripts que permite visualizar de forma centralizada los errores críticos detectados en el ERP.

## Características

- Visualización de los archivos `crash_*.json` generados por el sistema, ordenados del más reciente al más antiguo.
- Pestaña con los 50 últimos logs de nivel error.
- Borrado de un error o de todos (requiere permiso de eliminación en la página).
- **Desde la 1.6:** API REST para consultar y borrar los errores desde otra instalación (por ejemplo, con el plugin MisInstalaciones).
- Acceso desde el menú de administración.

## Requisitos

- FacturaScripts 2025.6 o superior.

## API REST (1.6+)

Requiere la API activada y una API key con acceso al recurso `visorerrores` (o acceso completo).

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/api/3/visorerrores/errores` | Lista los errores críticos |
| GET | `/api/3/visorerrores/errores/{hash}` | Detalle de un error |
| GET | `/api/3/visorerrores/logs` | Últimos 50 logs de error |
| DELETE | `/api/3/visorerrores/errores/{hash}` | Elimina un error |
| DELETE | `/api/3/visorerrores/errores` | Elimina todos los errores; devuelve `eliminados` y `fallidos` |

## Enlaces

- [Página del plugin en FacturaScripts](https://facturascripts.com/plugins/visorerrores)

## Licencia

LGPL, igual que FacturaScripts.
