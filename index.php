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
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
                $username = $_POST['username'];
                $password = $_POST['password'];
                
                // Conexão vulnerável ao banco de dados
                $conn = new mysqli("localhost", "root", "", "vulnerable_db");
                
                if ($conn->connect_error) {
                    die("Conexão falhou: " . $conn->connect_error);
                }
                
                // Query vulnerável a SQL Injection
                $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    echo "Login bem-sucedido! Bem-vindo, $username!";
                } else {
                    echo "Usuário ou senha incorretos.";
                }
                
                $conn->close();
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
                
                // Conexão ao banco de dados
                $conn = new mysqli("localhost", "root", "", "vulnerable_db");
                
                if ($conn->connect_error) {
                    die("Conexão falhou: " . $conn->connect_error);
                }
                
                // Inserir comentário sem sanitização (vulnerável a XSS)
                $sql = "INSERT INTO comments (name, comment) VALUES ('$name', '$comment')";
                
                if ($conn->query($sql) === TRUE) {
                    echo "Comentário adicionado com sucesso!";
                } else {
                    echo "Erro: " . $sql . "<br>" . $conn->error;
                }
                
                $conn->close();
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
        // Exibir comentários sem sanitização (vulnerável a XSS)
        $conn = new mysqli("localhost", "root", "", "vulnerable_db");
        
        if ($conn->connect_error) {
            die("Conexão falhou: " . $conn->connect_error);
        }
        
        $sql = "SELECT name, comment, created_at FROM comments ORDER BY created_at DESC";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div class='comment'>";
                echo "<strong>" . $row["name"] . "</strong> - " . $row["created_at"] . "<br>";
                echo $row["comment"]; // Vulnerável a XSS
                echo "</div>";
            }
        } else {
            echo "Nenhum comentário ainda.";
        }
        
        $conn->close();
        ?>
    </div>
</body>
</html>