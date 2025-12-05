<?php
require_once 'session.php';
require_once 'flash.php';
require 'conexao.php'; // Sua conexão com MySQL

// Verificar autenticação e se é admin
if (empty($_SESSION['usuario'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/CRUD.php';
    header('Location: login.php');
    exit;
}

if (!function_exists('is_admin')) {
    require_once 'session.php'; // Use require_once
}

// Buscar filmes do banco de dados (MySQLi)
function getMoviesFromDatabase($conn) {
    try {
        $stmt = $conn->prepare("SELECT id, title, name, year, genre, synopsis, image FROM movies ORDER BY created_at DESC");
        
        if (!$stmt) {
            throw new Exception("Erro ao preparar consulta: " . $conn->error);
        }
        
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

// Buscar filmes
$movies = getMoviesFromDatabase($conn);

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
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <title>Catálogo de Filmes</title>
  <link rel="stylesheet" href="CRUD.css" />
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="navbar">
  <img src="midias\LOGO CLIQ DEFINITIVA.png" alt="" class="logo">
    <li><a href="CRUDusers.php">Gerenciar usuários</a></li>
    <li><a href="CRUD.php">Gerenciar filmes</a></li>
  <div class="search-box">
    <input type="text" id="searchInput" placeholder="Pesquisar filme ou gênero...">
  </div>

  <button id="filterBtn">Filtrar</button>
</div>

<div id="filterCard" class="filter-card">

    <label>Gênero:</label>
    <select id="genreFilter">
      <option value="all">Todos</option>
      <<option value="Ação">Ação</option>
  <option value="Comédia">Comédia</option>
  <option value="Drama">Drama</option>
  <option value="Terror">Terror</option>
  <option value="Romance">Romance</option>
  <option value="Animação">Animação</option>
  <option value="Aventura">Aventura</option>
  <option value="Musical">Musical</option>
  <option value="Ficção Científica">Ficção Científica</option>
  <option value="Suspense">Suspense</option>
    </select>

    <label>Ano:</label>
    <input type="number" id="yearFilter" placeholder="Ex: 2025" />

</div>

<main class="main">
  <aside class="chart">
    <h2>Gráfico</h2>
    <canvas id="chartCanvas" width="300" height="250"></canvas>
  </aside>

  <section class="movies">
    <h2>Filmes</h2>
    <div id="movieGrid" class="grid"></div>
  </section>
</main>

<button id="addBtn" class="add-btn"><span class="plus">+</span></button>

<div id="movieModal" class="modal hidden">
  <form id="movieForm" enctype="multipart/form-data">
    <h3>Adicionar Filme</h3>
    <input type="text" name="title" placeholder="Título" required />
    <input type="text" name="name" placeholder="Nome Original" required />
    <input type="number" name="year" placeholder="Ano" required />
    
    <select name="genre" required>
  <option value="Ação">Ação</option>
  <option value="Comédia">Comédia</option>
  <option value="Drama">Drama</option>
  <option value="Terror">Terror</option>
  <option value="Romance">Romance</option>
  <option value="Animação">Animação</option>
  <option value="Aventura">Aventura</option>
  <option value="Musical">Musical</option>
  <option value="Ficção Científica">Ficção Científica</option>
  <option value="Suspense">Suspense</option>
</select>


   <select name="category" required>
  <option value="novidades">Novidades</option>
  <option value="recomendacoes">Recomendações</option>
  <option value="lancamentos">Lançamentos</option>
  <option value="animacao_kids">Animação Infantil</option>
  <option value="aventura_kids">Aventura Infantil</option>
  <option value="musical_kids">Musical Infantil</option>
</select>


    <textarea name="synopsis" placeholder="Sinopse" required></textarea>

    <label>Link do Filme (URL):</label>
    <input type="url" name="movie_link" placeholder="https://exemplo.com/filme" />

    <label>Link do Trailer (URL):</label>
    <input type="url" name="trailer_link" placeholder="https://youtube.com/trailer" />

    <label>Imagem:</label>
    <input type="file" name="image" accept="image/*" />

    <div class="actions">
      <button type="submit">Salvar</button>
      <button type="button" id="cancelBtn">Cancelar</button>
    </div>
  </form>
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
        <!-- Seção Empresa -->
        <div class="footer-section">
            <h3>Empresa</h3>
            <ul class="footer-links">
                <li><a href="sobre.php">Sobre Nós</a></li>
                <li><a href="CRUDusers.php">Gerenciar usuários</a></li>
                <li><a href="CRUD.php">Gerenciar filmes</a></li>
            </ul>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <p>© 2025 CLIQ Streaming. Todos os direitos reservados.</p>
</footer>
<script src="CRUD.js"></script>
</body>
</html>