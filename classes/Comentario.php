<?php
class Comentario {
    private $conn;
    private $tabela = 'comentarios';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function adicionarComentario($nome, $email, $conteudo) {
        $query = 'INSERT INTO ' . $this->tabela . ' SET nome = :nome, email = :email, conteudo = :conteudo, status = "pendente"';
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':conteudo', $conteudo);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function obterComentarios() {
        $query = 'SELECT * FROM ' . $this->tabela . ' WHERE status != "reprovado"';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function atualizarStatusComentario($id, $status) {
        $query = 'UPDATE ' . $this->tabela . ' SET status = :status WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function importarComentarios($comentarios) {
        foreach ($comentarios as $comentario) {
            if (is_array($comentario) && isset($comentario['nome']) && isset($comentario['email']) && isset($comentario['conteudo'])) {
                $this->adicionarComentario($comentario['nome'], $comentario['email'], $comentario['conteudo']);
            } else {
                echo 'Erro: Formato de comentário inválido.';
            }
        }
    }

    public function gerarJsonComentarios() {
        $query = 'SELECT nome, email, conteudo FROM ' . $this->tabela . ' WHERE status != "reprovado"';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $json = json_encode($comentarios, JSON_PRETTY_PRINT);
        file_put_contents(dirname(__DIR__) . '/comentarios.json', $json);
    }
}
?>
