<?php
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

if (!function_exists('set_flash')) {
    function set_flash(string $title, string $message, string $type = 'info') {
        if (session_status() === PHP_SESSION_NONE) @session_start();
        $_SESSION['flash'] = [
            'title' => $title,
            'message' => $message,
            'type' => $type
        ];
    }
}

if (!function_exists('show_flash')) {
    function show_flash() {
        if (session_status() === PHP_SESSION_NONE) @session_start();
        if (!empty($_SESSION['flash'])) {
            $f = $_SESSION['flash'];
            // saída simples; ajuste HTML/CSS conforme seu projeto
            echo '<div class="flash ' . htmlspecialchars($f['type']) . '">
                 ' . htmlspecialchars($f['title']) . ' - ' . htmlspecialchars($f['message'])
                 . '</div>';
            unset($_SESSION['flash']);
        }
    }
}
?>