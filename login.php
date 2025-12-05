<?php require 'session.php';
require 'conexao.php' ;

if (!empty($_SESSION['usuario'])){
    header('Location: home.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="formularios.css">
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <nav class="navbar">
        <img src="midias\LOGO CLIQ DEFINITIVA.png" class="logo" alt="Logo Cliq">
    </nav>

    <div class="container">
        <div class="avatar">
            <i class="fa-solid fa-user"></i>
        </div>
        <h1>Login</h1>
        <form method="POST" action="autenticar.php">
            <?php require 'flash.php';
            show_flash() ?>
            <div class="input-group">
                <i class="fa-solid fa-user"></i>
                <input type="email" id="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" id="senha" name="senha" placeholder="Senha" required>
            </div>
            <button type="submit">LOGIN</button>
        </form>
        <p>Não tem uma conta? <a href="cadastrese.php">Cadastre-se</a></p>
    </div>

    
  <footer class="footer">
    <p>© 2025 - CLIQ Streaming</p>
  </footer>
</body>

</html>