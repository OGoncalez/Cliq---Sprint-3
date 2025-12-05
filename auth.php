<?php 
require "session.php";

if (empty($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

// auth.php - arquivo alternativo
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function check_admin_access() {
    if (!isset($_SESSION['usuario'])) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit;
    }
    
    if (!isset($_SESSION['usuario']['perfil']) || $_SESSION['usuario']['perfil'] !== 'admin') {
        if (function_exists('set_flash')) {
            set_flash('Acesso Negado', 'Permissão insuficiente.');
        }
        header('Location: home.php');
        exit;
    }
    
    return true;
}

function user_is_admin() {
    return isset($_SESSION['usuario']['perfil']) && $_SESSION['usuario']['perfil'] === 'admin';
}
?>