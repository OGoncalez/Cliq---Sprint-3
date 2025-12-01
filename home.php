<?php
require_once 'session.php';
require_once 'flash.php';
require 'conexao.php'; // Sua conexão com MySQL

// Verificar autenticação
if (empty($_SESSION['usuario'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/home.php';
    header('Location: login.php');
    exit;
}

if (empty($_SESSION['perfil_ativo'])) {
    header('Location: perfil.php');
    exit;
}

// Verificar assinatura ativa — se não for admin, exigir assinatura
if (!function_exists('is_admin') || !is_admin()) {
    $userId = $_SESSION['usuario']['id'] ?? null;
    if ($userId === null) {
        header('Location: login.php');
        exit;
    }

    try {
        $stmt = $conn->prepare("SELECT 1 FROM assinaturas WHERE usuario_id = ? AND status = 'ativa' LIMIT 1");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        $hasSubscription = (bool) $res->fetch_assoc();
        $stmt->close();
    } catch (Exception $e) {
        error_log("Erro ao verificar assinatura: " . $e->getMessage());
        $hasSubscription = false;
    }

    if (!$hasSubscription) {
        if (function_exists('set_flash')) {
            set_flash('Atenção', 'É necessário ter uma assinatura ativa para acessar a home.');
        }
        header('Location: planos.php');
        exit;
    }
}

// Buscar filmes do banco de dados por categoria
function getMoviesByCategory($conn, $category) {
    try {
        $stmt = $conn->prepare("SELECT id, title, name, year, genre, synopsis, image, movie_link, trailer_link 
                               FROM movies 
                               WHERE category = ? 
                               ORDER BY created_at DESC");
        
        if (!$stmt) {
            throw new Exception("Erro ao preparar consulta: " . $conn->error);
        }
        
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $movies = [];
        while ($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }
        
        $stmt->close();
        return $movies;
        
    } catch (Exception $e) {
        error_log("Erro ao buscar filmes: " . $e->getMessage());
        return []; // Retorna array vazio em caso de erro
    }
}

// Buscar filmes por categoria
$novidades = getMoviesByCategory($conn, 'novidades');
$recomendacoes = getMoviesByCategory($conn, 'recomendacoes');
$lancamentos = getMoviesByCategory($conn, 'lancamentos');
$outros = getMoviesByCategory($conn, 'outros');

// Sua função getProfileImage() atual permanece aqui
function getProfileImage() {
    if (isset($_SESSION['perfil_ativo']['imagem'])) {
        return $_SESSION['perfil_ativo']['imagem'];
    }
    
    $profilesFile = 'data/profiles.json';
    if (file_exists($profilesFile)) {
        $profiles = json_decode(file_get_contents($profilesFile), true) ?: [];
        $perfilAtivoId = $_SESSION['perfil_ativo']['id'];
        
        foreach ($profiles as $profile) {
            if ($profile['id'] == $perfilAtivoId) {
                $_SESSION['perfil_ativo']['imagem'] = $profile['image'];
                return $profile['image'];
            }
        }
    }
    
    return 'uploads/default-avatar.jpg';
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cliq - Streaming de Filmes</title>
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <link rel="stylesheet" href="home.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body>
    <nav class="navbar">
        <img src="midias\LOGO CLIQ DEFINITIVA.png" class="logo" alt="Logo Cliq">
        
        <div class="nav-list">
            <a href="home.php">Home</a>
            <div class="search-wrap">
                <input type="text" id="searchInput" placeholder="Pesquisar" aria-label="Pesquisar">
                <ion-icon name="search" id="busca"></ion-icon>
            </div>
            <a href="kids.php">Infantil</a>
            
            <?php if (is_admin()): ?>
            <a href="CRUD.php" style="color: #ffeb3b; text-decoration: none; margin-left: 15px;">
                <i class="fa fa-cog"></i> Administração
            </a>
            <?php endif; ?>
            
            <!-- Versão SIMPLES da foto do perfil -->
            <div class="profile-simple">
                <a href="perfil.php" class="profile-link">
                    <img src="<?php echo getProfileImage(); ?>" 
                         alt="<?php echo htmlspecialchars($_SESSION['perfil_ativo']['nome']); ?>"
                         class="profile-avatar-small"
                         onerror="this.src='uploads/default-avatar.jpg'">
                </a>
            </div>
            
            <a href="logout.php">Sair</a>
        </div>
    </nav>

    <div class="carousel-container">
        <div class="carousel">
            <div class="carousel-item">
                <img src="https://ingresso-a.akamaihd.net/prd/img/movie/um-filme-minecraft/ca6ff5b1-6f3d-4c13-b451-ebe5a04a2b81.webp"
                    class="banner" alt="Minecraft">
            </div>
            <div class="carousel-item">
                <img src="https://ingresso-a.akamaihd.net/prd/img/movie/uma-batalha-apos-a-outra/ee5a48d2-80c6-43c4-97d6-e84b291b2129.webp"
                    class="banner" alt="Uma Batalha Após a Outra">
            </div>
            <div class="carousel-item">
                <img src="https://ingresso-a.akamaihd.net/prd/img/movie/os-caras-malvados-2/8894e786-d84b-4601-a5d1-32008a21e88d.webp"
                    class="banner" alt="Os Caras Malvados 2">
            </div>
        </div>
    </div>

    <div class="filtros">
        <div class="card" onclick="filterMovies('destaque')">
            <p>Em Destaque</p>
        </div>
        <div class="card" onclick="filterMovies('ficção')">
            <p>Ficção Científica</p>
        </div>
        <div class="card" onclick="filterMovies('terror')">
            <p>Terror</p>
        </div>
        <div class="card" onclick="filterMovies('ação')">
            <p>Ação</p>
        </div>
        <div class="card" onclick="filterMovies('drama')">
            <p>Drama</p>
        </div>
        <div class="card" onclick="filterMovies('comédia')">
            <p>Comédia</p>
        </div>
        <div class="card" onclick="filterMovies('animação')">
            <p>Animação</p>
        </div>
    </div>

    <!-- Seção Novidades -->
    <section class="recommendations">
        <br>
        <h2>Novidades</h2>
        <div class="movies-row-wrapper">
            <button class="scroll-btn scroll-left" onclick="scrollRow(this, 'left')">❮</button>
            <div class="movies-row" id="novidades-row">
                <?php if (!empty($novidades)): ?>
                    <?php foreach ($novidades as $movie): ?>
                        <div class="movie <?php echo strtolower($movie['genre']); ?>" 
                             data-title="<?php echo htmlspecialchars($movie['title']); ?>"
                             data-synopsis="<?php echo htmlspecialchars($movie['synopsis']); ?>"
                             data-year="<?php echo $movie['year']; ?>"
                             data-genre="<?php echo $movie['genre']; ?>"
                             data-movie-link="<?php echo htmlspecialchars($movie['movie_link'] ?? ''); ?>"
                             data-trailer-link="<?php echo htmlspecialchars($movie['trailer_link'] ?? ''); ?>">
                            <img src="<?php echo htmlspecialchars($movie['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($movie['title']); ?>"
                                 loading="lazy"
                                 onerror="this.src='uploads/default-movie.jpg'">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-message">
                        <p>Nenhum filme em novidades ainda.</p>
                    </div>
                <?php endif; ?>
            </div>
            <button class="scroll-btn scroll-right" onclick="scrollRow(this, 'right')">❯</button>
        </div>
    </section>

    <!-- Seção Recomendações -->
    <section class="recommendations">
        <br>
        <h2>Recomendações para você</h2>
        <div class="movies-row-wrapper">
            <button class="scroll-btn scroll-left" onclick="scrollRow(this, 'left')">❮</button>
            <div class="movies-row" id="recomendacoes-row">
                <?php if (!empty($recomendacoes)): ?>
                    <?php foreach ($recomendacoes as $movie): ?>
                        <div class="movie <?php echo strtolower($movie['genre']); ?>" 
                             data-title="<?php echo htmlspecialchars($movie['title']); ?>"
                             data-synopsis="<?php echo htmlspecialchars($movie['synopsis']); ?>"
                             data-year="<?php echo $movie['year']; ?>"
                             data-genre="<?php echo $movie['genre']; ?>"
                             data-movie-link="<?php echo htmlspecialchars($movie['movie_link'] ?? ''); ?>"
                             data-trailer-link="<?php echo htmlspecialchars($movie['trailer_link'] ?? ''); ?>">
                            <img src="<?php echo htmlspecialchars($movie['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($movie['title']); ?>"
                                 loading="lazy"
                                 onerror="this.src='uploads/default-movie.jpg'">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-message">
                        <p>Nenhuma recomendação disponível.</p>
                    </div>
                <?php endif; ?>
            </div>
            <button class="scroll-btn scroll-right" onclick="scrollRow(this, 'right')">❯</button>
        </div>
    </section>

    <!-- Seção Lançamentos -->
    <section class="top-movies">
        <br>
        <h2>Lançamentos</h2>
        <div class="movies-row-wrapper">
            <button class="scroll-btn scroll-left" onclick="scrollRow(this, 'left')">❮</button>
            <div class="movies-row" id="lancamentos-row">
                <?php if (!empty($lancamentos)): ?>
                    <?php foreach ($lancamentos as $movie): ?>
                        <div class="movie <?php echo strtolower($movie['genre']); ?>" 
                             data-title="<?php echo htmlspecialchars($movie['title']); ?>"
                             data-synopsis="<?php echo htmlspecialchars($movie['synopsis']); ?>"
                             data-year="<?php echo $movie['year']; ?>"
                             data-genre="<?php echo $movie['genre']; ?>"
                             data-movie-link="<?php echo htmlspecialchars($movie['movie_link'] ?? ''); ?>"
                             data-trailer-link="<?php echo htmlspecialchars($movie['trailer_link'] ?? ''); ?>">
                            <img src="<?php echo htmlspecialchars($movie['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($movie['title']); ?>"
                                 loading="lazy"
                                 onerror="this.src='uploads/default-movie.jpg'">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-message">
                        <p>Nenhum lançamento disponível.</p>
                    </div>
                <?php endif; ?>
            </div>
            <button class="scroll-btn scroll-right" onclick="scrollRow(this, 'right')">❯</button>
        </div>
    </section>

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
                    <li><a href="sobre.php">Sobre Nós</a></li>
                </ul>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <p>© 2025 CLIQ Streaming. Todos os direitos reservados.</p>
        </div>
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
    
</body>
<script src="home.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</html>