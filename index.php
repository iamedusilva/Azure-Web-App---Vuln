<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Vulnerável para Aprendizagem</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="password"], textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .comment {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Site Vulnerável para Aprendizagem</h1>
        
        <!-- Formulário de Login com SQL Injection -->
        <h2>Login (Vulnerável a SQL Injection)</h2>
        <div class="error">
            <?php
            // Incluir classe de conexão com banco
            require_once 'config/database.php';
            
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
                $username = $_POST['username'];
                $password = $_POST['password'];
                
                // Usar classe Database vulnerável
                $db = new Database();
                
                // Autenticação vulnerável (permite SQL Injection)
                $user = $db->authenticateUser($username, $password);
                
                if ($user) {
                    echo "Login bem-sucedido! Bem-vindo, " . $user['full_name'] . "! (Role: " . $user['role'] . ")";
                    
                    // Log da tentativa de login (armazena senha!)
                    $db->query("INSERT INTO login_logs (username, password_attempted, ip_address, user_agent, success) 
                               VALUES ('$username', '$password', '{$_SERVER['REMOTE_ADDR']}', '{$_SERVER['HTTP_USER_AGENT']}', TRUE)");
                } else {
                    echo "Usuário ou senha incorretos.";
                    
                    // Log da tentativa falhada (armazena senha tentada!)
                    $db->query("INSERT INTO login_logs (username, password_attempted, ip_address, user_agent, success) 
                               VALUES ('$username', '$password', '{$_SERVER['REMOTE_ADDR']}', '{$_SERVER['HTTP_USER_AGENT']}', FALSE)");
                }
            }
            ?>
        </div>
        <form method="post" action="">
            <div class="form-group">
                <label for="username">Usuário:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="login">Login</button>
        </form>
        
        <hr>
        
        <!-- Formulário de Comentários com XSS -->
        <h2>Comentários (Vulnerável a XSS)</h2>
        <div class="error">
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment'])) {
                $comment = $_POST['comment'];
                $name = $_POST['name'];
                
                // Usar classe Database vulnerável
                $db = new Database();
                
                // Inserir comentário vulnerável (permite XSS e SQL Injection)
                if ($db->addComment($name, $comment)) {
                    echo "Comentário adicionado com sucesso!";
                } else {
                    echo "Erro ao adicionar comentário.";
                }
            }
            ?>
        </div>
        <form method="post" action="">
            <div class="form-group">
                <label for="name">Nome:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="comment">Comentário:</label>
                <textarea id="comment" name="comment" rows="4" required></textarea>
            </div>
            <button type="submit" name="comment">Enviar Comentário</button>
        </form>
        
        <h3>Comentários Recentes:</h3>
        <?php
        // Exibir comentários usando a classe Database
        $db = new Database();
        $comments = $db->getComments();
        
        if (!empty($comments)) {
            foreach($comments as $comment) {
                echo "<div class='comment'>";
                echo "<strong>" . $comment["name"] . "</strong> - " . $comment["created_at"] . "<br>";
                echo $comment["comment"]; // Vulnerável a XSS - não sanitizado
                echo "</div>";
            }
        } else {
            echo "Nenhum comentário ainda.";
        }
        ?>
    </div>
</body>
</html>