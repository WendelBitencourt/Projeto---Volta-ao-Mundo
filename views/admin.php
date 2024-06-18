<?php
session_start();
require_once '../config/Banco.php';
require_once '../classes/Autenticacao.php';
require_once '../classes/Comentario.php';
require_once '../classes/LogAcao.php';

// Verificação de autenticação
$autenticacao = new Autenticacao((new Banco())->conectar());
if (!$autenticacao->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$banco = new Banco();
$db = $banco->conectar();

$comentario = new Comentario($db);
$logAcao = new LogAcao($db);
$usuario_nome = $_SESSION['usuario_nome'];

if (isset($_POST['acao']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    if ($_POST['acao'] == 'aprovar') {
        $comentario->atualizarStatusComentario($id, 'aprovado');
        $logAcao->logarAcao('aprovado', $id, $usuario_nome);
    } elseif ($_POST['acao'] == 'reprovar') {
        $comentario->atualizarStatusComentario($id, 'reprovado');
        $logAcao->logarAcao('reprovado', $id, $usuario_nome);
    }
}

if (isset($_POST['gerar_json'])) {
    $comentario->gerarJsonComentarios(); // Gerar o JSON
    $jsonPath = '../comentarios.json';
    if (file_exists($jsonPath)) {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="comentarios.json"');
        readfile($jsonPath);
        exit;
    } else {
        echo 'Erro: Arquivo comentarios.json não encontrado.';
    }
}

$comentarios = $comentario->obterComentarios()->fetchAll(PDO::FETCH_ASSOC) ?? [];

if (isset($_POST['logout'])) {
    $autenticacao->logout();
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!-- Navegação -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Painel Administrativo</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="admin.php">Admin</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logs.php">Logs</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo $_SESSION['usuario_nome']; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <form method="post" class="d-inline">
                                <button type="submit" name="logout" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
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
        <?php if (empty($comentarios)): ?>
            <tr>
                <td colspan="6" class="text-center">Nenhum comentário encontrado.</td>
            </tr>
        <?php else: ?>
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
        <?php endif; ?>
        </tbody>
    </table>
    <form method="post" class="mt-3">
        <button type="submit" name="gerar_json" class="btn btn-secondary">Gerar e Baixar Comentários</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

