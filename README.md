# Visor de Errores

Plugin para FacturaScripts que permite visualizar de forma centralizada los errores críticos detectados en el ERP.

## Descripción

Este plugin proporciona una interfaz sencilla y ordenada para consultar todos los errores críticos (crash reports) que se han generado en FacturaScripts. Los archivos de error se visualizan de forma cronológica, mostrando la fecha de ocurrencia y el mensaje de error completo.

## Características

- Visualización centralizada de todos los archivos crash*.json generados por el sistema
- Ordenación cronológica de errores (más recientes primero)
- Interfaz limpia y responsive con tarjetas individuales para cada error
- Acceso desde el menú de administración
- Compatible con FacturaScripts 2024 y versiones posteriores

## Requisitos

- FacturaScripts versión 2025 o superior

## Instalación

1. Descarga el plugin desde el [Marketplace de FacturaScripts](https://facturascripts.com/plugins/visorerrores)
2. Sube el plugin a tu instalación de FacturaScripts
3. Activa el plugin desde el panel de administración

## Uso

Una vez instalado y activado, el plugin añade una nueva opción en el menú de administración llamada "Visor de Errores" con el icono de triángulo de exclamación.

Accede a esta sección para visualizar todos los errores críticos que se han producido en tu sistema. Cada error se muestra en una tarjeta individual con:

- Fecha y hora de ocurrencia
- Mensaje de error completo con trazas de ejecución

Si no hay errores registrados, se mostrará un mensaje informativo.

## Desarrollo

El plugin consta de:

- `Controller/VisorErrores.php` - Controlador principal que lee los archivos de error
- `View/VisorErrores.html.twig` - Vista Twig para mostrar los errores
- `facturascripts.ini` - Configuración del plugin

## Enlaces

- [Página del plugin en FacturaScripts](https://facturascripts.com/plugins/visorerrores)
- [Documentación de FacturaScripts](https://facturascripts.com/ayuda)

## Licencia

Este plugin sigue la misma licencia que FacturaScripts (LGPL).