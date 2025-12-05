<?php require'session.php'; 
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
    <link rel="stylesheet" href="formularios.css">
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Cadastro</title>
</head>
<body>
    <nav class="navbar">
    <img src="midias\LOGO CLIQ DEFINITIVA.png" class="logo" alt="Logo Cliq">
  </nav>
    <div class="container">
        <div class="avatar">
            <i class="fa-solid fa-user"></i>
        </div>
        <h1>Criar conta</h1>
        <?php require 'flash.php'; show_flash()?>
        <form method="POST" action="cadastrar.php">
            <div class="input-group">
                <i class="fa-solid fa-user"></i>
                <input type="text" id="nome" name="nome" placeholder="Nome" required>
            </div>
            <div class="input-group">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" id="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" id="senha" name="senha" minlength="6" placeholder="Senha" required>
            </div>
            <button type="submit">Cadastrar</button>
        </form>
        <p>Já tem uma conta? <a href="login.php">Login</a></p>
    </div>

       <footer class="footer">
    <p>© 2025 - CLIQ Streaming</p>
  </footer>
</body>
</html>