<?php

class AuthMiddleware
{
    /**
     * Verifica si el usuario ha iniciado sesión.
     * Si no está autenticado, lo redirige a la página de login.
     */
    public static function checkAuth()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario'])) {
            header('Location: /perunet/login');
            exit;
        }
    }

    /**
     * Verifica si el usuario es un administrador.
     * Si no está autenticado o no tiene el rol 'admin', lo redirige.
     */
    public static function checkAdmin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Primero, verifica si está logueado.
        if (!isset($_SESSION['usuario'])) {
            header('Location: /perunet/login');
            exit;
        }

        // Luego, verifica si es administrador.
        if ($_SESSION['usuario']['rol'] !== 'admin') {
            // Si no es admin, lo redirigimos a la página principal (o a una de error).
            header('Location: /');
            exit;
        }
    }
} 