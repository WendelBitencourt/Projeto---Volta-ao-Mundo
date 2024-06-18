<?php
// Define a classe Comentario
class Comentario
{
    private $conn; // Propriedade para armazenar a conexão com o banco de dados
    private $tabela = 'comentarios'; // Nome da tabela no banco de dados

    // Construtor da classe, recebe a conexão com o banco de dados
    public function __construct($db)
    {
        $this->conn = $db; // Atribui a conexão recebida à propriedade $conn
    }

    // Método para adicionar um novo comentário ao banco de dados
    public function adicionarComentario($nome, $email, $conteudo)
    {
        // Prepara a query SQL para inserção de um novo comentário
        $query = 'INSERT INTO ' . $this->tabela . ' SET nome = :nome, email = :email, conteudo = :conteudo, status = "pendente"';
        $stmt = $this->conn->prepare($query); // Prepara a query

        // Associa os valores recebidos aos parâmetros da query
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':conteudo', $conteudo);

        return $stmt->execute(); // Executa a query e retorna o resultado (true em caso de sucesso, false em caso de falha)
    }

    // Método para obter todos os comentários aprovados
    public function obterComentariosAprovados()
    {
        // Prepara a query SQL para seleção de todos os comentários aprovados
        $query = 'SELECT * FROM ' . $this->tabela . ' WHERE status = "aprovado"';
        $stmt = $this->conn->prepare($query); // Prepara a query
        $stmt->execute(); // Executa a query
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retorna os resultados como um array associativo
    }

    // Método para obter todos os comentários que não estão reprovados
    public function obterComentarios()
    {
        // Prepara a query SQL para seleção de todos os comentários não reprovados
        $query = 'SELECT * FROM ' . $this->tabela . ' WHERE status != "reprovado"';
        $stmt = $this->conn->prepare($query); // Prepara a query
        $stmt->execute(); // Executa a query
        return $stmt; // Retorna o objeto statement com o resultado
    }

    // Método para atualizar o status de um comentário
    public function atualizarStatusComentario($id, $status)
    {
        // Prepara a query SQL para atualização do status de um comentário
        $query = 'UPDATE ' . $this->tabela . ' SET status = :status WHERE id = :id';
        $stmt = $this->conn->prepare($query); // Prepara a query

        // Associa os valores recebidos aos parâmetros da query
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);

        return $stmt->execute(); // Executa a query e retorna o resultado (true em caso de sucesso, false em caso de falha)
    }

    // Método para gerar um arquivo JSON com todos os comentários não reprovados
    public function gerarJsonComentarios()
    {
        // Prepara a query SQL para seleção de nome, email e conteúdo de todos os comentários não reprovados
        $query = 'SELECT nome, email, conteudo FROM ' . $this->tabela . ' WHERE status != "reprovado"';
        $stmt = $this->conn->prepare($query); // Prepara a query
        $stmt->execute(); // Executa a query
        $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtém os resultados como um array associativo
        $json = json_encode($comentarios, JSON_PRETTY_PRINT); // Codifica o array em JSON, formatando para melhor leitura
        file_put_contents(dirname(__DIR__) . '/comentarios.json', $json); // Salva o JSON em um arquivo no diretório pai
    }
}
