<?php
/**
 * Página de Registro Vulnerável
 * ATENÇÃO: Este código é propositalmente vulnerável para fins educacionais
 */

// Incluir classe de conexão com banco
require_once '../../config/database.php';

$error_message = '';
$success_message = '';

// Processar cadastro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm-password'] ?? '';
    
    // Validações básicas (propositalmente fracas)
    if (empty($fullname) || empty($username) || empty($email) || empty($password)) {
        $error_message = "Todos os campos são obrigatórios!";
    } elseif ($password !== $confirm_password) {
        $error_message = "As senhas não coincidem!";
    } elseif (strlen($password) < 3) {
        $error_message = "Senha muito curta! (mínimo 3 caracteres - muito fraco!)";
    } else {
        $db = new Database();
        
        try {
            // Verificar se usuário já existe (vulnerável a SQL injection)
            $check_query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
            $existing_user = $db->query($check_query);
            
            if ($existing_user && $existing_user->num_rows > 0) {
                $error_message = "Usuário ou email já existe!";
            } else {
                // Registrar usuário (VULNERÁVEL - sem sanitização)
                if ($db->registerUser($username, $password, $email, $fullname)) {
                    $success_message = "Cadastro realizado com sucesso! Você será redirecionado para o login.";
                    
                    // Log do cadastro
                    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
                    
                    // VULNERÁVEL - loga a senha em texto plano
                    $log_query = "INSERT INTO login_logs (username, password_attempted, ip_address, user_agent, success) 
                                 VALUES ('NEW_USER: $username', '$password', '$ip', '$user_agent', TRUE)";
                    $db->query($log_query);
                    
                    // Redirecionar após 3 segundos
                    header("refresh:3;url=login.php");
                } else {
                    $error_message = "Erro ao cadastrar usuário. Tente novamente.";
                }
            }
        } catch (Exception $e) {
            $error_message = "Erro no sistema: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Sistema Vulnerável</title>
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
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        input:focus {
            outline: none;
            border-color: #5cb85c;
            box-shadow: 0 0 5px rgba(92, 184, 92, 0.3);
        }
        .btn {
            width: 100%;
            padding: 12px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 15px;
        }
        .btn:hover {
            background-color: #449d44;
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
        .xss-demo {
            color: #d9534f;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📝 Cadastro de Usuário</h1>
        
        <div class="vulnerability-info">
            <strong>⚠️ Vulnerabilidades:</strong> Este formulário é vulnerável a SQL Injection e armazena senhas em texto plano. 
            Teste scripts maliciosos nos campos de texto.
        </div>

        <?php if ($error_message): ?>
            <div class="error">❌ <?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="success">✅ <?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="fullname">👤 Nome Completo:</label>
                <input type="text" id="fullname" name="fullname" value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>" required>
                <small>⚠️ Campo vulnerável a XSS quando exibido</small>
            </div>
            
            <div class="form-group">
                <label for="username">🏷️ Nome de Usuário:</label>
                <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                <small>🎯 Teste SQLi: <strong>test'; DROP TABLE users; --</strong></small>
            </div>
            
            <div class="form-group">
                <label for="email">📧 Email:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                <small>Email validado apenas pelo HTML5 (fraco)</small>
            </div>
            
            <div class="form-group">
                <label for="password">🔑 Senha:</label>
                <input type="password" id="password" name="password" required>
                <small class="xss-demo">⚠️ Senhas são armazenadas em TEXTO PLANO!</small>
            </div>
            
            <div class="form-group">
                <label for="confirm-password">🔑 Confirmar Senha:</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
                <small>Validação apenas no lado cliente (vulnerável)</small>
            </div>
            
            <button type="submit" class="btn">Cadastrar</button>
        </form>

        <div class="links">
            <a href="login.php">🔐 Já tem conta? Faça login</a>
            <a href="../../index.php">🏠 Voltar ao Início</a>
            <a href="../../admin.php?admin=true">⚙️ Ver Usuários Cadastrados</a>
        </div>

        <div style="margin-top: 30px; padding: 15px; background-color: #f8f8f8; border-radius: 4px;">
            <h4>Vulnerabilidades para Testar:</h4>
            <ul style="font-size: 12px; color: #666;">
                <li><strong>SQL Injection no Username:</strong> 
                    <br><code>hacker'; INSERT INTO users (username,password,email,full_name,role) VALUES ('backdoor','123',''backdoor@evil.com','Hacker','admin'); --</code>
                </li>
                <li><strong>XSS no Nome Completo:</strong> 
                    <br><code>&lt;script&gt;alert('XSS no nome!')&lt;/script&gt;</code>
                </li>
                <li><strong>Email malicioso:</strong> 
                    <br><code>"&gt;&lt;script&gt;alert('XSS')&lt;/script&gt;"@test.com</code>
                </li>
                <li><strong>Senha ultra-fraca:</strong> Sistema aceita senhas de 3+ caracteres</li>
            </ul>
            
            <h4>Problemas de Segurança:</h4>
            <ul style="font-size: 12px; color: #d9534f;">
                <li>✗ Senhas em texto plano no banco</li>
                <li>✗ Sem sanitização de entrada</li>
                <li>✗ Validação apenas no cliente</li>
                <li>✗ Log de senhas em texto plano</li>
                <li>✗ Consultas SQL vulneráveis</li>
                <li>✗ Sem rate limiting</li>
                <li>✗ Informações detalhadas de erro</li>
            </ul>
            
            <p style="font-size: 11px; color: #888; margin-top: 10px;">
                💡 <strong>Dica:</strong> Após cadastrar, vá ao painel admin para ver como seus dados ficaram armazenados!
            </p>
        </div>
    </div>

    <script>
        // JavaScript vulnerável para demonstração
        document.addEventListener('DOMContentLoaded', function() {
            // Demonstrar XSS em tempo real no campo nome
            const nameField = document.getElementById('fullname');
            nameField.addEventListener('input', function() {
                const value = nameField.value;
                
                // VULNERÁVEL - executaria scripts (desabilitado para segurança da demo)
                if (value.includes('<script>')) {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'error';
                    messageDiv.innerHTML = '🎯 XSS detectado no campo nome: ' + value;
                    
                    const form = document.querySelector('form');
                    form.insertBefore(messageDiv, form.firstChild);
                    
                    setTimeout(() => messageDiv.remove(), 3000);
                }
            });
            
            // Validação fraca de senha
            const passwordField = document.getElementById('password');
            const confirmField = document.getElementById('confirm-password');
            
            function validatePasswords() {
                const password = passwordField.value;
                const confirm = confirmField.value;
                
                if (password.length > 0 && password.length < 6) {
                    passwordField.style.borderColor = '#ff9999';
                    passwordField.title = 'Senha fraca, mas será aceita mesmo assim!';
                } else {
                    passwordField.style.borderColor = '#ddd';
                    passwordField.title = '';
                }
            }
            
            passwordField.addEventListener('input', validatePasswords);
            confirmField.addEventListener('input', validatePasswords);
        });
    </script>
</body>
</html>