document.addEventListener("DOMContentLoaded", () => {
  const movieGrid = document.getElementById("movieGrid");
  const movieForm = document.getElementById("movieForm");
  const movieModal = document.getElementById("movieModal");
  const addBtn = document.getElementById("addBtn");
  const cancelBtn = document.getElementById("cancelBtn");

  const filterBtn = document.getElementById("filterBtn");
  const filterCard = document.getElementById("filterCard");

  const searchInput = document.getElementById("searchInput");
  const genreFilter = document.getElementById("genreFilter");
  const yearFilter = document.getElementById("yearFilter");

  let movies = [];
  let chart;

  // Mostrar filtro lateral
  filterBtn.addEventListener("click", () => {
    // ensure base class remains
    if (!filterCard.classList.contains('filter-Card')) {
      filterCard.classList.add('filter-Card');
    }

    // toggle between visible (flex) and hidden (none)
    const curr = getComputedStyle(filterCard).display;
    if (curr === 'none') {
      filterCard.style.display = 'flex';
      filterCard.classList.remove('hidden');
    } else {
      filterCard.style.display = 'none';
      filterCard.classList.add('hidden');
    }
  });

  // Abrir modal
  addBtn.addEventListener("click", () => {
    movieModal.classList.remove("hidden");
    movieForm.reset();
    movieForm.dataset.editIndex = "";
  });

  // Fechar modal
  cancelBtn.addEventListener("click", () => {
    movieModal.classList.add("hidden");
  });

  // Carregar filmes
  async function loadMovies() {
    const res = await fetch("server.php?action=list");
    movies = await res.json();
    renderMovies(movies);
    updateChart();
  }

  // Aplicar filtro
  function applyFilters() {
    const search = searchInput.value.toLowerCase().trim();
    const genre = genreFilter.value;
    const year = yearFilter.value.trim();

    return movies.filter(m => {
      const matchSearch =
        m.title.toLowerCase().includes(search) ||
        m.genre.toLowerCase().includes(search);

      const matchGenre = genre === "all" || m.genre === genre;
      const matchYear = year === "" || m.year.toString() === year;

      return matchSearch && matchGenre && matchYear;
    });
  }

  // Renderizar
  function renderMovies(list) {
    movieGrid.innerHTML = "";

    if (list.length === 0) {
      movieGrid.innerHTML = "<p class='empty'>Nenhum filme encontrado.</p>";
      return;
    }

    list.forEach((movie) => {
      const card = document.createElement("div");
      card.className = "card";

      card.innerHTML = `
        <img src="${movie.image}">
        <h3>${movie.title}</h3>
        <p><strong>${movie.year}</strong> - ${movie.genre}</p>
        <p>${movie.synopsis}</p>
        <div class="buttons">
            <button class="edit" data-id="${movie.id}">Editar</button>
            <button class="delete" data-id="${movie.id}">Excluir</button>
        </div>
      `;

      movieGrid.appendChild(card);
    });

    // attach handlers using the movie id -> resolve current index in movies[]
    document.querySelectorAll(".edit").forEach(btn => {
      btn.addEventListener("click", () => {
        const id = btn.dataset.id;
        const idx = movies.findIndex(m => String(m.id) === String(id));
        if (idx !== -1) editMovie(idx);
      });
    });

    document.querySelectorAll(".delete").forEach(btn => {
      btn.addEventListener("click", () => {
        const id = btn.dataset.id;
        const idx = movies.findIndex(m => String(m.id) === String(id));
        if (idx !== -1) deleteMovie(idx);
      });
    });
  }

  // Gráfico
  function updateChart() {
    const ctx = document.getElementById("chartCanvas").getContext("2d");

    const genres = {};
    movies.forEach(m => genres[m.genre] = (genres[m.genre] || 0) + 1);

    if (chart) chart.destroy();

    chart = new Chart(ctx, {
      type: "pie",
      data: {
        labels: Object.keys(genres),
        datasets: [{
          data: Object.values(genres),
          backgroundColor: [
            "rgba(255, 0, 200, 0.7)",
            "rgba(255, 251, 3, 0.7)",
            "rgba(75, 192, 192, 0.7)",
            "rgba(180, 46, 126, 0.7)",
            "rgba(108, 0, 255, 0.7)"
          ],
          borderColor: "#fff",
          borderWidth: 2
        }]
      },
      options: {
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              color: '#ffffff',
              font: { size: 12 }
            }
          },
          tooltip: {
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            backgroundColor: 'rgba(0,0,0,0.8)'
          },
          
          datalabels: {
            color: '#ffffff',    
            formatter: (value, ctx) => `${value}`, 
            font: { weight: '600' }
          }
        }
      }
    });
  }

// Excluir filme - CORRIGIDO
async function deleteMovie(index) {
    const movie = movies[index];
    
    if (!movie || !confirm(`Excluir o filme "${movie.title}"?`)) return;

    try {
        const fd = new FormData();
        fd.append("index", movie.id); // Enviar o ID do banco, não o índice do array

        const response = await fetch("server.php?action=delete", {
            method: "POST",
            body: fd
        });

        const result = await response.json();
        
        if (result.success) {
            loadMovies();
            alert(result.message || 'Filme excluído com sucesso!');
        } else {
            alert('Erro: ' + (result.error || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('Erro ao excluir filme: ' + error.message);
    }
}

// Editar filme
function editMovie(index) {
    const m = movies[index];

    movieModal.classList.remove("hidden");
    movieForm.dataset.editIndex = m.id;

    movieForm.title.value = m.title;
    movieForm.name.value = m.name;
    movieForm.year.value = m.year;
    movieForm.genre.value = m.genre;
    movieForm.category.value = m.category || 'novidades';
    movieForm.synopsis.value = m.synopsis;
    movieForm.movie_link.value = m.movie_link || '';
    movieForm.trailer_link.value = m.trailer_link || '';
}

// Salvar filme - CORRIGIDO
movieForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    try {
        const fd = new FormData(movieForm);
        const editing = movieForm.dataset.editIndex !== "";

        if (editing) {
            fd.append("index", movieForm.dataset.editIndex); // Já é o ID do banco
        }

        const action = editing ? "update" : "add";

        const response = await fetch(`server.php?action=${action}`, {
            method: "POST",
            body: fd
        });

        const result = await response.json();
        
        if (result.success) {
            movieModal.classList.add("hidden");
            loadMovies();
            alert(result.message || 'Operação realizada com sucesso!');
        } else {
            alert('Erro: ' + (result.error || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('Erro ao salvar filme: ' + error.message);
    }
});

  movieGrid.addEventListener('click', (e) => {
    const btn = e.target.closest('button.edit, button.delete');
    if (!btn) return;
    const id = btn.dataset.id; // read the stable DB id we set in renderMovies
    const idx = movies.findIndex(m => String(m.id) === String(id));
    if (idx === -1) return;
    if (btn.classList.contains('edit')) {
      editMovie(idx);
    } else {
      deleteMovie(idx);
    }
  });

// Carregar filmes
async function loadMovies() {
    try {
        const res = await fetch("server.php?action=list");
        const data = await res.json();
        
        if (Array.isArray(data)) {
            movies = data;
            renderMovies(movies);
            updateChart();
        } else {
            console.error('Resposta inválida:', data);
            movies = [];
            renderMovies(movies);
        }
    } catch (error) {
        console.error('Erro ao carregar filmes:', error);
        movies = [];
        renderMovies(movies);
    }
}

  // Eventos de filtro
  searchInput.addEventListener("input", () => renderMovies(applyFilters()));
  genreFilter.addEventListener("change", () => renderMovies(applyFilters()));
  yearFilter.addEventListener("input", () => renderMovies(applyFilters()));

  loadMovies();
});
