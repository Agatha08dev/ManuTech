<?php
session_start();
require_once 'includes/db.php'; // Agora pode usar $pdo
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ManuTech - Página Inicial</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 40px auto;
            padding: 0 20px;
            background-color: #f9f9f9;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #0073e6;
        }
        nav p {
            margin: 10px 0;
        }
        a {
            text-decoration: none;
            color: #0073e6;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Bem-vindo ao ManuTech</h1>
    <nav>
        <?php if (empty($_SESSION['usuario_id'])): ?>
            <p><a href="login.php" aria-label="Ir para página de login">Login</a></p>
        <?php else: ?>
            <p><a href="meuschamados.php" aria-label="Ver meus chamados">Meus chamados</a></p>
            <p><a href="abrichamados.php" aria-label="Abrir novo chamado">Abrir novo chamado</a></p>
            <p><a href="logout.php" aria-label="Fazer logout">Logout</a></p>
        <?php endif; ?>
    </nav>
</body>
</html>
