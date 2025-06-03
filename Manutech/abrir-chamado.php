<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php?erro=Você precisa estar logado');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="UTF-8" /><title>Abrir Chamado</title></head>
<body>
<h2>Abrir Novo Chamado</h2>
<form action="abrichamados.php" method="POST" enctype="multipart/form-data">
    <label>Título:</label><br />
    <input type="text" name="titulo" required /><br /><br />
    
    <label>Descrição:</label><br />
    <textarea name="descricao" required></textarea><br /><br />
    
    <label>Setor:</label><br />
    <select name="setor" required>
        <option value="">Selecione</option>
        <option value="TI">TI</option>
        <option value="Infraestrutura">Infraestrutura</option>
        <option value="RH">RH</option>
        <!-- mais setores -->
    </select><br /><br />
    
    <label>Urgência:</label><br />
    <select name="urgencia" required>
        <option value="leve">Leve</option>
        <option value="moderada">Moderada</option>
        <option value="critica">Crítica</option>
    </select><br /><br />
    
    <label>Imagem (opcional):</label><br />
    <input type="file" name="imagem" accept="image/*" /><br /><br />
    
    <button type="submit">Enviar</button>
</form>

<?php
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $setor = $_POST['setor'] ?? '';
    $urgencia = $_POST['urgencia'] ?? 'leve';
    $usuario_id = $_SESSION['usuario_id'];
    $imagem_path = null;

    // Upload simples de imagem
    if (!empty($_FILES['imagem']['name'])) {
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','gif'];
        if (in_array(strtolower($ext), $allowed)) {
            $novo_nome = uniqid() . '.' . $ext;
            $destino = 'uploads/' . $novo_nome;
            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                $imagem_path = $destino;
            }
        }
    }

    // Inserir no banco
    $sql = "INSERT INTO chamados (usuario_id, titulo, descricao, setor, urgencia, status, imagem)
            VALUES (:usuario_id, :titulo, :descricao, :setor, :urgencia, 'aberto', :imagem)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':usuario_id' => $usuario_id,
        ':titulo' => $titulo,
        ':descricao' => $descricao,
        ':setor' => $setor,
        ':urgencia' => $urgencia,
        ':imagem' => $imagem_path
    ]);

    echo "<p style='color:green'>Chamado aberto com sucesso!</p>";
}
?>
</body>
</html>
