<?php
require 'session.php';
require 'flash.php';
require 'conexao.php';

$email = trim($_POST['email']) ?? '';
$password = $_POST['senha'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    set_flash('Erro', 'Preencha os campos corretamente.');
    header('Location: login.php');
    exit;
}

// MODIFICAÇÃO AQUI: Buscar também o perfil do usuário
$stmt = $conn->prepare("SELECT id, nome, passHash, perfil FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || !password_verify($password, $user['passHash'])) {
    set_flash('Erro', 'Email ou senha inválidos.');
    header('Location: login.php'); 
    exit;
}

session_regenerate_id(true);

// MODIFICAÇÃO AQUI: Incluir o perfil na sessão
$_SESSION['usuario'] = [
    'id' => $user['id'],
    'nome' => $user['nome'],
    'email' => $email,
    'perfil' => $user['perfil'] // Adicionado o perfil
];

$stmt = $conn->prepare("SELECT id FROM assinaturas WHERE usuario_id = ? AND status = 'ativa' LIMIT 1");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$subscription = $stmt->get_result()->fetch_assoc();

// MODIFICAÇÃO AQUI: Lógica de redirecionamento para admin
if ($user['perfil'] === 'admin') {
    // Se é admin, redireciona para CRUDusers.php
    set_flash('Sucesso', 'Login administrativo realizado com sucesso!');
    header('Location: CRUDusers.php');
    exit;
}

if ($subscription) {
    // User tem assinatura ativa
    set_flash('Sucesso', 'Login realizado com sucesso!');
    
    // Verificar se já tem perfil ativo
    if (isset($_SESSION['perfil_ativo'])) {
        header('Location: home.php');
    } else {
        header('Location: perfil.php');
    }
    exit;
}

if (isset($_SESSION['redirect_after_login'])) {
    $redirect_url = $_SESSION['redirect_after_login'];
    unset($_SESSION['redirect_after_login']);
    set_flash('Sucesso', 'Login realizado! Escolha um plano para continuar.');
    header('Location: ' . $redirect_url);
    exit;
}

set_flash('Sucesso', 'Login realizado! Escolha um plano para começar.');
header('Location: planos.php');
exit;
?>