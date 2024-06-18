<?php
session_start();
require_once 'config/Banco.php';
require_once 'classes/Comentario.php';
require_once 'classes/LogAcao.php';

$banco = new Banco();
$db = $banco->conectar();

$comentario = new Comentario($db);
$logAcao = new LogAcao($db);

if (isset($_POST['acao']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    if ($_POST['acao'] == 'aprovar') {
        $comentario->atualizarStatusComentario($id, 'aprovado');
        $logAcao->logarAcao('aprovado', $id);
    } elseif ($_POST['acao'] == 'reprovar') {
        $comentario->atualizarStatusComentario($id, 'reprovado');
        $logAcao->logarAcao('reprovado', $id);
    }
}

if (isset($_POST['importar'])) {
    $json = file_get_contents('comentarios.json');
    $comentarios = json_decode($json, true);
    $comentario->importarComentarios($comentarios);
}

$comentarios = $comentario->obterComentarios()->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h2>Painel Administrativo</h2>
    <form method="post">
        <button type="submit" name="importar" class="btn btn-secondary">Importar Comentários</button>
    </form>
    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Conteúdo</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($comentarios as $comentario): ?>
            <tr>
                <td><?php echo $comentario['id']; ?></td>
                <td><?php echo $comentario['nome']; ?></td>
                <td><?php echo $comentario['email']; ?></td>
                <td><?php echo $comentario['conteudo']; ?></td>
                <td><?php echo $comentario['status']; ?></td>
                <td>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $comentario['id']; ?>">
                        <button type="submit" name="acao" value="aprovar" class="btn btn-success">Aprovar</button>
                        <button type="submit" name="acao" value="reprovar" class="btn btn-danger">Reprovar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
