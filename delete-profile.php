<?php
header('Content-Type: application/json');

$dataFile = 'data/profiles.json';

// Carregar perfis existentes
$profiles = [];
if (file_exists($dataFile)) {
    $profilesJson = file_get_contents($dataFile);
    $profiles = json_decode($profilesJson, true);
    if (!$profiles) {
        $profiles = [];
    }
}

// Obter ID do perfil
$profileId = isset($_POST['profileId']) ? intval($_POST['profileId']) : 0;

if ($profileId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

// Encontrar e remover perfil
$found = false;
$newProfiles = [];

foreach ($profiles as $profile) {
    if ($profile['id'] == $profileId) {
        $found = true;
        // Deletar imagem se não for a padrão
        if (isset($profile['image']) && 
            $profile['image'] !== 'uploads/default-avatar.jpg' && 
            file_exists($profile['image'])) {
            unlink($profile['image']);
        }
    } else {
        $newProfiles[] = $profile;
    }
}

if (!$found) {
    echo json_encode(['success' => false, 'message' => 'Perfil não encontrado']);
    exit;
}

// Salvar perfis atualizados
if (file_put_contents($dataFile, json_encode($newProfiles, JSON_PRETTY_PRINT))) {
    echo json_encode(['success' => true, 'message' => 'Perfil excluído com sucesso', 'profiles' => $newProfiles]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao excluir perfil']);
}
?>
