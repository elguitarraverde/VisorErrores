## VisorErrores 1.6

### 🚀 Novedades
- Nueva API REST para consultar y eliminar los errores críticos y consultar los últimos logs de error desde otra instalación, por ejemplo con el plugin MisInstalaciones
- Acceso a la API controlado por el recurso `visorerrores` de la API key (lectura y eliminación por separado)

### ✨ Mejoras
- Textos de la pantalla traducibles
- Los errores con un archivo dañado ya no rompen el listado: simplemente se omiten

### 🐛 Correcciones
- Borrar errores requiere ahora el permiso de eliminación de la página y un token de seguridad, evitando borrados accidentales desde enlaces
- Corregido que el mensaje de un error pudiera inyectar HTML en la pantalla del visor
