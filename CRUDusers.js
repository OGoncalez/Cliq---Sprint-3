// CRUDusers.js - VERSÃO DEFINITIVAMENTE CORRIGIDA
document.addEventListener('DOMContentLoaded', function() {
    // Modal elements
    const addBtn = document.getElementById('addBtn');
    const userModal = document.getElementById('userModal');
    const subscriptionModal = document.getElementById('subscriptionModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const cancelSubscriptionBtn = document.getElementById('cancelSubscriptionBtn');
    const userForm = document.getElementById('userForm');
    const subscriptionForm = document.getElementById('subscriptionForm');
    const modalTitle = document.getElementById('modalTitle');
    const subscriptionModalTitle = document.getElementById('subscriptionModalTitle');
    const formAction = document.getElementById('formAction');
    const subscriptionFormAction = document.getElementById('subscriptionFormAction');
    const userId = document.getElementById('userId');
    const subscriptionId = document.getElementById('subscriptionId');
    const usuarioId = document.getElementById('usuarioId');
    const passwordGroup = document.getElementById('passwordGroup');
    const subscriptionStatusGroup = document.getElementById('subscriptionStatusGroup');
    
    // Filter elements
    const filterBtn = document.getElementById('filterBtn');
    const filterCard = document.getElementById('filterCard');
    const sortFilter = document.getElementById('sortFilter');
    const searchInput = document.getElementById('searchInput');
    const usersGrid = document.getElementById('usersGrid');

    // Armazenar o HTML ORIGINAL de todos os user-cards
    let originalUserCardsHTML = [];
    
    function initOriginalUserCards() {
        if (!usersGrid) return;
        // Salvar o HTML interno ORIGINAL da grid
        originalUserCardsHTML = usersGrid.innerHTML;
    }

    // Inicializar quando o DOM estiver pronto
    initOriginalUserCards();

    // User modal functions
    addBtn.addEventListener('click', () => {
        modalTitle.textContent = 'Adicionar Usuário';
        formAction.value = 'create_user';
        userId.value = '';
        passwordGroup.style.display = 'block';
        const passwordInput = document.getElementById('password');
        if (passwordInput) {
          passwordInput.required = true;
          passwordInput.disabled = false;
          passwordInput.placeholder = 'Senha';
        }
        userForm.reset();
        userModal.classList.remove('hidden');
    });

    cancelBtn.addEventListener('click', () => {
        const passwordInput = document.getElementById('password');
        if (passwordInput) {
          passwordInput.required = true;
          passwordInput.disabled = false;
          passwordInput.placeholder = 'Senha';
        }
        userModal.classList.add('hidden');
        if (userForm) userForm.reset();
    });

    // Subscription modal functions
    cancelSubscriptionBtn.addEventListener('click', () => {
        subscriptionModal.classList.add('hidden');
    });

    // Filter functionality
    filterBtn.addEventListener('click', () => {
        filterCard.classList.toggle('active');
    });

    sortFilter.addEventListener('change', filterUsers);
    searchInput.addEventListener('input', filterUsers);

    // Close modals when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target === userModal) {
            userModal.classList.add('hidden');
        }
        if (e.target === subscriptionModal) {
            subscriptionModal.classList.add('hidden');
        }
    });

    // Initial filter
    filterUsers();
});

// Global functions for button clicks
function openEditModal(id, nome, email) {
    console.log('Abrindo edição para usuário:', id, nome, email);
    
    const modalTitle = document.getElementById('modalTitle');
    const formAction = document.getElementById('formAction');
    const userId = document.getElementById('userId');
    const nomeInput = document.getElementById('nome');
    const emailInput = document.getElementById('email');
    const passwordGroup = document.getElementById('passwordGroup');
    const userModal = document.getElementById('userModal');
    const passwordInput = document.getElementById('password');
    
    // Preenche os dados do formulário
    modalTitle.textContent = 'Editar Usuário';
    formAction.value = 'update_user';
    userId.value = id;
    nomeInput.value = nome;
    emailInput.value = email;

    // Mostrar o campo de senha como opcional ao editar
    passwordGroup.style.display = 'block';
    if (passwordInput) {
      passwordInput.required = false;
      passwordInput.disabled = false;
      passwordInput.value = '';
      passwordInput.placeholder = 'Deixe em branco para manter a senha atual';
      passwordInput.autocomplete = 'new-password';
    }
     
    // Mostra o modal
    userModal.classList.remove('hidden');
    
    console.log('Formulário preenchido:', {
        action: formAction.value,
        id: userId.value,
        nome: nomeInput.value,
        email: emailInput.value
    });
}

function openAddSubscriptionModal(userId, userName) {
    const subscriptionModalTitle = document.getElementById('subscriptionModalTitle');
    const subscriptionFormAction = document.getElementById('subscriptionFormAction');
    const subscriptionId = document.getElementById('subscriptionId');
    const usuarioId = document.getElementById('usuarioId');
    const subscriptionStatusGroup = document.getElementById('subscriptionStatusGroup');
    const subscriptionModal = document.getElementById('subscriptionModal');
    
    subscriptionModalTitle.textContent = 'Adicionar Assinatura';
    subscriptionFormAction.value = 'create_subscription';
    subscriptionId.value = '';
    usuarioId.value = userId;
    document.getElementById('userName').value = userName;
    subscriptionStatusGroup.style.display = 'none';
    subscriptionForm.reset();
    
    document.getElementById('plano').value = 'Padrão';
    subscriptionModal.classList.remove('hidden');
}

function openEditSubscriptionModal(subscriptionId, plano, status, userId, userName) {
    const subscriptionModalTitle = document.getElementById('subscriptionModalTitle');
    const subscriptionFormAction = document.getElementById('subscriptionFormAction');
    const subscriptionIdElem = document.getElementById('subscriptionId');
    const usuarioId = document.getElementById('usuarioId');
    const subscriptionStatusGroup = document.getElementById('subscriptionStatusGroup');
    const subscriptionModal = document.getElementById('subscriptionModal');
    
    subscriptionModalTitle.textContent = 'Editar Assinatura';
    subscriptionFormAction.value = 'update_subscription';
    subscriptionIdElem.value = subscriptionId;
    usuarioId.value = userId;
    document.getElementById('plano').value = plano;
    document.getElementById('status').value = status;
    document.getElementById('userName').value = userName;
    subscriptionStatusGroup.style.display = 'block';
    
    subscriptionModal.classList.remove('hidden');
}

// FUNÇÃO FILTERUSERS COMPLETAMENTE REFEITA
function filterUsers() {
    const searchInput = document.getElementById('searchInput');
    const sortFilter = document.getElementById('sortFilter');
    const usersGrid = document.getElementById('usersGrid');
    
    const searchTerm = searchInput.value.toLowerCase();
    const sortValue = sortFilter.value;
    
    // Se não há busca e não há filtro de assinatura, restaurar o HTML original
    if (!searchTerm && (sortValue === 'newest' || sortValue === 'oldest' || sortValue === 'name')) {
        usersGrid.innerHTML = window.originalUserCardsHTML || usersGrid.innerHTML;
        
        // Aplicar apenas ordenação se necessário
        if (sortValue !== 'newest') { // newest é o padrão original
            applySorting(usersGrid, sortValue);
        }
        return;
    }
    
    // Restaurar HTML original primeiro para ter todos os cards
    usersGrid.innerHTML = window.originalUserCardsHTML || usersGrid.innerHTML;
    
    // Agora filtrar e ordenar
    const userCards = Array.from(usersGrid.querySelectorAll('.user-card'));
    
    // Filtrar por termo de busca
    let filteredCards = userCards;
    if (searchTerm) {
        filteredCards = userCards.filter(card => {
            const userName = card.querySelector('h3').textContent.toLowerCase();
            const userEmail = card.querySelector('p:nth-child(2)').textContent.toLowerCase();
            return userName.includes(searchTerm) || userEmail.includes(searchTerm);
        });
    }
    
    // Filtrar por status de assinatura
    if (sortValue === 'subscription' || sortValue === 'no-subscription') {
        filteredCards = filteredCards.filter(card => {
            const hasSubscription = !card.querySelector('.no-subscription');
            return sortValue === 'subscription' ? hasSubscription : !hasSubscription;
        });
    }
    
    // Ordenar
    applySortingToCards(filteredCards, sortValue);
    
    // Atualizar a grid
    usersGrid.innerHTML = '';
    filteredCards.forEach(card => {
        usersGrid.appendChild(card);
    });
}

// Função para aplicar ordenação diretamente na grid
function applySorting(usersGrid, sortValue) {
    const userCards = Array.from(usersGrid.querySelectorAll('.user-card'));
    applySortingToCards(userCards, sortValue);
    
    usersGrid.innerHTML = '';
    userCards.forEach(card => {
        usersGrid.appendChild(card);
    });
}

// Função para ordenar array de cards
function applySortingToCards(cards, sortValue) {
    cards.sort((a, b) => {
        const aName = a.querySelector('h3').textContent;
        const bName = b.querySelector('h3').textContent;
        const aDateText = a.querySelector('p:nth-child(3)').textContent.replace('Criado em: ', '');
        const bDateText = b.querySelector('p:nth-child(3)').textContent.replace('Criado em: ', '');
        
        function parseDate(dateStr) {
            try {
                const [datePart, timePart] = dateStr.split(' ');
                const [day, month, year] = datePart.split('/');
                return new Date(`${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}T${timePart}:00`);
            } catch (e) {
                return new Date();
            }
        }
        
        const aDate = parseDate(aDateText);
        const bDate = parseDate(bDateText);
        
        switch(sortValue) {
            case 'name':
                return aName.localeCompare(bName, 'pt-BR');
            case 'newest':
                return bDate - aDate;
            case 'oldest':
                return aDate - bDate;
            default:
                return 0;
        }
    });
}

// Inicializar quando a página carregar
window.addEventListener('load', function() {
    const usersGrid = document.getElementById('usersGrid');
    if (usersGrid) {
        window.originalUserCardsHTML = usersGrid.innerHTML;
    }
});

// Recarregar após ações que modificam a lista
document.addEventListener('userListUpdated', function() {
    // Aguardar um pouco para o PHP processar e recarregar a página
    setTimeout(() => {
        location.reload();
    }, 1000);
});