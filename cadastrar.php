<?php
require 'session.php';
require 'conexao.php';
require 'flash.php';

$nome = trim($_POST['nome']?? '') ;
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha']??'' ;

$stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
  set_flash('Erro', 'E-mail já cadastrado.');
  header('Location: index.php'); exit;
}

$stmt->close();

$hash = password_hash($senha    , PASSWORD_DEFAULT);
$stmt = $conn->prepare('INSERT INTO usuarios (nome, email, passHash) VALUES (?, ?, ?)');
$stmt->bind_param('sss', $nome, $email,$hash);

if($stmt->execute()){
    set_flash(  'Sucesso','Cadastrado com sucesso! Efetue seu login.',);
    header('Location: login.php');
    exit;  
} else {
    set_flash("Erro","Erro ao cadastrar: ");
    header("Location: index.php");
    exit;
}
?>