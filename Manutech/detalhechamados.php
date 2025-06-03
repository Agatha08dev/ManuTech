<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php?erro=Você precisa estar logado');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$perfil = $_SESSION['usuario_perfil']; // colaborador, tecnico, gestor

// Só técnico e gestor podem alterar status e comentar
$permiteAlterar = in_array($perfil, ['tecnico', 'gestor']);

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Chamado não informado.");
}

// Buscar chamado
$sql = "SELECT c.*, u.nome AS nome_usuario FROM chamados c
        JOIN usuarios u ON c.usuario_id = u.id
        WHERE c.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$chamado = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$chamado) {
    die("Chamado não encontrado.");
}

// Buscar comentários
$sql = "SELECT cm.*, u.nome FROM comentarios cm
        JOIN usuarios u ON cm.usuario_id = u.id
        WHERE cm.chamado_id = :id ORDER BY cm.criado_em ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Processar envio de comentário / atualização de status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $permiteAlterar) {
    $novo_status = $_POST['status'] ?? $chamado['status'];
    $comentario = trim($_POST['comentario'] ?? '');

    // Atualizar status se diferente
    if ($novo_status !== $chamado['status']) {
        $sql = "UPDATE chamados SET status = :status WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['status' => $novo_status, 'id' => $id]);
    }

    // Inserir comentário se houver
    if ($comentario !== '') {
        $sql = "INSERT INTO comentarios (chamado_id, usuario_id, comentario) VALUES (:chamado_id, :usuario_id, :comentario)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'chamado_id' => $id,
            'usuario_id' => $usuario_id,
            'comentario' => $comentario
        ]);
    }

    // Recarregar para mostrar atualizado
    header("Location: detalhechamado.php?id=$id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<title>Detalhe Chamado #<?= $chamado['id'] ?></title>
</head>
<body>
<h2>Chamado #<?= $chamado['id'] ?> - <?= htmlspecialchars($chamado['titulo']) ?></h2>

<p><strong>Aberto por:</strong> <?= htmlspecialchars($chamado['nome_usuario']) ?></p>
<p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($chamado['descricao'])) ?></p>
<p><strong>Setor:</strong> <?= htmlspecialchars($chamado['setor']) ?></p>
<p><strong>Urgência:</strong> <?= htmlspecialchars($chamado['urgencia']) ?></p>
<p><strong>Status:</strong> <?= htmlspecialchars($chamado['status']) ?></p>

<?php if ($chamado['imagem']): ?>
    <p><strong>Imagem anexada:</strong><br />
    <img src="<?= htmlspecialchars($chamado['imagem']) ?>" alt="Imagem Chamado" style="max-width:300px;" /></p>
<?php endif; ?>

<hr>

<h3>Comentários</h3>
<?php if (count($comentarios) === 0): ?>
    <p>Nenhum comentário ainda.</p>
<?php else: ?>
    <ul>
    <?php foreach ($comentarios as $com): ?>
        <li>
            <strong><?= htmlspecialchars($com['nome']) ?></strong> em <?= $com['criado_em'] ?><br />
            <?= nl2br(htmlspecialchars($com['comentario'])) ?>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if ($permiteAlterar): ?>
<hr>
<h3>Atualizar Status e Comentar</h3>
<form method="POST" action="">
    <label for="status">Status:</label>
    <select name="status" id="status">
        <?php
        $opcoes = ['aberto', 'em andamento', 'resolvido', 'fechado'];
        foreach ($opcoes as $opt) {
            $sel = ($opt === $chamado['status']) ? 'selected' : '';
            echo "<option value=\"$opt\" $sel>" . ucfirst($opt) . "</option>";
        }
        ?>
    </select><br><br>

    <label for="comentario">Comentário:</label><br />
    <textarea name="comentario" id="comentario" rows="4" cols="50" placeholder="Deixe uma observação..."></textarea><br><br>

    <button type="submit">Salvar</button>
</form>
<?php endif; ?>

<p><a href="meuschamados.php">Voltar</a></p>
</body>
</html>

<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$perfil = $_SESSION['usuario_perfil'];

if (!in_array($perfil, ['tecnico', 'gestor'])) {
    die("Acesso negado.");
}

$chamado_id = $_GET['id'] ?? null;
if (!$chamado_id) {
    die("Chamado não especificado.");
}

// Pegar dados do chamado
$stmt = $pdo->prepare("SELECT c.*, u.nome AS solicitante_nome FROM chamados c JOIN usuarios u ON c.usuario_id = u.id WHERE c.id = ?");
$stmt->execute([$chamado_id]);
$chamado = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$chamado) {
    die("Chamado não encontrado.");
}

// Atualizar status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'atualizar_status') {
        $novo_status = $_POST['status'] ?? $chamado['status'];
        $atualizou = false;
        if (in_array($novo_status, ['aberto', 'em andamento', 'resolvido', 'fechado']) && $novo_status !== $chamado['status']) {
            $upd = $pdo->prepare("UPDATE chamados SET status = ? WHERE id = ?");
            $upd->execute([$novo_status, $chamado_id]);
            $atualizou = true;
        }
        if ($atualizou) {
            header("Location: detalhechamado.php?id=$chamado_id&msg=Status atualizado");
            exit;
        }
    } elseif ($_POST['action'] === 'adicionar_comentario') {
        $comentario = trim($_POST['comentario'] ?? '');
        if ($comentario !== '') {
            $ins = $pdo->prepare("INSERT INTO comentarios (chamado_id, usuario_id, comentario, criado_em) VALUES (?, ?, ?, NOW())");
            $ins->execute([$chamado_id, $usuario_id, $comentario]);
            header("Location: detalhechamado.php?id=$chamado_id&msg=Comentário adicionado");
            exit;
        }
    }
}

// Buscar comentários do chamado
$coments_stmt = $pdo->prepare("SELECT co.*, u.nome FROM comentarios co JOIN usuarios u ON co.usuario_id = u.id WHERE co.chamado_id = ? ORDER BY co.criado_em ASC");
$coments_stmt->execute([$chamado_id]);
$comentarios = $coments_stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/menu.php';

?>

<h2>Detalhe do Chamado #<?= $chamado['id'] ?></h2>

<?php if (isset($_GET['msg'])): ?>
    <p style="color: green;"><?= htmlspecialchars($_GET['msg']) ?></p>
<?php endif; ?>

<table border="1" cellpadding="6" cellspacing="0" style="border-collapse: collapse;">
    <tr><th>Título</th><td><?= htmlspecialchars($chamado['titulo']) ?></td></tr>
    <tr><th>Solicitante</th><td><?= htmlspecialchars($chamado['solicitante_nome']) ?></td></tr>
    <tr><th>Setor</th><td><?= htmlspecialchars($chamado['setor']) ?></td></tr>
    <tr><th>Urgência</th><td><?= htmlspecialchars($chamado['urgencia']) ?></td></tr>
    <tr><th>Status</th><td><?= htmlspecialchars($chamado['status']) ?></td></tr>
    <tr><th>Descrição</th><td><?= nl2br(htmlspecialchars($chamado['descricao'])) ?></td></tr>
    <tr><th>Criado em</th><td><?= $chamado['criado_em'] ?></td></tr>
</table>

<h3>Atualizar Status</h3>
<form method="POST">
    <input type="hidden" name="action" value="atualizar_status" />
    <select name="status" required>
        <?php
        $status_options = ['aberto', 'em andamento', 'resolvido', 'fechado'];
        foreach ($status_options as $st) {
            $selected = ($st === $chamado['status']) ? 'selected' : '';
            echo "<option value=\"$st\" $selected>" . ucfirst($st) . "</option>";
        }
        ?>
    </select>
    <button type="submit">Atualizar</button>
</form>

<h3>Comentários</h3>
<?php if (count($comentarios) === 0): ?>
    <p>Sem comentários ainda.</p>
<?php else: ?>
    <ul style="list-style:none; padding-left: 0;">
    <?php foreach ($comentarios as $cm): ?>
        <li style="border:1px solid #ccc; margin-bottom: 8px; padding: 6px;">
            <strong><?= htmlspecialchars($cm['nome']) ?></strong> 
            <em>(<?= $cm['criado_em'] ?>)</em><br />
            <?= nl2br(htmlspecialchars($cm['comentario'])) ?>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<h3>Adicionar Comentário</h3>
<form method="POST">
    <input type="hidden" name="action" value="adicionar_comentario" />
    <textarea name="comentario" rows="4" cols="60" required></textarea><br />
    <button type="submit">Enviar comentário</button>
</form>

<p><a href="chamados.php">Voltar à lista de chamados</a></p>

</div> <!-- fecha div.conteudo do menu.php -->
</body>
</html>
