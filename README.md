Andrei | Gainsboro CMS
======================

Descripción:
-------------
Andrei | Gainsboro es un sistema de gestión de contenidos (CMS) desarrollado en PHP con base de datos SQLite.  
La aplicación permite crear y editar dinámicamente páginas, entradas de blog, gestionar contactos y configurar  
varios elementos del sitio (por ejemplo, menús, héroes, redes sociales y estilos personalizados).  
También incluye un panel de administración para gestionar el contenido de forma integral.

Requisitos:
------------
- Servidor web: Apache (por ejemplo, usando XAMPP o LAMP)
- PHP 7.x o superior, con soporte para SQLite3 (extensión php-sqlite3)
- SQLite3

Instalación:
------------
1. Clona o copia el proyecto en la carpeta raíz de tu servidor web.
   Ejemplo: C:\xampp\htdocs\andrei-gainsboro

2. Verifica el archivo de configuración (config.php) y asegúrate de que la variable $dbPath apunte  
   a la ubicación correcta de la base de datos (por defecto, "database/cms.db" en la raíz del proyecto).

3. La aplicación creará automáticamente las tablas necesarias (pages, blog, config, contact, heroes,  
   social_media, custom_css) si estas no existen.

4. Ajusta los permisos de las carpetas relevantes, especialmente la carpeta "database" y cualquier otra  
   que requiera escritura, para que el servidor (generalmente el usuario "www-data" en Linux) tenga acceso.

5. Accede a la aplicación:
   - Sitio Público: http://localhost/andrei-gainsboro/index.php
   - Panel de Administración: http://localhost/andrei-gainsboro/admin/admin.php

Estructura del Proyecto:
-------------------------
andrei-gainsboro/
├── admin/
│   ├── acciones/         (Scripts para cada acción del panel administrativo)
│   ├── funciones/        (Funciones auxiliares para manejo de login, renderizado, etc.)
│   ├── inc/              (Inicialización de la base de datos)
│   ├── rutas/            (Scripts de rutas y autenticación)
│   └── admin.php         (Punto de entrada del panel administrativo)
├── css/                  (Hojas de estilo del sitio, por temas)
├── database/             (Archivo de la base de datos SQLite, creado automáticamente)
├── img/                  (Imágenes utilizadas en el sitio)
├── index.php             (Punto de entrada del sitio público)
├── README.txt            (Este archivo)
└── sitemap.xml           (Generado automáticamente)

Uso:
-----
- Sitio Público:
  El sitio se renderiza dinámicamente según el parámetro GET "page". Se muestran las páginas creadas,  
  entradas del blog, el formulario de contacto y la sección "hero" (si está configurada) en base a la  
  información almacenada en la base de datos.

- Panel de Administración:
  Permite gestionar de forma integral el contenido del sitio. Se pueden crear, editar y eliminar páginas,  
  entradas del blog, héroes, redes sociales, archivos multimedia, reglas de CSS personalizado y configurar  
  otros parámetros del sitio. La autenticación es necesaria para acceder al panel.
  
- Navegación:
  La navegación primaria se genera dinámicamente. El enlace "Inicio" solo se muestra si existe una página  
  titulada "inicio" en la base de datos. Además, se añaden de forma fija los enlaces para "Blog" y "Contacto".  

Despliegue en Ubuntu (Prueba sin VirtualHost):
-----------------------------------------------
1. Actualiza el sistema e instala Apache, PHP y SQLite3:
   
   sudo apt update  
   sudo apt upgrade -y  
   sudo apt install apache2 php libapache2-mod-php php-sqlite3 -y

2. Crea la carpeta del proyecto en la ubicación por defecto de Apache:
   
   sudo mkdir -p /var/www/html/andrei-gainsboro  
   sudo chown -R $USER:$USER /var/www/html/andrei-gainsboro

3. Copia o clona el proyecto en /var/www/html/andrei-gainsboro.

4. Ajusta los permisos de la carpeta "database" para garantizar la escritura:
   
   sudo chown -R www-data:www-data /var/www/html/andrei-gainsboro/database  
   sudo chmod -R 755 /var/www/html/andrei-gainsboro/database

5. Reinicia Apache:
   
   sudo systemctl restart apache2

6. Accede a la aplicación desde tu navegador:  
   http://localhost/andrei-gainsboro

Notas Adicionales:
------------------
- La aplicación crea automáticamente las tablas necesarias al cargar index.php (y en el panel de administración).  
- El sistema de temas permite cambiar la apariencia del sitio a través de hojas de estilo ubicadas en la carpeta "css".  
- El script de analytics (con la URL definida en la configuración) se carga en el footer del sitio.
- Si se utiliza UFW (firewall), asegúrese de permitir el tráfico HTTP:
  
   sudo ufw allow in "Apache Full"
Contacto:
---------
Para cualquier duda, sugerencia o soporte, comunícate con Andrei Buga al correo: bugaandrei1@gmail.com
