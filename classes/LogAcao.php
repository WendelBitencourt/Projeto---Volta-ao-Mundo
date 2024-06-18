<?php
class LogAcao {
    private $conn;
    private $tabela = 'logs_acoes';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function logarAcao($acao, $comentario_id, $usuario_nome) {
        $query = 'INSERT INTO ' . $this->tabela . ' SET acao = :acao, comentario_id = :comentario_id, usuario_nome = :usuario_nome, data_acao = NOW()';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':acao', $acao);
        $stmt->bindParam(':comentario_id', $comentario_id);
        $stmt->bindParam(':usuario_nome', $usuario_nome);
        return $stmt->execute();
    }

    public function obterLogs() {
        $query = 'SELECT * FROM ' . $this->tabela . ' ORDER BY data_acao DESC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>

