<?php

class Database
{
    private string $host = '127.0.0.1';
    private string $dbName = 'bd_mundo';
    private string $user = 'root';
    private string $password = '';
    private ?PDO $conn = null;

    public function getConnection(): PDO
    {
        if ($this->conn === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4";
                $this->conn = new PDO($dsn, $this->user, $this->password, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()
                ]);
                exit;
            }
        }

        return $this->conn;
    }
}
