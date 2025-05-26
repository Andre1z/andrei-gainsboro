<?php
/**
 * Archivo: config.php
 *
 * Descripción:
 *   Archivo de configuración principal del CMS.
 *   Se define la ruta absoluta de la base de datos y otros parámetros globales.
 *
 * @package CMS-ANDREI
 */

// __DIR__ devuelve la carpeta donde se encuentra este archivo (la raíz del proyecto)
define('ROOT_PATH', __DIR__);

// Define la ruta absoluta al archivo de base de datos (la carpeta "database" está en la raíz)
$dbPath = ROOT_PATH . '/database/cms.db';
?>