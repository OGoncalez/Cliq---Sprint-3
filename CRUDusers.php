<?php
// CRUDusers.php - VERSÃO CORRIGIDA
// Incluir os arquivos necessários PRIMEIRO
require 'session.php';
require_once 'flash.php';
require 'conexao.php';

if (!function_exists('is_admin')) {
    require_once 'session.php'; // Use require_once
}

require_auth('admin');

// Agora verificar a autenticação
if (!isset($_SESSION['usuario'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit;
}

// Verificar se é admin
if (!isset($_SESSION['usuario']['perfil']) || $_SESSION['usuario']['perfil'] !== 'admin') {
    set_flash('Acesso Negado', 'Você não tem permissão para acessar esta página.');
    header('Location: home.php');
    exit;
}

// O RESTANTE DO SEU CÓDIGO CRUDusers.php CONTINUA AQUI...
// Database connection
$host = 'localhost';
$dbname = 'db_Cliq';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_user':
                createUser($pdo);
                break;
            case 'update_user':
                updateUser($pdo);
                break;
            case 'delete_user':
                deleteUser($pdo);
                break;
            case 'create_subscription':
                createSubscription($pdo);
                break;
            case 'update_subscription':
                updateSubscription($pdo);
                break;
            case 'cancel_subscription':
                cancelSubscription($pdo);
                break;
        }
    }
}

function createUser($pdo) {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($nome) || empty($email) || empty($password)) {
        $_SESSION['message'] = "Todos os campos são obrigatórios!";
        $_SESSION['message_type'] = "error";
        header("Location: CRUDusers.php");
        exit();
    }
    
    $passHash = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, passHash) VALUES (?, ?, ?)");
        $stmt->execute([$nome, $email, $passHash]);
        
        $_SESSION['message'] = "Usuário criado com sucesso!";
        $_SESSION['message_type'] = "success";
    } catch(PDOException $e) {
        $_SESSION['message'] = "Erro: Este email já está em uso!";
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: CRUDusers.php");
    exit();
}

function updateUser($pdo) {
    // Debug
    error_log("UPDATE USER - Dados recebidos: " . print_r($_POST, true));
    
    $id = $_POST['id'] ?? null;
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = $_POST['password'] ?? '';
    
    if (empty($id) || empty($nome) || empty($email)) {
        $_SESSION['message'] = "ID, nome e email são obrigatórios!";
        $_SESSION['message_type'] = "error";
        header("Location: CRUDusers.php");
        exit();
    }
    
    try {
        if (!empty($password)) {
            // atualizar também a senha (hash)
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, passHash = ? WHERE id = ?");
            $result = $stmt->execute([$nome, $email, $hash, $id]);
        } else {
            // atualizar somente nome e email
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
            $result = $stmt->execute([$nome, $email, $id]);
        }
        
        if ($result && $stmt->rowCount() > 0) {
            $_SESSION['message'] = "Usuário atualizado com sucesso!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Nenhuma alteração realizada ou usuário não encontrado.";
            $_SESSION['message_type'] = "error";
        }
    } catch(PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['message'] = "Erro: Este email já está em uso!";
        } else {
            $_SESSION['message'] = "Erro ao atualizar usuário: " . $e->getMessage();
        }
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: CRUDusers.php");
    exit();
}

function deleteUser($pdo) {
    $id = $_POST['id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['message'] = "Usuário excluído com sucesso!";
        $_SESSION['message_type'] = "success";
    } catch(PDOException $e) {
        $_SESSION['message'] = "Erro ao excluir usuário!";
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: CRUDusers.php");
    exit();
}

function createSubscription($pdo) {
    $usuario_id = $_POST['usuario_id'];
    $plano = $_POST['plano'];
    
    // Define preços dos planos
    $precos = [
        'Padrão' => 24.99,
        'Super' => 49.99,
        'VIP' => 69.99
    ];
    
    $preco = $precos[$plano];
    $data_expiracao = date('Y-m-d H:i:s', strtotime('+30 days'));
    
    try {
        // Cancel existing active subscription
        $stmt = $pdo->prepare("UPDATE assinaturas SET status = 'cancelada' WHERE usuario_id = ? AND status = 'ativa'");
        $stmt->execute([$usuario_id]);
        
        // Create new subscription
        $stmt = $pdo->prepare("INSERT INTO assinaturas (usuario_id, plano, preco, data_expiracao) VALUES (?, ?, ?, ?)");
        $stmt->execute([$usuario_id, $plano, $preco, $data_expiracao]);
        
        $_SESSION['message'] = "Assinatura criada com sucesso!";
        $_SESSION['message_type'] = "success";
    } catch(PDOException $e) {
        $_SESSION['message'] = "Erro ao criar assinatura!";
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: CRUDusers.php");
    exit();
}

function updateSubscription($pdo) {
    $id = $_POST['subscription_id'];
    $plano = $_POST['plano'];
    $status = $_POST['status'];
    
    // Define preços dos planos
    $precos = [
        'Padrão' => 24.99,
        'Super' => 49.99,
        'VIP' => 69.99
    ];
    
    $preco = $precos[$plano];
    
    try {
        $stmt = $pdo->prepare("UPDATE assinaturas SET plano = ?, preco = ?, status = ? WHERE id = ?");
        $stmt->execute([$plano, $preco, $status, $id]);
        
        $_SESSION['message'] = "Assinatura atualizada com sucesso!";
        $_SESSION['message_type'] = "success";
    } catch(PDOException $e) {
        $_SESSION['message'] = "Erro ao atualizar assinatura!";
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: CRUDusers.php");
    exit();
}

function cancelSubscription($pdo) {
    $id = $_POST['subscription_id'];
    
    try {
        $stmt = $pdo->prepare("UPDATE assinaturas SET status = 'cancelada' WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['message'] = "Assinatura cancelada com sucesso!";
        $_SESSION['message_type'] = "success";
    } catch(PDOException $e) {
        $_SESSION['message'] = "Erro ao cancelar assinatura!";
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: CRUDusers.php");
    exit();
}

// Get all users with their active subscriptions
$stmt = $pdo->query("
    SELECT u.*, 
           a.id as assinatura_id,
           a.plano, 
           a.preco, 
           a.status as assinatura_status,
           a.data_inicio,
           a.data_expiracao
    FROM usuarios u
    LEFT JOIN assinaturas a ON u.id = a.usuario_id AND a.status = 'ativa'
    ORDER BY u.created_at DESC
");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <title>CRUD Users - Cliq</title>
  <link rel="stylesheet" href="CRUDusers.css" />
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>

<div class="navbar">
  <img src="midias\LOGO CLIQ DEFINITIVA.png" alt="" class="logo">
  <li><a href="CRUDusers.php">Gerenciar usuários</a></li>
  <li><a href="CRUD.php">Gerenciar filmes</a></li>
  <div class="search-box">
    <input type="text" id="searchInput" placeholder="Pesquisar usuário...">
  </div>

  <button id="filterBtn">Filtrar</button>
</div>

<div id="filterCard" class="filter-card">
    <label>Ordenar por:</label>
    <select id="sortFilter">
      <option value="newest">Mais Recentes</option>
      <option value="oldest">Mais Antigos</option>
      <option value="name">Nome (A-Z)</option>
      <option value="subscription">Com Assinatura</option>
      <option value="no-subscription">Sem Assinatura</option>
    </select>
</div>

<div class="users-container">
  <?php if (isset($_SESSION['message'])): ?>
    <div class="message <?php echo $_SESSION['message_type']; ?>">
      <?php 
        echo $_SESSION['message']; 
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
      ?>
    </div>
  <?php endif; ?>

  <h2>Gerenciar Usuários</h2>
  
  <div class="users-grid" id="usersGrid">
    <?php foreach ($users as $user): 
      $hasActiveSubscription = !empty($user['assinatura_id']);
    ?>
      <div class="user-card" data-user-id="<?php echo $user['id']; ?>">
        <h3><?php echo htmlspecialchars($user['nome']); ?></h3>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Criado em:</strong> <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></p>
        
        <div class="subscription-info">
          <h4>Assinatura:</h4>
          <?php if ($hasActiveSubscription): 
            $isExpired = strtotime($user['data_expiracao']) < time();
            $statusClass = $isExpired ? 'status-expirada' : 'status-ativa';
            $statusText = $isExpired ? 'expirada' : $user['assinatura_status'];
          ?>
            <p>
              <span class="plano-badge"><?php echo htmlspecialchars($user['plano']); ?></span>
              <span class="price-tag">R$ <?php echo number_format($user['preco'], 2, ',', '.'); ?></span>
              <span class="subscription-status <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
            </p>
            <p><strong>Início:</strong> <?php echo date('d/m/Y', strtotime($user['data_inicio'])); ?></p>
            <p><strong>Expira em:</strong> <?php echo date('d/m/Y', strtotime($user['data_expiracao'])); ?></p>
            
            <div class="subscription-actions">
              <button class="edit-sub-btn" onclick="openEditSubscriptionModal(<?php echo $user['assinatura_id']; ?>, '<?php echo htmlspecialchars($user['plano']); ?>', '<?php echo $user['assinatura_status']; ?>', <?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['nome']); ?>')">Editar Assinatura</button>
              <form method="POST" style="display: inline;">
                <input type="hidden" name="action" value="cancel_subscription">
                <input type="hidden" name="subscription_id" value="<?php echo $user['assinatura_id']; ?>">
                <button type="submit" class="cancel-sub-btn" onclick="return confirm('Tem certeza que deseja cancelar esta assinatura?')">Cancelar</button>
              </form>
            </div>
          <?php else: ?>
            <p class="no-subscription">Nenhuma assinatura ativa</p>
            <button class="add-sub-btn" onclick="openAddSubscriptionModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['nome']); ?>')">Adicionar Assinatura</button>
          <?php endif; ?>
        </div>
        
        <div class="user-actions">
          <button class="edit-btn" onclick="openEditModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['nome'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($user['email'], ENT_QUOTES); ?>')">
            Editar Usuário
          </button>
          <form method="POST" style="display: inline;">
            <input type="hidden" name="action" value="delete_user">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
            <button type="submit" class="delete-btn" onclick="return confirm('Tem certeza que deseja excluir este usuário?')">Excluir Usuário</button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<button id="addBtn" class="add-btn"><span class="plus">+</span></button>

<!-- User Modal - VERSÃO CORRIGIDA -->
<div id="userModal" class="modal hidden">
  <div class="modal-content">
    <form id="userForm" method="POST">
      <h3 id="modalTitle">Adicionar Usuário</h3>
      <input type="hidden" name="action" value="create_user" id="formAction">
      <input type="hidden" name="id" id="userId">
      
      <div class="form-group">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" placeholder="Nome completo" required>
      </div>
      
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Email" required>
      </div>
      
      <div class="form-group" id="passwordGroup">
        <label for="password">Senha:</label>
        <input type="password" id="password" name="password" placeholder="Senha" required>
      </div>
      
      <div class="actions">
        <button type="submit" class="btn-save">Salvar</button>
        <button type="button" id="cancelBtn" class="btn-cancel">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- Subscription Modal -->
<div id="subscriptionModal" class="modal hidden">
  <form id="subscriptionForm" method="POST">
    <h3 id="subscriptionModalTitle">Adicionar Assinatura</h3>
    <input type="hidden" name="action" value="create_subscription" id="subscriptionFormAction">
    <input type="hidden" name="subscription_id" id="subscriptionId">
    <input type="hidden" name="usuario_id" id="usuarioId">
    
    <div class="form-group">
      <label for="userName">Usuário:</label>
      <input type="text" id="userName" readonly style="background-color: #f0f0f0;">
    </div>
    
    <div class="form-group">
      <label for="plano">Plano:</label>
      <select id="plano" name="plano" required>
        <option value="Padrão">Padrão - R$ 24,99</option>
        <option value="Super">Super - R$ 49,99</option>
        <option value="VIP">VIP - R$ 69,99</option>
      </select>
    </div>
    
    <div class="form-group" id="subscriptionStatusGroup" style="display: none;">
      <label for="status">Status:</label>
      <select id="status" name="status">
        <option value="ativa">Ativa</option>
        <option value="cancelada">Cancelada</option>
        <option value="expirada">Expirada</option>
      </select>
    </div>
    
    <div class="actions">
      <button type="submit">Salvar</button>
      <button type="button" id="cancelSubscriptionBtn">Cancelar</button>
    </div>
  </form>
</div>

<footer class="footer">
    <div class="footer-content">
        <div class="footer-section">
            <div class="footer-logo">Cliq</div>
            <p class="footer-description">
                Sua plataforma de streaming favorita com os melhores filmes e séries. 
                Assista onde quiser, quando quiser.
            </p>
            <div class="social-links">
                <a href="#" class="social-btn" title="Facebook">
                    <ion-icon name="logo-facebook"></ion-icon>
                </a>
                <a href="#" class="social-btn" title="Instagram">
                    <ion-icon name="logo-instagram"></ion-icon>
                </a>
                <a href="#" class="social-btn" title="YouTube">
                    <ion-icon name="logo-youtube"></ion-icon>
                </a>
                <a href="#" class="social-btn" title="TikTok">
                    <ion-icon name="logo-tiktok"></ion-icon>
                </a>
            </div>
        </div>

        <div class="footer-section">
            <h3>Navegação</h3>
            <ul class="footer-links">
                <li><a href="home.php">Home</a></li>
                <li><a href="kids.php">Infantil</a></li>
                <li><a href="perfil.php">Perfil</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h3>Empresa</h3>
            <ul class="footer-links">
                <li><a href="sobre.php">Sobre Nós</a></li>
                <li><a href="CRUDusers.php">Gerenciar Usuários</a></li>
                <li><a href="CRUD.php">Gerenciar Filmes</a></li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <p>© 2025 CLIQ Streaming. Todos os direitos reservados.</p>
    </div>
</footer>

<script src="CRUDusers.js"></script>
</body>
</html>