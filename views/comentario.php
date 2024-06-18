<?php
// Inicia uma nova sessão ou resume a sessão existente
session_start();

// Inclui os arquivos necessários para a conexão com o banco de dados e a classe Comentario
require_once '../config/Banco.php';
require_once '../classes/Comentario.php';

// Cria uma nova instância da classe Banco e estabelece uma conexão com o banco de dados
$banco = new Banco();
$db = $banco->conectar();

// Cria uma nova instância da classe Comentario, passando a conexão com o banco de dados
$comentario = new Comentario($db);

// Verifica se o método de requisição é POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recupera os dados enviados pelo formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $conteudo = $_POST['conteudo'];

    // Tenta adicionar um novo comentário com os dados fornecidos
    if ($comentario->adicionarComentario($nome, $email, $conteudo)) {
        // Se o comentário for adicionado com sucesso, define uma mensagem de sucesso
        $mensagem = 'Comentário enviado com sucesso.';
    } else {
        // Se houver falha ao adicionar o comentário, define uma mensagem de erro
        $erro = 'Falha ao enviar comentário.';
    }
}

// Busca os comentários aprovados no banco de dados
$comentariosAprovados = $comentario->obterComentariosAprovados();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>China Number 1</title>
</head>

<body class="testadas">
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" aria-current="page" href="../index.html"><img id="logo" src="../imagens/logo.png" alt="logo"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav ">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="../politica-economia.html">Política e Econômia</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="../ciencia.html">Ciência e Tecnologia</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="../cultura.html">Cultura Chinesa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="../turismo.html">Turismo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="../views/comentario.php">Contato</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="../views/admin.php">Painel Administrativo</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container bg-white mt-3 mb-3 p-3">
        <h2>Enviar um Comentário</h2>
        <?php if (isset($mensagem)) : ?>
            <div class="alert alert-success"><?php echo $mensagem; ?></div>
        <?php elseif (isset($erro)) : ?>
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
                <label for="conteudo" class="form-label">Comentário</label>
                <textarea class="form-control" id="conteudo" name="conteudo" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>

        <h2 class="mt-5">Comentários Aprovados</h2>
        <?php if (!empty($comentariosAprovados)) : ?>
            <ul class="list-group mt-3">
                <?php foreach ($comentariosAprovados as $comentario) : ?>
                    <li class="list-group-item">
                        <h5><?php echo htmlspecialchars($comentario['nome']); ?></h5>
                        <p><?php echo nl2br(htmlspecialchars($comentario['conteudo'])); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p class="mt-3">Nenhum comentário aprovado ainda.</p>
        <?php endif; ?>
    </div>

    <footer class="bg-dark text-center text-white pt-4 pb-2">
        <div class="container">
            <div class="row">
                <div class="col-12 mb-2">
                    <a href="https://twitter.com" class="text-white me-2">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://facebook.com" class="text-white me-2">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://instagram.com" class="text-white me-2">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://linkedin.com" class="text-white">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
                <div class="col-12">
                    <p>Desenvolvido por <strong>Wendel Adriano Bitencourt</strong></p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/scrollreveal"></script>
    <script>
        ScrollReveal().reveal(".reveal", {
            duration: 3000,
            distance: "40px",
            origin: "bottom",
            interval: 400,
            reset: true,
        });
    </script>
</body>

</html>