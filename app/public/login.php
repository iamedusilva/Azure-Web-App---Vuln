<?php
/**
 * Página de Login Vulnerável
 * ATENÇÃO: Este código é propositalmente vulnerável para fins educacionais
 */

// Incluir classe de conexão com banco
require_once '../../config/database.php';

$error_message = '';
$success_message = '';

// Processar login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Usar classe Database vulnerável
    $db = new Database();
    
    try {
        // Autenticação vulnerável (permite SQL Injection)
        $user = $db->authenticateUser($username, $password);
        
        if ($user) {
            $success_message = "Login bem-sucedido! Bem-vindo, " . htmlspecialchars($user['full_name']) . "! (Role: " . htmlspecialchars($user['role']) . ")";
            
            // Iniciar sessão vulnerável
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            
            // Log da tentativa de login (armazena senha!)
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            $db->query("INSERT INTO login_logs (username, password_attempted, ip_address, user_agent, success) 
                       VALUES ('$username', '$password', '$ip', '$user_agent', TRUE)");
            
            // Redirecionar após 2 segundos
            header("refresh:2;url=dashboard.php");
        } else {
            $error_message = "Usuário ou senha incorretos.";
            
            // Log da tentativa falhada (armazena senha tentada!)
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            $db->query("INSERT INTO login_logs (username, password_attempted, ip_address, user_agent, success) 
                       VALUES ('$username', '$password', '$ip', '$user_agent', FALSE)");
        }
    } catch (Exception $e) {
        $error_message = "Erro no sistema: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Vulnerável</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 500px;
            margin: 50px auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        input:focus {
            outline: none;
            border-color: #337ab7;
            box-shadow: 0 0 5px rgba(51, 122, 183, 0.3);
        }
        .btn {
            width: 100%;
            padding: 12px;
            background-color: #337ab7;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 15px;
        }
        .btn:hover {
            background-color: #286090;
        }
        .error {
            color: #d9534f;
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .success {
            color: #3c763d;
            background-color: #dff0d8;
            border: 1px solid #d6e9c6;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .links {
            text-align: center;
            margin-top: 20px;
        }
        .links a {
            color: #337ab7;
            text-decoration: none;
            margin: 0 10px;
        }
        .links a:hover {
            text-decoration: underline;
        }
        .vulnerability-info {
            background-color: #fcf8e3;
            border: 1px solid #faebcc;
            color: #8a6d3b;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        small {
            color: #666;
            font-size: 12px;
            display: block;
            margin-top: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Login do Sistema</h1>
        
        <div class="vulnerability-info">
            <strong>⚠️ Vulnerabilidade:</strong> Este formulário é vulnerável a SQL Injection. 
            Teste com: <code>admin' OR '1'='1'--</code>
        </div>

        <?php if ($error_message): ?>
            <div class="error">❌ <?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="success">✅ <?php echo $success_message; ?></div>
            <script>
                setTimeout(() => {
                    window.location.href = 'dashboard.php';
                }, 2000);
            </script>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="username">👤 Usuário:</label>
                <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                <small>Teste com: <strong>admin' OR '1'='1'--</strong></small>
            </div>
            
            <div class="form-group">
                <label for="password">🔑 Senha:</label>
                <input type="password" id="password" name="password" required>
                <small>Qualquer valor funciona com SQLi</small>
            </div>
            
            <button type="submit" class="btn">Entrar</button>
        </form>

        <div class="links">
            <a href="register.php">📝 Não tem conta? Cadastre-se</a>
            <a href="../../index.php">🏠 Voltar ao Início</a>
            <a href="../../admin.php?admin=true">⚙️ Painel Admin</a>
        </div>

        <div style="margin-top: 30px; padding: 15px; background-color: #f8f8f8; border-radius: 4px;">
            <h4>Usuários de Teste Válidos:</h4>
            <ul style="font-size: 12px; color: #666;">
                <li><strong>admin</strong> / admin</li>
                <li><strong>root</strong> / 123456</li>
                <li><strong>user1</strong> / password</li>
                <li><strong>guest</strong> / guest</li>
            </ul>
            
            <h4>Exemplos de Ataques SQL Injection:</h4>
            <ul style="font-size: 12px; color: #666;">
                <li><strong>Bypass básico:</strong> admin' OR '1'='1'--</li>
                <li><strong>Union attack:</strong> ' UNION SELECT 1,2,3,username,password,6,7,8 FROM users--</li>
                <li><strong>Blind SQLi:</strong> admin' AND SLEEP(3)--</li>
                <li><strong>Boolean-based:</strong> admin' AND '1'='1</li>
            </ul>
            
            <p style="font-size: 11px; color: #888; margin-top: 10px;">
                💡 <strong>Dica:</strong> Observe que as tentativas de login (incluindo senhas) são logadas no banco de dados!
            </p>
        </div>
    </div>
</body>
</html>