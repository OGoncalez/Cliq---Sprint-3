document.addEventListener("DOMContentLoaded", () => {
  let currentSlide = 0;
  const slides = document.querySelectorAll(".carousel-item");
  const carousel = document.querySelector(".carousel");

// Abre o player
function openMoviePlayer(title, videoUrl, type) {
    console.log('Tentando abrir player:', { title, videoUrl, type });
    
    if (!videoUrl || videoUrl.trim() === '') {
        Swal.fire({
            title: 'Conteúdo Indisponível',
            text: `O ${type} para "${title}" não está disponível no momento.`,
            icon: 'warning',
            confirmButtonText: 'Entendi',
            confirmButtonColor: '#8e24aa',
            background: '#1a0a1a',
            color: '#eb6ad1'
        });
        return;
    }

    // Função para extrair ID do YouTube
    function getYouTubeId(url) {
        const patterns = [
            /youtube\.com\/watch\?v=([^&]+)/,
            /youtu\.be\/([^?]+)/,
            /youtube\.com\/embed\/([^?]+)/,
            /youtube\.com\/v\/([^?]+)/
        ];
        
        for (const pattern of patterns) {
            const match = url.match(pattern);
            if (match) return match[1];
        }
        return null;
    }

    // Processar URL para garantir compatibilidade
    let finalUrl = videoUrl;
    
    // Se for YouTube, converter para embed
    if (videoUrl.includes('youtube.com') || videoUrl.includes('youtu.be')) {
        const videoId = getYouTubeId(videoUrl);
        if (videoId) {
            finalUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0`;
        }
    }
    
    // Se for Vimeo, converter para embed
    if (videoUrl.includes('vimeo.com')) {
        const vimeoMatch = videoUrl.match(/vimeo\.com\/(\d+)/);
        if (vimeoMatch) {
            finalUrl = `https://player.vimeo.com/video/${vimeoMatch[1]}?autoplay=1`;
        }
    }

    // Validar URL
    try {
        new URL(finalUrl);
    } catch (e) {
        Swal.fire({
            title: 'URL Inválida',
            text: `O link do ${type} para "${title}" não é válido.`,
            icon: 'error',
            confirmButtonText: 'Entendi',
            confirmButtonColor: '#8e24aa',
            background: '#1a0a1a',
            color: '#eb6ad1'
        });
        return;
    }

    // CORREÇÃO: Remover espaços extras e criar título limpo
    const cleanTitle = title.trim().replace(/\s+/g, ' ');
    const playerTitle = `${cleanTitle} - ${type === 'movie' ? 'Filme' : 'Trailer'}`;

    // CORREÇÃO: Usar URL direta sem encodeURIComponent no título
    window.location.href = `player.php?video=${encodeURIComponent(finalUrl)}&title=${encodeURIComponent(playerTitle)}&type=${type}`;
}

  // Função principal para exibir opções com SweetAlert
  function showMovieOptions(movie) {
      const title = movie.dataset.title;
      const synopsis = movie.dataset.synopsis;
      const year = movie.dataset.year;
      const genre = movie.dataset.genre;
      const movieLink = movie.dataset.movieLink || '';
      const trailerLink = movie.dataset.trailerLink || '';
      
      console.log('Dados do filme:', { 
          title, 
          movieLink, 
          trailerLink,
          hasMovieLink: !!movieLink && movieLink.trim() !== '',
          hasTrailerLink: !!trailerLink && trailerLink.trim() !== ''
      });
      
      // Verificar se os links existem e não estão vazios
      const hasMovie = movieLink && movieLink.trim() !== '';
      const hasTrailer = trailerLink && trailerLink.trim() !== '';
      
      Swal.fire({
          title: title,
          html: `
              <div style="text-align: left; max-height: 200px; overflow-y: auto;">
                  <p><strong>Ano:</strong> ${year}</p>
                  <p><strong>Gênero:</strong> ${genre}</p>
                  <p><strong>Sinopse:</strong> ${synopsis}</p>
          `,
          icon: 'info',
          showCancelButton: true,
          confirmButtonText: hasMovie ? 'Assistir Filme' : 'Filme Indisponível',
          cancelButtonText: hasTrailer ? 'Ver Trailer' : ' Trailer Indisponível',
          confirmButtonColor: hasMovie ? '#8e24aa' : '#6c757d',
          cancelButtonColor: hasTrailer ? '#ff5722' : '#6c757d',
          background: '#1a0a1a',
          color: '#eb6ad1',
          showDenyButton: true,
          denyButtonText: 'Fechar',
          denyButtonColor: '#6c757d',
          width: '600px'
      }).then((result) => {
          console.log('Resultado do SweetAlert:', result);
          
          if (result.isConfirmed && hasMovie) {
              // Assistir filme completo
              console.log('Abrindo filme:', movieLink);
              openMoviePlayer(title, movieLink, 'movie');
          } else if (result.dismiss === Swal.DismissReason.cancel && hasTrailer) {
              // Ver trailer
              console.log('Abrindo trailer:', trailerLink);
              openMoviePlayer(title, trailerLink, 'trailer');
          } else if (result.isConfirmed && !hasMovie) {
              Swal.fire({
                  title: 'Filme Indisponível',
                  text: 'O link do filme completo não está disponível.',
                  icon: 'warning',
                  confirmButtonColor: '#8e24aa',
                  background: '#1a0a1a',
                  color: '#eb6ad1'
              });
          } else if (result.dismiss === Swal.DismissReason.cancel && !hasTrailer) {
              Swal.fire({
                  title: 'Trailer Indisponível',
                  text: 'O link do trailer não está disponível.',
                  icon: 'warning',
                  confirmButtonColor: '#8e24aa',
                  background: '#1a0a1a',
                  color: '#eb6ad1'
              });
          }
      });
  }

  // Configurar carrossel se existir
  if (carousel && slides.length > 0) {
      slides.forEach((slide, index) => {
          slide.style.position = "absolute";
          slide.style.top = "0";
          slide.style.left = "0";
          slide.style.width = "100%";
          slide.style.transition = "opacity 0.5s ease-in-out, transform 0.5s ease-in-out";
          slide.style.opacity = "0";
          slide.style.transform = "translateX(0)";
      });

      function animateCarousel(index) {
          slides.forEach((slide, i) => {
              if (i === index) {
                  slide.style.opacity = "1";
                  slide.style.transform = "translateX(0)";
              } else if (i < index) {
                  slide.style.opacity = "0.3";
                  slide.style.transform = "translateX(-100%)";
              } else {
                  slide.style.opacity = "0.3";
                  slide.style.transform = "translateX(100%)";
              }
          });
          currentSlide = index;
      }

      // Auto-slide every 5 seconds
      setInterval(() => {
          const nextSlide = (currentSlide + 1) % slides.length;
          animateCarousel(nextSlide);
      }, 5000);

      animateCarousel(currentSlide);
  }

  // FUNÇÃO PARA FILTRAR FILMES POR CATEGORIA
  window.filterMovies = (type) => {
      const movies = document.querySelectorAll(".movie");

      movies.forEach((movie) => {
          movie.classList.remove("hidden");

          if (type !== "destaque" && !movie.classList.contains(type)) {
              movie.classList.add("hidden");
          }
      });

      const firstRow = document.querySelector(".movies-row");
      if (firstRow) {
          firstRow.scrollLeft = 0;
      }
  };

  // FUNÇÃO PARA ROLAR A LINHA DE FILMES
  window.scrollRow = (button, direction) => {
      const row = button.parentElement.querySelector(".movies-row");

      if (!row) return;

      const scrollAmount = 300;

      row.scrollBy({
          left: direction === "left" ? -scrollAmount : scrollAmount,
          behavior: "smooth",
      });
  };

  // Barra de pesquisa
  const searchInput = document.getElementById("searchInput");
  if (searchInput) {
      searchInput.addEventListener("input", () => {
          const query = searchInput.value.toLowerCase();
          const movies = document.querySelectorAll(".movie");

          movies.forEach((movie) => {
              const title = movie.querySelector("img").alt.toLowerCase();
              if (query === "" || title.includes(query)) {
                  movie.classList.remove("hidden");
              } else {
                  movie.classList.add("hidden");
              }
          });
      });
  }

  // Eventos de clique nos filmes
  document.querySelectorAll(".movie").forEach((movie) => {
      if (!movie.closest(".scroll-btn")) {
          movie.addEventListener("click", () => showMovieOptions(movie));
          // Adicionar suporte a teclado para acessibilidade
          movie.addEventListener("keydown", (e) => {
              if (e.key === 'Enter' || e.key === ' ') {
                  e.preventDefault();
                  showMovieOptions(movie);
              }
          });
      }
  });
});

// Funções do Chatbot
function openChatbot() {
    document.getElementById('chatbotModal').style.display = 'block';
}

function closeChatbot() {
    document.getElementById('chatbotModal').style.display = 'none';
}

function sendMessage() {
    const input = document.getElementById('chatbotInput');
    const message = input.value.trim();
    
    if (message === '') return;
    
    // Adicionar mensagem do usuário
    const messagesContainer = document.getElementById('chatbotMessages');
    const userMessage = document.createElement('div');
    userMessage.className = 'chat-message user-message';
    userMessage.textContent = message;
    messagesContainer.appendChild(userMessage);
    
    // Limpar input
    input.value = '';
    
    // Scroll para baixo
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
    // Resposta automática do bot
    setTimeout(() => {
        const botMessage = document.createElement('div');
        botMessage.className = 'chat-message bot-message';
        
        // Respostas pré-definidas baseadas no input
        const responses = {
            '1': 'Para problemas de reprodução, verifique sua conexão com a internet e tente reiniciar o aplicativo. Se o problema persistir, entre em contato com nosso suporte técnico.',
            '2': 'Para dúvidas sobre conta, você pode redefinir sua senha na página de login ou verificar as configurações da sua conta no menu de perfil.',
            '3': 'Para questões sobre assinatura, acesse "Minha Assinatura" no seu perfil para ver detalhes do plano atual e opções de pagamento.',
            '4': 'Temos uma equipe dedicada a selecionar os melhores conteúdos! Você pode sugerir filmes através do formulário de contato em nosso site.',
            '5': 'Para falar com um atendente, envie um email para suporte@cliq.com ou ligue para (11) 9999-9999 no horário comercial.'
        };
        
        const response = responses[message] || 
            'Obrigado pela sua mensagem! Um de nossos atendentes entrará em contato em breve. Enquanto isso, você pode verificar nossa Central de Ajuda.';
        
        botMessage.textContent = response;
        messagesContainer.appendChild(botMessage);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }, 1000);
}

// Enviar mensagem com Enter
document.getElementById('chatbotInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendMessage();
    }
});

// Fechar chatbot clicando fora
window.addEventListener('click', function(e) {
    const modal = document.getElementById('chatbotModal');
    if (e.target === modal) {
        closeChatbot();
    }
});