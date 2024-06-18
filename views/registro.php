<?php
// Inclui os arquivos necessários para a conexão com o banco de dados e a classe Autenticacao
require_once '../config/Banco.php';
require_once '../classes/Autenticacao.php';

// Cria uma nova instância da classe Banco e estabelece uma conexão com o banco de dados
$banco = new Banco();
$db = $banco->conectar();

// Cria uma nova instância da classe Autenticacao, passando a conexão com o banco de dados
$autenticacao = new Autenticacao($db);

// Verifica se o método de requisição é POST, indicando que o formulário de registro foi submetido
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recupera os dados de nome, email e senha enviados pelo formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Tenta registrar o novo usuário com os dados fornecidos
    if ($autenticacao->registrar($nome, $email, $senha)) {
        // Se o registro for bem-sucedido, redireciona o usuário para a página de login
        header('Location: login.php');
    } else {
        // Se o registro falhar, define uma mensagem de erro
        $erro = 'Falha no registro.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h2>Registro</h2>
        <?php if (isset($erro)) : ?>
            <div class="alert alert-danger"><?php echo $erro; ?></div>
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
            <button type="submit" class="btn btn-primary">Registrar</button>
        </form>
    </div>
</body>

</html>