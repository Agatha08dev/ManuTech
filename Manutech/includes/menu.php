<?php
// includes/menu.php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_nome = $_SESSION['usuario_nome'] ?? 'Usuário';
$usuario_perfil = $_SESSION['usuario_perfil'] ?? '';

// Define os links visíveis para cada perfil
$links = [
    'tecnico' => [
        ['url' => 'chamados.php', 'label' => 'Chamados Pendentes'],
        ['url' => 'meuschamados.php', 'label' => 'Meus Chamados'],
        ['url' => 'agenda/index.php', 'label' => 'Agenda'],
        ['url' => 'logout.php', 'label' => 'Sair'],
    ],
    'gestor' => [
        ['url' => 'chamados.php', 'label' => 'Todos os Chamados'],
        ['url' => 'relatorios/index.php', 'label' => 'Relatórios'],
        ['url' => 'agenda/index.php', 'label' => 'Agenda'],
        ['url' => 'logout.php', 'label' => 'Sair'],
    ],
    'admin' => [
        ['url' => 'admin-dashboard.php', 'label' => 'Dashboard'],
        ['url' => 'chamados.php', 'label' => 'Chamados'],
        ['url' => 'relatorios/index.php', 'label' => 'Relatórios'],
        ['url' => 'agenda/index.php', 'label' => 'Agenda'],
        ['url' => 'usuarios.php', 'label' => 'Usuários'],
        ['url' => 'logout.php', 'label' => 'Sair'],
    ]
];

$menu_links = $links[$usuario_perfil] ?? [];

?>

<style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}
.menu-lateral {
    position: fixed;
    top: 0;
    left: 0;
    width: 220px;
    height: 100vh;
    background: #2c3e50;
    color: #ecf0f1;
    padding-top: 20px;
    box-sizing: border-box;
}
.menu-lateral h3 {
    text-align: center;
    margin-bottom: 30px;
}
.menu-lateral ul {
    list-style: none;
    padding-left: 0;
}
.menu-lateral ul li {
    padding: 12px 20px;
}
.menu-lateral ul li a {
    color: #ecf0f1;
    text-decoration: none;
    display: block;
}
.menu-lateral ul li a:hover {
    background: #34495e;
}
.conteudo {
    margin-left: 220px;
    padding: 20px;
}
</style>

<div class="menu-lateral">
    <h3>Olá, <?= htmlspecialchars($usuario_nome) ?></h3>
    <ul>
        <?php foreach ($menu_links as $link): ?>
            <li><a href="<?= $link['url'] ?>"><?= htmlspecialchars($link['label']) ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>

<div class="conteudo">
<!-- Conteúdo da página vai aqui -->
