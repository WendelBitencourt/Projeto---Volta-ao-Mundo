<?php
// Define a classe LogAcao para gerenciar logs de ações no banco de dados
class LogAcao
{
    private $conn; // Variável para armazenar a conexão com o banco de dados
    private $tabela = 'logs_acoes'; // Nome da tabela no banco de dados para logs de ações

    // Construtor da classe, inicializa a conexão com o banco de dados
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Método para registrar uma nova ação no banco de dados
    public function logarAcao($acao, $comentario_id, $usuario_nome)
    {
        // Prepara a consulta SQL para inserir o novo log de ação
        $query = 'INSERT INTO ' . $this->tabela . ' SET acao = :acao, comentario_id = :comentario_id, usuario_nome = :usuario_nome, data_acao = NOW()';
        $stmt = $this->conn->prepare($query);

        // Associa os valores aos parâmetros da consulta
        $stmt->bindParam(':acao', $acao);
        $stmt->bindParam(':comentario_id', $comentario_id);
        $stmt->bindParam(':usuario_nome', $usuario_nome);

        // Executa a consulta e retorna verdadeiro se bem-sucedido
        return $stmt->execute();
    }

    // Método para obter todos os logs de ações, ordenados pela data de ação em ordem decrescente
    public function obterLogs()
    {
        // Prepara a consulta SQL para selecionar todos os logs de ações
        $query = 'SELECT * FROM ' . $this->tabela . ' ORDER BY data_acao DESC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute(); // Executa a consulta
        return $stmt; // Retorna o objeto de declaração preparada com os resultados
    }
}
