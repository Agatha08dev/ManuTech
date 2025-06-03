<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    
    if (!$email || !$senha) {
        header('Location: login.php?erro=Preencha todos os campos');
        exit;
    }

    $sql = "SELECT id, nome, senha_hash, perfil FROM usuarios WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
        // Login válido, salvar dados na sessão
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_perfil'] = $usuario['perfil'];

        // Redirecionar para o dashboard ou página inicial
        header('Location: admin-dashboard.php');
        exit;
    } else {
        header('Location: login.php?erro=Usuário ou senha inválidos');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Login - MANUTECH</title>
</head>
<body>
    <h2>Login</h2>
    <form method="POST" action="login.php">
        <label>Email:</label><br />
        <input type="email" name="email" required /><br /><br />
        
        <label>Senha:</label><br />
        <input type="password" name="senha" required /><br /><br />
        
        <button type="submit">Entrar</button>
    </form>
    <?php
    if (!empty($_GET['erro'])) {
        echo "<p style='color:red'>" . htmlspecialchars($_GET['erro']) . "</p>";
    }
    ?>
</body>
</html>
<?php
session_start();      // inicia a sessão
session_destroy();    // destrói todas as variáveis da sessão (logout)
header('Location: login.php');  // redireciona para página de login
exit;
?>
