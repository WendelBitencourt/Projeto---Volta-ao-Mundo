<?php
class Autenticacao {
    private $conn;
    private $tabela = 'usuarios';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($email, $senha) {
        $query = 'SELECT * FROM ' . $this->tabela . ' WHERE email = :email LIMIT 0,1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            return true;
        }
        return false;
    }

    public function registrar($nome, $email, $senha) {
        $query = 'INSERT INTO ' . $this->tabela . ' SET nome = :nome, email = :email, senha = :senha';
        $stmt = $this->conn->prepare($query);

        $senha_hashed = password_hash($senha, PASSWORD_DEFAULT);

        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha_hashed);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function logout() {
        session_unset();
        session_destroy();
    }

    public function isLoggedIn() {
        return isset($_SESSION['usuario_id']);
    }
}
?>

