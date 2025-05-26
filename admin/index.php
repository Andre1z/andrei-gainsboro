<?php
/**
 * Archivo: index.php (Panel Administrativo)
 *
 * Descripción:
 *   Este archivo es el punto de entrada del panel administrativo del CMS.
 *   Inicia la sesión, carga la configuración básica y redirige el flujo a la
 *   lógica principal del panel, contenida en el archivo "admin.php". De esta forma,
 *   se centraliza la gestión del panel y se facilita su mantenimiento.
 *
 * @package CMS-ANDREI-ADMIN
 */

// Inicia la sesión
session_start();

// Se incluye el archivo principal del panel administrativo, que contiene la
// lógica de autenticación, enrutamiento y renderización de las diferentes secciones.
require_once 'admin.php';