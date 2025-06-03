<?php
require_once 'includes/db.php'; // Conexão PDO

$nome = "João Silva";
$email = "joao@email.com";
$senha = "senha123";
$perfil = "colaborador";

// Criar hash da senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (nome, email, senha_hash, perfil) VALUES (:nome, :email, :senha_hash, :perfil)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':nome' => $nome,
    ':email' => $email,
    ':senha_hash' => $senha_hash,
    ':perfil' => $perfil
]);

echo "Usuário criado!";
?>
