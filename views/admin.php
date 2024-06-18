<?php
session_start(); // Inicia uma nova sessão ou retoma uma sessão existente
require_once '../config/Banco.php'; // Inclui o arquivo de configuração do banco de dados
require_once '../classes/Autenticacao.php'; // Inclui a classe de autenticação
require_once '../classes/Comentario.php'; // Inclui a classe de comentários
require_once '../classes/LogAcao.php'; // Inclui a classe de log de ações

// Verificação de autenticação
$autenticacao = new Autenticacao((new Banco())->conectar()); // Cria uma instância da classe Autenticacao e conecta ao banco de dados
if (!$autenticacao->isLoggedIn()) { // Verifica se o usuário está logado
    header('Location: login.php'); // Redireciona para a página de login se não estiver logado
    exit; // Encerra a execução do script
}

$banco = new Banco(); // Cria uma nova instância da classe Banco
$db = $banco->conectar(); // Conecta ao banco de dados

$comentario = new Comentario($db); // Cria uma instância da classe Comentario
$logAcao = new LogAcao($db); // Cria uma instância da classe LogAcao
$usuario_nome = $_SESSION['usuario_nome']; // Recupera o nome do usuário da sessão

// Verifica se a ação e o ID do comentário foram enviados via POST
if (isset($_POST['acao']) && isset($_POST['id'])) {
    $id = $_POST['id']; // Armazena o ID do comentário
    if ($_POST['acao'] == 'aprovar') { // Verifica se a ação é aprovar
        $comentario->atualizarStatusComentario($id, 'aprovado'); // Atualiza o status do comentário para aprovado
        $logAcao->logarAcao('aprovado', $id, $usuario_nome); // Registra a ação de aprovação no log
    } elseif ($_POST['acao'] == 'reprovar') { // Verifica se a ação é reprovar
        $comentario->atualizarStatusComentario($id, 'reprovado'); // Atualiza o status do comentário para reprovado
        $logAcao->logarAcao('reprovado', $id, $usuario_nome); // Registra a ação de reprovação no log
    }
}

// Verifica se foi solicitado a geração do JSON dos comentários
if (isset($_POST['gerar_json'])) {
    $comentario->gerarJsonComentarios(); // Gera um arquivo JSON com todos os comentários
    $jsonPath = '../comentarios.json'; // Define o caminho do arquivo JSON
    if (file_exists($jsonPath)) { // Verifica se o arquivo JSON existe
        header('Content-Type: application/json'); // Define o cabeçalho do tipo de conteúdo como JSON
        header('Content-Disposition: attachment; filename="comentarios.json"'); // Define o cabeçalho para forçar o download do arquivo
        readfile($jsonPath); // Lê e envia o arquivo para o navegador
        exit; // Encerra a execução do script
    } else {
        echo 'Erro: Arquivo comentarios.json não encontrado.'; // Exibe uma mensagem de erro se o arquivo não for encontrado
    }
}

// Verifica se um arquivo JSON foi enviado para importação de comentários
if (isset($_FILES['arquivo_json'])) {
    $arquivoTmp = $_FILES['arquivo_json']['tmp_name']; // Armazena o caminho temporário do arquivo
    $jsonData = file_get_contents($arquivoTmp); // Lê o conteúdo do arquivo JSON
    $comentariosImportados = json_decode($jsonData, true); // Decodifica o JSON para um array
    if (is_array($comentariosImportados)) { // Verifica se o conteúdo decodificado é um array
        foreach ($comentariosImportados as $c) { // Itera sobre cada comentário importado
            $comentario->adicionarComentario($c['nome'], $c['professor'], $c['comentario']); // Adiciona o comentário ao banco de dados
        }
        $mensagemImportacao = 'Comentários importados com sucesso!'; // Define uma mensagem de sucesso
    } else {
        $erroImportacao = 'Erro ao importar o arquivo JSON. Verifique o formato do arquivo.'; // Define uma mensagem de erro
    }
}

$comentarios = $comentario->obterComentarios()->fetchAll(PDO::FETCH_ASSOC) ?? []; // Obtém todos os comentários do banco de dados

// Verifica se o botão de logout foi pressionado
if (isset($_POST['logout'])) {
    $autenticacao->logout(); // Realiza o logout do usuário
    header('Location: login.php'); // Redireciona para a página de login
    exit; // Encerra a execução do script
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
        <?php if (isset($mensagemImportacao)) : ?>
            <div class="alert alert-success"><?php echo $mensagemImportacao; ?></div>
        <?php endif; ?>
        <?php if (isset($erroImportacao)) : ?>
            <div class="alert alert-danger"><?php echo $erroImportacao; ?></div>
        <?php endif; ?>

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
                <?php if (empty($comentarios)) : ?>
                    <tr>
                        <td colspan="6" class="text-center">Nenhum comentário encontrado.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($comentarios as $comentario) : ?>
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

        <form method="post" enctype="multipart/form-data" class="mt-3">
            <div class="mb-3">
                <label for="arquivo_json" class="form-label">Importar Comentários (JSON)</label>
                <input class="form-control" type="file" id="arquivo_json" name="arquivo_json" accept=".json" required>
            </div>
            <button type="submit" class="btn btn-primary">Importar</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>