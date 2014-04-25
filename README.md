# Léeme

Aquí subo actualizaciones que hago a los archivos en diferentes commits, así que pueden tener errores ostensibles u otros problemas. Elige una tag para elegir una versión (lanzada) en específico.

## TrackYourPenguin

TrackYourPenguin es un sistema multiusuario que te permite trackear fácilmente con múltiples trackers.

### Instalación

Para instalar, debes tener un hostingM **recomiendo nixiweb.com**. Y hacer lo siguiente.

Normal
------

- Descomprime el archivo en tu servidor.
- Ejecuta el archivo instalar.php
- Sigue los pasos.

Manual
------
- Descomprime los archivos en tu servidor.
- Edita el archivo typ-config.php con los datos que te pide.
- Ejecuta instalar.php

### Requisitos

- **PHP 5.0.0 o posterior**. *Para mejor compatibilidad.*
- **MySQL 4.0 o posterior**.
- **cURL**. *La mayoría de los servidores de los hostings lo traen. Esto es sólo para las actualizaciones.*
- **ZipArchive**. *Para descomprimir los archivos durante la actualización.*

### Troubleshooting

Si tienes problemas al subir archivos en tu localhost (Linux), cambia los permisos ejecutando:
'''sh
chmod -R 777 /var/www/typ/
'''

Cambiando /var/www/ por la ubicación a tus archivos.

Disfruta. :)!