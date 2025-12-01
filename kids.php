<?php
require_once 'session.php';
require_once 'flash.php';
require 'conexao.php';

// Verificar autenticação
if (empty($_SESSION['usuario'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/kids.php';
    header('Location: login.php');
    exit;
}

if (empty($_SESSION['perfil_ativo'])) {
    header('Location: perfil.php');
    exit;
}

// Buscar filmes do banco de dados por categoria (apenas Kids)
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
        return [];
    }
}

// Buscar filmes kids por categoria
$animacao = getMoviesByCategory($conn, 'animacao_kids');
$aventura = getMoviesByCategory($conn, 'aventura_kids');
$musical = getMoviesByCategory($conn, 'musical_kids');
$educativo = getMoviesByCategory($conn, 'educativo_kids');

// Função getProfileImage
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
    <title>Cliq Kids - Streaming Infantil</title>
    
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
            <a href="CRUDusers.php" style="color: #ffeb3b; text-decoration: none; margin-left: 15px;">
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
                <img src="https://image.tmdb.org/t/p/original/w9kR8qbmQ01HwnvK4alvnQ2ca0L.jpg"
                    class="banner" alt="Toy Story 4">
            </div>
            <div class="carousel-item">
                <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEi3AMTMVSHVG7Wi0TRaNApnjYZz75PYclyrz_83KWkKLAq_O6wEi_a4Mhx5PS1ewo-riMk1ucdegMCmrRSWvJ-zrRgN7CI68pky7atVIXRO7Q9CM0dQ9Avr7xm_iFutu-Jgi2RkT0ud2EXaKCLup7qos0wX-ay4ANicCh44ZYK_OfCa4sy11lirmhuN/s475/nemo-globo-2022-banner.jpeg"
                    class="banner" alt="Procurando Nemo">
            </div>
            <div class="carousel-item">
                <img src="https://image.tmdb.org/t/p/original/xJWPZIYOEFIjZpBL7SVBGnzRYXp.jpg"
                    class="banner" alt="Frozen 2">
            </div>
            <div class="carousel-item">
                <img src="https://i.pinimg.com/736x/3c/dc/09/3cdc09081f208b52d245455a6e345d4c.jpg"
                    class="banner" alt="Moana">
            </div>
            <div class="carousel-item">
                <img src="https://uploads.jovemnerd.com.br/wp-content/uploads/2021/11/encanto-capa-2.jpg?ims=1210x544/filters:quality(75)"
                    class="banner" alt="Encanto">
            </div>
        </div>
    </div>

    <!-- Seção Animação Infantil -->
    <section class="recommendations">
        <br>
        <h2>Animação Infantil</h2>
        <div class="movies-row-wrapper">
            <button class="scroll-btn scroll-left" onclick="scrollRow(this, 'left')">❮</button>
            <div class="movies-row" id="animacao-row">
                <?php if (!empty($animacao)): ?>
                    <?php foreach ($animacao as $movie): ?>
                        <div class="movie kids animação" 
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
                        <p>Nenhum filme de animação disponível.</p>
                        <p><a href="CRUD.php" style="color: #8e24aa;">Adicione filmes kids no CRUD</a></p>
                    </div>
                <?php endif; ?>
            </div>
            <button class="scroll-btn scroll-right" onclick="scrollRow(this, 'right')">❯</button>
        </div>
    </section>

    <!-- Seção Aventura Infantil -->
    <section class="recommendations">
        <br>
        <h2>Aventura Infantil</h2>
        <div class="movies-row-wrapper">
            <button class="scroll-btn scroll-left" onclick="scrollRow(this, 'left')">❮</button>
            <div class="movies-row" id="aventura-row">
                <?php if (!empty($aventura)): ?>
                    <?php foreach ($aventura as $movie): ?>
                        <div class="movie kids aventura" 
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
                        <p>Nenhum filme de aventura disponível.</p>
                        <p><a href="CRUD.php" style="color: #8e24aa;">Adicione filmes kids no CRUD</a></p>
                    </div>
                <?php endif; ?>
            </div>
            <button class="scroll-btn scroll-right" onclick="scrollRow(this, 'right')">❯</button>
        </div>
    </section>

    <!-- Seção Musical Infantil -->
    <section class="recommendations">
        <br>
        <h2>Musicais Infantis</h2>
        <div class="movies-row-wrapper">
            <button class="scroll-btn scroll-left" onclick="scrollRow(this, 'left')">❮</button>
            <div class="movies-row" id="musical-row">
                <?php if (!empty($musical)): ?>
                    <?php foreach ($musical as $movie): ?>
                        <div class="movie kids musical" 
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
                        <p>Nenhum musical infantil disponível.</p>
                        <p><a href="CRUD.php" style="color: #8e24aa;">Adicione filmes kids no CRUD</a></p>
                    </div>
                <?php endif; ?>
            </div>
            <button class="scroll-btn scroll-right" onclick="scrollRow(this, 'right')">❯</button>
        </div>
    </section>

    <!-- Footer -->
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="kids.js"></script>
</body>
</html>