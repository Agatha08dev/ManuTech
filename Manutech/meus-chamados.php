<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php?erro=Você precisa estar logado');
    exit;
}

require_once 'includes/db.php';

$usuario_id = $_SESSION['usuario_id'];

// Buscar chamados do usuário
$sql = "SELECT * FROM chamados WHERE usuario_id = :usuario_id ORDER BY data_abertura DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['usuario_id' => $usuario_id]);
$chamados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="UTF-8" /><title>Meus Chamados</title></head>
<body>
<h2>Meus Chamados</h2>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Status</th>
            <th>Urgência</th>
            <th>Setor</th>
            <th>Data</th>
            <th>Imagem</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($chamados as $c): ?>
        <tr>
            <td><?= $c['id'] ?></td>
            <td><?= htmlspecialchars($c['titulo']) ?></td>
            <td><?= $c['status'] ?></td>
            <td><?= $c['urgencia'] ?></td>
            <td><?= htmlspecialchars($c['setor']) ?></td>
            <td><?= $c['data_abertura'] ?></td>
            <td>
                <?php if ($c['imagem']): ?>
                <img src="<?= htmlspecialchars($c['imagem']) ?>" alt="Imagem" width="50" />
                <?php else: ?>
                -
                <?php endif; ?>
            </td>
            <td>
                <a href="detalhechamado.php?id=<?= $c['id'] ?>">Ver</a>
                <!-- link para editar/excluir, se quiser -->
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
