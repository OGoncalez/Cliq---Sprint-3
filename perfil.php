<?php
require 'session.php';
require 'conexao.php';
require 'flash.php';
require 'auth.php';

// Criar diretório de uploads se não existir
if (!file_exists('uploads')) {
    mkdir('uploads', 0755, true);
}

// Criar arquivo de perfis se não existir
if (!file_exists('profiles.json')) {
    if (!file_exists('data')) {
        mkdir('data', 0755, true);
    }
    $defaultProfiles = [
        ["id" => 1, "name" => "Pai", "image" => "uploads/default-avatar.jpg"],
        ["id" => 2, "name" => "Mãe", "image" => "uploads/default-avatar.jpg"],
        ["id" => 3, "name" => "Filho", "image" => "uploads/default-avatar.jpg"],
        ["id" => 4, "name" => "Filha", "image" => "uploads/default-avatar.jpg"]
    ];
    file_put_contents('data/profiles.json', json_encode($defaultProfiles, JSON_PRETTY_PRINT));
}

// Carregar perfis
$profilesJson = file_get_contents('data/profiles.json');
$profiles = json_decode($profilesJson, true);

if (!$profiles) {
    $profiles = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cliq - Perfis</title>
    <link rel="stylesheet" href="perfil.css">
    <link rel="stylesheet" href="home.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>
   <nav class="navbar">
        <img src="midias\LOGO CLIQ DEFINITIVA.png" class="logo" alt="Logo Cliq">
        <div class="nav-list">
            <a href="home.php">Home</a>
            <a href="kids.php">Infantil</a>
            <a href="logout.php">Sair</a>
        </div>
    </nav>

    <div class="profile-box">
        <h2>Quem está assistindo?</h2>
        <div class="profiles" id="profilesContainer">
            <?php foreach ($profiles as $profile): ?>
                <div class="profile" data-id="<?php echo htmlspecialchars($profile['id']); ?>">
                    <div class="profile-image-wrapper">
                        <img src="<?php echo htmlspecialchars($profile['image']); ?>" 
                             alt="<?php echo htmlspecialchars($profile['name']); ?>"
                             onerror="this.src='uploads/default-avatar.jpg'">
                        <button class="edit-btn" onclick="openEditModal(<?php echo $profile['id']; ?>, '<?php echo htmlspecialchars($profile['name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($profile['image'], ENT_QUOTES); ?>')" title="Editar perfil">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </button>
                    </div>
                    <p><?php echo htmlspecialchars($profile['name']); ?></p>
                </div>
            <?php endforeach; ?>

            <div class="profile add-profile" onclick="openAddModal()">
                <div class="add-circle">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </div>
                <p>Adicionar</p>
            </div>
        </div>
    </div>

    <!-- Modal de Edição/Adição -->
    <div id="profileModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3 id="modalTitle">Editar Perfil</h3>
            
            <form id="profileForm" enctype="multipart/form-data">
                <input type="hidden" id="profileId" name="profileId">
                
                <div class="form-group">
                    <label for="profileName">Nome do Perfil:</label>
                    <input type="text" id="profileName" name="profileName" required maxlength="50" placeholder="Digite o nome">
                </div>

                <div class="form-group">
                    <label>Imagem do Perfil:</label>
                    <div class="image-preview-container">
                        <img id="imagePreview" src="uploads/default-avatar.jpg" alt="Preview">
                    </div>
                    <input type="file" id="profileImage" name="profileImage" accept="image/jpeg,image/jpg,image/png,image/gif" onchange="previewImage(this)">
                    <small>Formatos aceitos: JPG, PNG, GIF (máx. 5MB)</small>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="deleteBtn" onclick="deleteProfile()">Excluir</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>

            <div id="formMessage" class="form-message"></div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="spinner"></div>
    </div>
 <footer class="footer">
    <div class="footer-content">
        <!-- Seção Sobre -->
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

        <!-- Seção Navegação -->
        <div class="footer-section">
            <h3>Navegação</h3>
            <ul class="footer-links">
                <li><a href="home.php">Home</a></li>
                <li><a href="kids.php">Infantil</a></li>
                <li><a href="perfil.php">Perfil</a></li>
            </ul>
        </div>

        <!-- Seção Suporte -->
        <div class="footer-section">
            <h3>Suporte</h3>
            <ul class="footer-links">
            </ul>
            <button class="chatbot-trigger" onclick="openChatbot()">
                <ion-icon name="chatbubble-ellipses"></ion-icon>
                Chat de Suporte
            </button>
        </div>

        <!-- Seção Empresa -->
        <div class="footer-section">
            <h3>Empresa</h3>
            <ul class="footer-links">
                <li><a href="#sobre">Sobre Nós</a></li>
            </ul>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <p>© 2025 CLIQ Streaming. Todos os direitos reservados.</p>
</footer>

<!-- Chatbot Modal -->
<div id="chatbotModal" class="chatbot-modal">
    <div class="chatbot-header">
        <h4><img src="midias\criquetinho.png" class="cliquetinho"> Suporte CLIQ</h4>
        <button class="chatbot-close" onclick="closeChatbot()">
            <ion-icon name="close"></ion-icon>
        </button>
    </div>
    <div class="chatbot-body" id="chatbotMessages">
        <div class="chat-message bot-message">
             Olá! Sou o assistente virtual da Cliq, O Cliquetinho! 
             Como posso ajudar você hoje?
        </div>
        <div class="chat-message bot-message">
            Escolha uma opção:<br>
            1. Problemas com reprodução<br>
            2. Dúvidas sobre conta<br>
            3. Assinatura e pagamentos<br>
            4. Sugestões de conteúdo<br>
            5. Falar com atendente
        </div>
    </div>
    <div class="chatbot-input">
        <input type="text" id="chatbotInput" placeholder="Digite sua mensagem...">
        <button onclick="sendMessage()">
            <ion-icon name="send"></ion-icon>
        </button>
    </div>
</div>

    <script src="perfil.js"></script>
    <script src="home.js"></script>
</body>
</html>
