<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php?erro=Você precisa estar logado');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$perfil = $_SESSION['usuario_perfil'];

// Só técnico e gestor acessam essa página
if (!in_array($perfil, ['tecnico', 'gestor'])) {
    die("Acesso negado.");
}

// Captura filtros do GET
$status = $_GET['status'] ?? '';
$setor = $_GET['setor'] ?? '';
$urgencia = $_GET['urgencia'] ?? '';
$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';

// Construir WHERE dinâmico
$where = [];
$params = [];

if ($status) {
    $where[] = "status = :status";
    $params['status'] = $status;
}

if ($setor) {
    $where[] = "setor = :setor";
    $params['setor'] = $setor;
}

if ($urgencia) {
    $where[] = "urgencia = :urgencia";
    $params['urgencia'] = $urgencia;
}

if ($data_inicio) {
    $where[] = "criado_em >= :data_inicio";
    $params['data_inicio'] = $data_inicio . ' 00:00:00';
}

if ($data_fim) {
    $where[] = "criado_em <= :data_fim";
    $params['data_fim'] = $data_fim . ' 23:59:59';
}

$where_sql = '';
if (count($where) > 0) {
    $where_sql = 'WHERE ' . implode(' AND ', $where);
}

// Buscar chamados
$sql = "SELECT c.id, c.titulo, c.status, c.setor, c.urgencia, c.criado_em, u.nome AS solicitante 
        FROM chamados c
        JOIN usuarios u ON c.usuario_id = u.id
        $where_sql
        ORDER BY c.criado_em DESC
        LIMIT 100";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$chamados = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<title>Listagem de Chamados</title>
<style>
    table { border-collapse: collapse; width: 100%; }
    th, td { padding: 8px; border: 1px solid #ccc; }
    th { background: #eee; }
    form.filtros input, form.filtros select { margin-right: 10px; }
</style>
</head>
<body>
<h2>Chamados</h2>

<form method="GET" class="filtros">
    <label>Status:
        <select name="status">
            <option value="">-- Todos --</option>
            <option value="aberto" <?= $status == 'aberto' ? 'selected' : '' ?>>Aberto</option>
            <option value="em andamento" <?= $status == 'em andamento' ? 'selected' : '' ?>>Em andamento</option>
            <option value="resolvido" <?= $status == 'resolvido' ? 'selected' : '' ?>>Resolvido</option>
            <option value="fechado" <?= $status == 'fechado' ? 'selected' : '' ?>>Fechado</option>
        </select>
    </label>

    <label>Setor:
        <input type="text" name="setor" value="<?= htmlspecialchars($setor) ?>" placeholder="Ex: TI" />
    </label>

    <label>Urgência:
        <select name="urgencia">
            <option value="">-- Todas --</option>
            <option value="crítica" <?= $urgencia == 'crítica' ? 'selected' : '' ?>>Crítica</option>
            <option value="moderada" <?= $urgencia == 'moderada' ? 'selected' : '' ?>>Moderada</option>
            <option value="leve" <?= $urgencia == 'leve' ? 'selected' : '' ?>>Leve</option>
        </select>
    </label>

    <label>De:
        <input type="date" name="data_inicio" value="<?= htmlspecialchars($data_inicio) ?>" />
    </label>

    <label>Até:
        <input type="date" name="data_fim" value="<?= htmlspecialchars($data_fim) ?>" />
    </label>

    <button type="submit">Filtrar</button>
    <a href="chamados.php">Limpar filtros</a>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Status</th>
            <th>Setor</th>
            <th>Urgência</th>
            <th>Data</th>
            <th>Solicitante</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($chamados) === 0): ?>
            <tr><td colspan="8">Nenhum chamado encontrado.</td></tr>
        <?php else: ?>
            <?php foreach ($chamados as $c): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><?= htmlspecialchars($c['titulo']) ?></td>
                    <td><?= htmlspecialchars($c['status']) ?></td>
                    <td><?= htmlspecialchars($c['setor']) ?></td>
                    <td><?= htmlspecialchars($c['urgencia']) ?></td>
                    <td><?= $c['criado_em'] ?></td>
                    <td><?= htmlspecialchars($c['solicitante']) ?></td>
                    <td><a href="detalhechamado.php?id=<?= $c['id'] ?>">Detalhes</a></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<p><a href="admin-dashboard.php">Voltar ao Painel</a></p>

</body>
</html>
