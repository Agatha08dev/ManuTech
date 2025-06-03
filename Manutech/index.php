<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<title>ManuTech - Página Inicial</title>
</head>
<body>
<h1>Bem-vindo ao ManuTech</h1>

<?php if (!isset($_SESSION['usuario_id'])): ?>
    <p><a href="login.php">Login</a></p>
<?php else: ?>
    <p><a href="meuschamados.php">Meus chamados</a></p>
    <p><a href="abrichamados.php">Abrir novo chamado</a></p>
    <p><a href="logout.php">Logout</a></p>
<?php endif; ?>

</body>
</html>
