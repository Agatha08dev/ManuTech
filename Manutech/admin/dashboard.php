<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php?erro=Você precisa estar logado para acessar esta página');
    exit;
}

// Acesso liberado, você pode usar:
// $_SESSION['usuario_nome'], $_SESSION['usuario_perfil'] para mostrar dados
?>
