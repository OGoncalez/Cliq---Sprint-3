<?php
session_start();

//require 'session.php';
require 'conexao.php';

$conn = $conn ?? $pdo ?? $mysqli ?? null;

if (!isset($conn)) {
    die("Erro: Conexão com banco de dados não encontrada. Verifique o arquivo conexao.php");
}

//Check if user is logged in using existing session structure
if (!isset($_SESSION['usuario'])) {
    $plan = isset($_GET['plan']) ? $_GET['plan'] : 'padrao';
    $_SESSION['redirect_after_login'] = "checkout.php?plan=" . $plan;
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['usuario']['id'];
$stmt = $conn->prepare("SELECT * FROM assinaturas WHERE usuario_id = ? AND status = 'ativa'");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User already has an active subscription, redirect to home
    header('Location: home.php');
    exit();
}

$plan = isset($_GET['plan']) ? $_GET['plan'] : 'padrao';

// Define plan details
$plans = [
    'padrao' => ['name' => 'Padrão', 'price' => 'R$29,99'],
    'super' => ['name' => 'Super', 'price' => 'R$49,99'],
    'vip' => ['name' => 'VIP', 'price' => 'R$69,99']
];

// Get selected plan details
$selectedPlan = isset($plans[$plan]) ? $plans[$plan] : $plans['padrao'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLIQ - Checkout</title>
    <link rel="stylesheet" href="checkout.css">
    <link rel="stylesheet" href="home.css">
</head>
<body>
    <nav class="navbar">
        <img src="midias\LOGO CLIQ DEFINITIVA.png" class="logo" alt="Logo Cliq">
        <div class="nav-list">
        </div>
    </nav>

    <main class="main-content">
        <div class="checkout-container">
            <h1 class="checkout-title">Insira suas informações para o pagamento:</h1>
    
            <form class="checkout-form" method="POST" action="processar_pagamento.php">
                <div class="form-group">
                    <label for="card-number">Número do cartão:</label>
                    <input type="text" id="card-number" name="card_number" maxlength="19" required placeholder="0000 0000 0000 0000">
                </div>

                <div class="form-row">
                    <div class="form-group half">
                        <label for="expiry-date">Data de expiração:</label>
                        <input type="text" id="expiry-date" name="expiry_date" placeholder="MM/AA" maxlength="5" required>
                    </div>
                    <div class="form-group half">
                        <label for="cvv">CVV:</label>
                        <input type="text" id="cvv" name="cvv" maxlength="3" required placeholder="000">
                    </div>
                </div>

                <div class="form-group">
                    <label for="full-name">Nome completo:</label>
                    <input type="text" id="full-name" name="full_name" required>
                </div>

                <div class="form-group">
                    <label for="cpf">CPF:</label>
                    <input type="text" id="cpf" name="cpf" maxlength="14" required placeholder="000.000.000-00">
                </div>

                <div class="form-group">
                    <label for="plan">Plano:</label>
                    <select id="plan" name="plan" required>
                        <option value="padrao" <?php echo $plan === 'padrao' ? 'selected' : ''; ?>>
                            Padrão - R$29,99
                        </option>
                        <option value="super" <?php echo $plan === 'super' ? 'selected' : ''; ?>>
                            Super - R$49,99
                        </option>
                        <option value="vip" <?php echo $plan === 'vip' ? 'selected' : ''; ?>>
                            VIP - R$69,99
                        </option>
                    </select>
                </div>

                <button type="submit" class="submit-button">Confirmar pagamento</button>
            </form>
        </div>
    </main>

    <footer class="footer">
        <p>Cliq© Todos os direitos reservados</p>
    </footer>
</body>
<script src="checkout.js"></script>
</html>
