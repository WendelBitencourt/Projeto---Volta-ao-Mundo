<?php
// Define a classe Autenticacao
class Autenticacao
{
    private $conn; // Variável para armazenar a conexão com o banco de dados
    private $tabela = 'usuarios'; // Nome da tabela no banco de dados

    // Construtor da classe, inicializa a conexão com o banco de dados
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Método para realizar o login do usuário
    public function login($email, $senha)
    {
        // Prepara a consulta SQL para buscar o usuário pelo email
        $query = 'SELECT * FROM ' . $this->tabela . ' WHERE email = :email LIMIT 0,1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email); // Associa o valor do email ao parâmetro da consulta
        $stmt->execute(); // Executa a consulta

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC); // Obtém o resultado da consulta
        // Verifica se o usuário existe e se a senha está correta
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id']; // Armazena o ID do usuário na sessão
            $_SESSION['usuario_nome'] = $usuario['nome']; // Armazena o nome do usuário na sessão
            return true; // Retorna verdadeiro se o login for bem-sucedido
        }
        return false; // Retorna falso se o login falhar
    }

    // Método para registrar um novo usuário
    public function registrar($nome, $email, $senha)
    {
        // Prepara a consulta SQL para inserir o novo usuário
        $query = 'INSERT INTO ' . $this->tabela . ' SET nome = :nome, email = :email, senha = :senha';
        $stmt = $this->conn->prepare($query);

        $senha_hashed = password_hash($senha, PASSWORD_DEFAULT); // Gera um hash da senha

        // Associa os valores aos parâmetros da consulta
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha_hashed);

        // Executa a consulta e retorna verdadeiro se bem-sucedido
        if ($stmt->execute()) {
            return true;
        }
        return false; // Retorna falso se a inserção falhar
    }

    // Método para realizar o logout do usuário
    public function logout()
    {
        session_unset(); // Remove todas as variáveis de sessão
        session_destroy(); // Destrói a sessão
    }

    // Método para verificar se o usuário está logado
    public function isLoggedIn()
    {
        return isset($_SESSION['usuario_id']); // Retorna verdadeiro se o usuário estiver logado
    }
}
