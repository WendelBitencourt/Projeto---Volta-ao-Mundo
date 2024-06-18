<?php
session_start();
require_once '../config/Banco.php';
require_once '../classes/Autenticacao.php';

$banco = new Banco();
$db = $banco->conectar();

$autenticacao = new Autenticacao($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    if ($autenticacao->login($email, $senha)) {
        header('Location: admin.php');
    } else {
        $erro = 'Falha no login. Email ou senha inválidos.';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    if ($autenticacao->registrar($nome, $email, $senha)) {
        $mensagem = 'Usuário registrado com sucesso!';
    } else {
        $erroRegistro = 'Falha no registro.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .vh-100 {
            min-height: 100vh;
        }
    </style>
</head>
<body>
<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="w-100" style="max-width: 400px;">
        <h2 class="text-center">Login</h2>
        <?php if(isset($erro)): ?>
            <div class="alert alert-danger"><?php echo $erro; ?></div>
        <?php endif; ?>
        <?php if(isset($mensagem)): ?>
            <div class="alert alert-success"><?php echo $mensagem; ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Endereço de Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
        </form>
        <div class="text-center mt-3">
            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#registerModal">
                Criar Novo Usuário
            </button>
        </div>
    </div>
</div>

<!-- Modal de Registro -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">Registrar Novo Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if(isset($erroRegistro)): ?>
                    <div class="alert alert-danger"><?php echo $erroRegistro; ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Endereço de Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>
                    <button type="submit" name="register" class="btn btn-primary w-100">Registrar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


