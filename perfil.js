let currentProfileId = 0
let isEditMode = false

// Abrir modal para adicionar perfil
function openAddModal() {
  isEditMode = false
  currentProfileId = 0

  document.getElementById("modalTitle").textContent = "Adicionar Perfil"
  document.getElementById("profileId").value = ""
  document.getElementById("profileName").value = ""
  document.getElementById("imagePreview").src = "uploads/default-avatar.jpg"
  document.getElementById("profileImage").value = ""
  document.getElementById("deleteBtn").style.display = "none"

  showModal()
}

// Abrir modal para editar perfil
function openEditModal(id, name, image) {
  isEditMode = true
  currentProfileId = id

  document.getElementById("modalTitle").textContent = "Editar Perfil"
  document.getElementById("profileId").value = id
  document.getElementById("profileName").value = name
  document.getElementById("imagePreview").src = image
  document.getElementById("profileImage").value = ""
  document.getElementById("deleteBtn").style.display = "inline-block"

  showModal()
}

// Mostrar modal
function showModal() {
  document.getElementById("profileModal").style.display = "block"
  document.getElementById("formMessage").style.display = "none"
  document.body.style.overflow = "hidden"
}

// Fechar modal
function closeModal() {
  document.getElementById("profileModal").style.display = "none"
  document.body.style.overflow = "auto"
}

// Fechar modal ao clicar fora
window.onclick = (event) => {
  const modal = document.getElementById("profileModal")
  if (event.target === modal) {
    closeModal()
  }
}

// Preview da imagem
function previewImage(input) {
  if (input.files && input.files[0]) {
    const file = input.files[0]

    // Validar tamanho (5MB)
    if (file.size > 5 * 1024 * 1024) {
      showMessage("Arquivo muito grande. Máximo: 5MB", "error")
      input.value = ""
      return
    }

    // Validar tipo
    const allowedTypes = ["image/jpeg", "image/jpg", "image/png", "image/gif"]
    if (!allowedTypes.includes(file.type)) {
      showMessage("Tipo de arquivo não permitido. Use JPG, PNG ou GIF", "error")
      input.value = ""
      return
    }

    const reader = new FileReader()
    reader.onload = (e) => {
      document.getElementById("imagePreview").src = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

// Mostrar mensagem
function showMessage(message, type) {
  const messageDiv = document.getElementById("formMessage")
  messageDiv.textContent = message
  messageDiv.className = "form-message " + type
  messageDiv.style.display = "block"

  setTimeout(() => {
    messageDiv.style.display = "none"
  }, 5000)
}

// Mostrar loading
function showLoading() {
  document.getElementById("loadingOverlay").style.display = "flex"
}

// Esconder loading
function hideLoading() {
  document.getElementById("loadingOverlay").style.display = "none"
}

// Salvar perfil
document.getElementById("profileForm").addEventListener("submit", async (e) => {
  e.preventDefault()

  const profileName = document.getElementById("profileName").value.trim()
  const profileImage = document.getElementById("profileImage").files[0]
  const profileId = document.getElementById("profileId").value

  if (!profileName) {
    showMessage("Por favor, digite um nome para o perfil", "error")
    return
  }

  showLoading()

  try {
    let imagePath = ""

    // Upload da imagem se houver
    if (profileImage) {
      const formData = new FormData()
      formData.append("profileImage", profileImage)

      const uploadResponse = await fetch("upload.php", {
        method: "POST",
        body: formData,
      })

      const uploadResult = await uploadResponse.json()

      if (!uploadResult.success) {
        throw new Error(uploadResult.message)
      }

      imagePath = uploadResult.filePath
    }

    // Salvar perfil
    const saveFormData = new FormData()
    saveFormData.append("profileId", profileId)
    saveFormData.append("profileName", profileName)
    saveFormData.append("imagePath", imagePath)

    const saveResponse = await fetch("save-profile.php", {
      method: "POST",
      body: saveFormData,
    })

    const saveResult = await saveResponse.json()

    if (saveResult.success) {
      showMessage(saveResult.message, "success")
      setTimeout(() => {
        location.reload()
      }, 1000)
    } else {
      throw new Error(saveResult.message)
    }
  } catch (error) {
    hideLoading()
    showMessage(error.message || "Erro ao salvar perfil", "error")
  }
})

// Deletar perfil
async function deleteProfile() {
  if (!confirm("Tem certeza que deseja excluir este perfil?")) {
    return
  }

  const profileId = document.getElementById("profileId").value

  if (!profileId) {
    showMessage("Erro ao identificar o perfil", "error")
    return
  }

  showLoading()

  try {
    const formData = new FormData()
    formData.append("profileId", profileId)

    const response = await fetch("delete-profile.php", {
      method: "POST",
      body: formData,
    })

    const result = await response.json()

    if (result.success) {
      showMessage(result.message, "success")
      setTimeout(() => {
        location.reload()
      }, 1000)
    } else {
      throw new Error(result.message)
    }
  } catch (error) {
    hideLoading()
    showMessage(error.message || "Erro ao excluir perfil", "error")
  }
}

// Fechar modal com tecla ESC
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") {
    closeModal()
  }
})

// Selecionar perfil para assistir
function selectProfile(profileId, profileName) {
    showLoading();
    
    fetch('set-profile.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            profile_id: profileId,
            profile_name: profileName
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            // Redirecionar para home
            window.location.href = 'home.php';
        } else {
            showMessage('Erro ao selecionar perfil', 'error');
        }
    })
    .catch(error => {
        hideLoading();
        showMessage('Erro de conexão', 'error');
    });
}

// Atualizar o HTML dos perfis para serem clicáveis
// Atualizar o HTML dos perfis para serem clicáveis (VERSÃO CORRIGIDA)
function makeProfilesClickable() {
    document.querySelectorAll('.profile:not(.add-profile)').forEach(profile => {
        // Remove qualquer evento anterior para evitar duplicação
        profile.replaceWith(profile.cloneNode(true));
    });

    // Re-aplica os eventos
    document.querySelectorAll('.profile:not(.add-profile)').forEach(profile => {
        const profileElement = profile;
        
        // Só adiciona clique se não for o botão de editar
        profileElement.onclick = function(e) {
            // Se o clique foi no botão de editar, não faz nada (deixa o botão handle)
            if (e.target.closest('.edit-btn')) {
                return;
            }
            
            // Se foi no perfil (fora do botão editar), seleciona o perfil
            const profileId = this.getAttribute('data-id');
            const profileName = this.querySelector('p').textContent;
            selectProfile(profileId, profileName);
        };
    });
}

// Chamar quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    makeProfilesClickable();
}); 