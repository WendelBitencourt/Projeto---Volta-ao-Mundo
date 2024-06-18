<?php
// Inicia uma nova sessão ou resume a sessão existente
session_start();

// Inclui os arquivos necessários para a conexão com o banco de dados e a classe Autenticacao
require_once '../config/Banco.php';
require_once '../classes/Autenticacao.php';

// Cria uma nova instância da classe Banco e estabelece uma conexão com o banco de dados
$banco = new Banco();
$db = $banco->conectar();

// Cria uma nova instância da classe Autenticacao, passando a conexão com o banco de dados
$autenticacao = new Autenticacao($db);

// Verifica se o método de requisição é POST e se o botão de login foi pressionado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    // Recupera os dados de email e senha enviados pelo formulário de login
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Tenta autenticar o usuário com o email e senha fornecidos
    if ($autenticacao->login($email, $senha)) {
        // Se a autenticação for bem-sucedida, redireciona o usuário para a página de administração
        header('Location: admin.php');
    } else {
        // Se a autenticação falhar, define uma mensagem de erro
        $erro = 'Falha no login. Email ou senha inválidos.';
    }
}

// Verifica se o método de requisição é POST e se o botão de registro foi pressionado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    // Recupera os dados de nome, email e senha enviados pelo formulário de registro
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Tenta registrar o novo usuário com os dados fornecidos
    if ($autenticacao->registrar($nome, $email, $senha)) {
        // Se o registro for bem-sucedido, define uma mensagem de sucesso
        $mensagem = 'Usuário registrado com sucesso!';
    } else {
        // Se o registro falhar, define uma mensagem de erro
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
            <?php if (isset($erro)) : ?>
                <div class="alert alert-danger"><?php echo $erro; ?></div>
            <?php endif; ?>
            <?php if (isset($mensagem)) : ?>
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
                    <?php if (isset($erroRegistro)) : ?>
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