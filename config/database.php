<?php
/**
 * Configuração de Conexão com Banco de Dados MySQL
 * ATENÇÃO: Este código é propositalmente vulnerável para fins educacionais
 */

class Database {
    // Configurações do banco de dados
    private $host = "localhost";
    private $username = "root";
    private $password = ""; // Senha vazia - VULNERÁVEL
    private $database = "vulnerable_db";
    private $charset = "utf8mb4";
    
    public $connection;
    
    /**
     * Construtor - conecta automaticamente ao banco
     */
    public function __construct() {
        $this->connect();
    }
    
    /**
     * Conecta ao banco de dados MySQL
     * @return mysqli|null
     */
    public function connect() {
        try {
            // Conexão básica com MySQL - sem SSL e configurações de segurança
            $this->connection = new mysqli(
                $this->host, 
                $this->username, 
                $this->password, 
                $this->database
            );
            
            // Verifica se houve erro na conexão
            if ($this->connection->connect_error) {
                throw new Exception("Falha na conexão: " . $this->connection->connect_error);
            }
            
            // Define charset (vulnerável a algumas injeções de charset)
            $this->connection->set_charset($this->charset);
            
            return $this->connection;
            
        } catch (Exception $e) {
            die("Erro de conexão com o banco: " . $e->getMessage());
        }
    }
    
    /**
     * Executa uma query vulnerável (sem prepared statements)
     * @param string $query
     * @return mysqli_result|bool
     */
    public function query($query) {
        // VULNERÁVEL - executa query diretamente sem sanitização
        $result = $this->connection->query($query);
        
        if (!$result) {
            echo "Erro na query: " . $this->connection->error . "<br>";
            echo "Query executada: " . $query . "<br>";
        }
        
        return $result;
    }
    
    /**
     * Método vulnerável para autenticação
     * @param string $username
     * @param string $password
     * @return array|null
     */
    public function authenticateUser($username, $password) {
        // VULNERÁVEL A SQL INJECTION - concatenação direta
        $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        
        // Debug - mostra a query (NUNCA fazer em produção)
        echo "<!-- Query executada: $query -->";
        
        $result = $this->query($query);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Registra um novo usuário (método vulnerável)
     * @param string $username
     * @param string $password
     * @param string $email
     * @param string $fullName
     * @return bool
     */
    public function registerUser($username, $password, $email, $fullName) {
        // VULNERÁVEL - sem sanitização e senha em texto plano
        $query = "INSERT INTO users (username, password, email, full_name) 
                  VALUES ('$username', '$password', '$email', '$fullName')";
        
        return $this->query($query) ? true : false;
    }
    
    /**
     * Adiciona comentário (vulnerável a XSS)
     * @param string $name
     * @param string $comment
     * @return bool
     */
    public function addComment($name, $comment) {
        // VULNERÁVEL - sem sanitização para XSS
        $query = "INSERT INTO comments (name, comment) VALUES ('$name', '$comment')";
        
        return $this->query($query) ? true : false;
    }
    
    /**
     * Busca comentários (retorna dados não sanitizados)
     * @return array
     */
    public function getComments() {
        $query = "SELECT * FROM comments ORDER BY created_at DESC";
        $result = $this->query($query);
        
        $comments = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $comments[] = $row;
            }
        }
        
        return $comments;
    }
    
    /**
     * Busca todos os usuários (VULNERÁVEL - expõe senhas)
     * @return array
     */
    public function getAllUsers() {
        // PERIGOSO - expõe senhas em texto plano
        $query = "SELECT id, username, password, email, full_name, created_at FROM users";
        $result = $this->query($query);
        
        $users = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }
        
        return $users;
    }
    
    /**
     * Busca usuário por ID (vulnerável a SQL injection)
     * @param string $id
     * @return array|null
     */
    public function getUserById($id) {
        // VULNERÁVEL - sem validação de entrada
        $query = "SELECT * FROM users WHERE id = $id";
        $result = $this->query($query);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Executa query personalizada (MUITO PERIGOSO)
     * @param string $customQuery
     * @return mysqli_result|bool
     */
    public function executeCustomQuery($customQuery) {
        // EXTREMAMENTE VULNERÁVEL - permite qualquer query
        echo "<!-- Executando query personalizada: $customQuery -->";
        return $this->query($customQuery);
    }
    
    /**
     * Fecha a conexão
     */
    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
    
    /**
     * Destrutor - fecha conexão automaticamente
     */
    public function __destruct() {
        $this->close();
    }
}

// Função global para obter conexão (prática ruim - variável global)
function getDatabase() {
    static $database = null;
    
    if ($database === null) {
        $database = new Database();
    }
    
    return $database;
}

// Exemplo de uso vulnerável:
/*
$db = new Database();

// SQL Injection no login
$user = $db->authenticateUser("admin' OR '1'='1'-- ", "qualquer_coisa");

// XSS nos comentários  
$db->addComment("Hacker", "<script>alert('XSS')</script>");

// Exposição de dados sensíveis
$users = $db->getAllUsers(); // Retorna senhas em texto plano

// Query personalizada perigosa
$db->executeCustomQuery("DROP TABLE users; --");
*/

?>