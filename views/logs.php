<?php
session_start();
require_once '../config/Banco.php';
require_once '../classes/Autenticacao.php';
require_once '../classes/LogAcao.php';

// Verificação de autenticação
$autenticacao = new Autenticacao((new Banco())->conectar());
if (!$autenticacao->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$banco = new Banco();
$db = $banco->conectar();

$logAcao = new LogAcao($db);

$logs = $logAcao->obterLogs()->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Logs de Ações</title>
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
    <h2>Logs de Ações</h2>
    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Ação</th>
            <th>ID do Comentário</th>
            <th>Nome do Usuário</th>
            <th>Data da Ação</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($logs as $log): ?>
            <tr>
                <td><?php echo $log['id']; ?></td>
                <td><?php echo $log['acao']; ?></td>
                <td><?php echo $log['comentario_id']; ?></td>
                <td><?php echo $log['usuario_nome']; ?></td>
                <td><?php echo $log['data_acao']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

