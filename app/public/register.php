<?php
/**
 * Página de Registro Vulnerável
 * ATENÇÃO: Este código é propositalmente vulnerável para fins educacionais
 */

// Incluir classe de conexão com banco
require_once '../../config/database.php';

// Iniciar sessão se ainda não foi iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar se usuário já está logado
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    // Redirecionar para dashboard se já estiver logado
    header('Location: dashboard.php');
    exit;
}

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
    <title>Cadastro - FinSecure</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- CSS Styles -->
    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            overflow-x: hidden;
            padding: 1rem 0;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 25% 25%, #667eea 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, #764ba2 0%, transparent 50%);
            opacity: 0.1;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .register-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 2;
            animation: slideUp 1s ease-out;
            margin: 0.5rem;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            text-align: center;
            margin-bottom: 1.2rem;
        }

        .logo i {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #00bcd4 0%, #1976d2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
            display: block;
        }

        .logo h1 {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #1976d2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.3rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .logo p {
            color: #cccccc;
            font-size: 0.9rem;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 1rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.4rem;
            color: #cccccc;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .form-group i {
            position: absolute;
            left: 1rem;
            top: 2.4rem;
            color: #667eea;
            font-size: 0.9rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem 0.8rem 0.8rem 2.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #00bcd4;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: rgba(255, 255, 255, 0.08);
        }

        .form-group input::placeholder {
            color: #888;
        }

        .form-group small {
            color: #888;
            font-size: 0.85rem;
            margin-top: 0.5rem;
            display: block;
        }

        .form-group small strong {
            color: #00bcd4;
        }

        .form-group small.warning {
            color: #ffc107;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 2.4rem;
            color: #667eea;
            cursor: pointer;
            transition: color 0.3s ease;
            z-index: 10;
        }

        .password-toggle:hover {
            color: #00bcd4;
        }

        .btn {
            width: 100%;
            padding: 0.9rem;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #00bcd4 0%, #1976d2 100%);
            color: white;
            margin-bottom: 1rem;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .links {
            text-align: center;
            margin-top: 2rem;
        }

        .links a {
            color: #667eea;
            text-decoration: none;
            margin: 0 1rem;
            font-weight: 500;
            transition: color 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .links a:hover {
            color: #cccccc;
        }

        .error, .success {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 500;
        }

        .error {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #dc3545;
        }

        .success {
            background: rgba(40, 167, 69, 0.1);
            border: 1px solid rgba(40, 167, 69, 0.3);
            color: #28a745;
        }

        .demo-info {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .demo-info h4 {
            color: #00bcd4;
            margin-bottom: 1rem;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .demo-info ul {
            list-style: none;
            padding: 0;
        }

        .demo-info li {
            color: #cccccc;
            font-size: 0.9rem;
            margin-bottom: 0.8rem;
            padding: 0.5rem;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 6px;
            line-height: 1.4;
        }

        .demo-info li strong {
            color: #00bcd4;
        }

        .demo-info code {
            background: rgba(255, 193, 7, 0.1);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            color: #ffc107;
        }

        .security-issues {
            background: rgba(220, 53, 69, 0.05);
            border: 1px solid rgba(220, 53, 69, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1rem;
        }

        .security-issues h4 {
            color: #dc3545;
            margin-bottom: 1rem;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .security-issues li {
            color: #dc3545 !important;
            background: rgba(220, 53, 69, 0.05) !important;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .register-container {
                max-width: 480px;
                padding: 2.5rem;
            }
            
            .logo img {
                width: 280px !important;
                height: 65px !important;
            }
        }
        
        @media (max-width: 992px) {
            .register-container {
                max-width: 420px;
                padding: 2rem;
            }
        }
        
        @media (max-width: 768px) {
            .register-container {
                margin: 1rem;
                padding: 2rem;
                max-width: none;
            }
            
            .logo img {
                width: 200px !important;
                height: 47px !important;
            }
            
            .links a {
                display: block;
                margin: 0.5rem 0;
                padding: 0.8rem;
                background: rgba(255, 255, 255, 0.05);
                border-radius: 8px;
                transition: background 0.3s ease;
            }
            
            .links a:hover {
                background: rgba(255, 255, 255, 0.1);
            }
        }
        
        @media (max-width: 576px) {
            body {
                padding: 1rem 0;
            }
            
            .register-container {
                margin: 0.5rem;
                padding: 1.5rem;
                border-radius: 15px;
            }
            
            .logo h1 {
                font-size: 1.6rem;
            }
            
            .logo i {
                font-size: 2rem;
            }
            
            .form-group input {
                padding: 0.9rem 0.9rem 0.9rem 2.8rem;
                font-size: 0.95rem;
            }
            
            .form-group i {
                left: 0.9rem;
                font-size: 0.9rem;
            }
            
            .btn {
                padding: 0.9rem;
                font-size: 1rem;
            }
        }
        
        @media (max-width: 480px) {
            .register-container {
                margin: 0.25rem;
                padding: 1.2rem;
            }
            
            .logo h1 {
                font-size: 1.4rem;
            }
            
            .form-group input {
                padding: 0.8rem 0.8rem 0.8rem 2.5rem;
            }
            
            .form-group i {
                left: 0.8rem;
                top: 2.8rem;
            }
            
            .links a {
                padding: 0.6rem;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 360px) {
            .register-container {
                padding: 1rem;
            }
            
            .logo {
                margin-bottom: 1.5rem;
            }
            
            .logo h1 {
                font-size: 1.3rem;
            }
            
            .form-group {
                margin-bottom: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <img src="assets/images/PingPay(img-letrabranca).png" alt="PingPay Logo" style="
                width: 300px; 
                height: 70px; 
                object-fit: contain;
                margin: 0 auto 0.5rem;
                display: block;
                filter: brightness(1.1) contrast(1.2);
            ">
            <p>Crie sua conta</p>
        </div>
        
        <?php if ($error_message): ?>
            <div class="error">
                <i class="fas fa-times-circle"></i> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="fullname">
                    <i class="fas fa-user"></i> Nome Completo
                </label>
                <i class="fas fa-user"></i>
                <input type="text" id="fullname" name="fullname" 
                       value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>" 
                       placeholder="Digite seu nome completo" required>
            </div>
            
            <div class="form-group">
                <label for="username">
                    <i class="fas fa-at"></i> Nome de Usuário
                </label>
                <i class="fas fa-at"></i>
                <input type="text" id="username" name="username" 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                       placeholder="Escolha um nome de usuário" required>
            </div>
            
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" name="email" 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                       placeholder="Digite seu email" required>
            </div>
            
            <div class="form-group">
                <label for="password">
                    Senha
                </label>
                <input type="password" id="password" name="password" 
                       placeholder="Crie uma senha" required>
                <i class="fas fa-eye password-toggle" onclick="togglePassword('password', this)"></i>
            </div>
            
            <div class="form-group">
                <label for="confirm-password">
                    Confirmar Senha
                </label>
                <input type="password" id="confirm-password" name="confirm-password" 
                       placeholder="Confirme sua senha" required>
                <i class="fas fa-eye password-toggle" onclick="togglePassword('confirm-password', this)"></i>
            </div>
            
            <button type="submit" class="btn">
                <i class="fas fa-bolt"></i> Criar Conta FinSecure
            </button>
        </form>

        <div class="links">
            <a href="login.php">
                <i class="fas fa-sign-in-alt"></i> Já tem conta?
            </a>
            <a href="../../index.php">
                <i class="fas fa-home"></i> Início
            </a>
        </div>
    </div>

    <script>
        // JavaScript para demonstração de vulnerabilidades
        document.addEventListener('DOMContentLoaded', function() {
            // Demonstrar XSS em tempo real no campo nome
            const nameField = document.getElementById('fullname');
            nameField.addEventListener('input', function() {
                const value = nameField.value;
                
                // Detectar tentativa de XSS
                if (value.includes('<script>') || value.includes('javascript:') || value.includes('onload=')) {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'error';
                    messageDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> XSS detectado no campo nome: ' + value;
                    
                    const form = document.querySelector('form');
                    const existing = document.querySelector('.xss-alert');
                    if (existing) existing.remove();
                    
                    messageDiv.classList.add('xss-alert');
                    form.insertBefore(messageDiv, form.firstChild);
                    
                    setTimeout(() => messageDiv.remove(), 3000);
                }
            });
            
            // Validação fraca de senha com feedback visual
            const passwordField = document.getElementById('password');
            const confirmField = document.getElementById('confirm-password');
            
            function validatePasswords() {
                const password = passwordField.value;
                const confirm = confirmField.value;
                
                // Feedback visual para senha fraca
                if (password.length > 0 && password.length < 6) {
                    passwordField.style.borderColor = '#ffc107';
                    passwordField.style.boxShadow = '0 0 0 3px rgba(255, 193, 7, 0.1)';
                } else if (password.length >= 6) {
                    passwordField.style.borderColor = '#28a745';
                    passwordField.style.boxShadow = '0 0 0 3px rgba(40, 167, 69, 0.1)';
                } else {
                    passwordField.style.borderColor = 'rgba(255, 255, 255, 0.2)';
                    passwordField.style.boxShadow = 'none';
                }
                
                // Verificar confirmação de senha
                if (confirm.length > 0) {
                    if (password === confirm) {
                        confirmField.style.borderColor = '#28a745';
                        confirmField.style.boxShadow = '0 0 0 3px rgba(40, 167, 69, 0.1)';
                    } else {
                        confirmField.style.borderColor = '#dc3545';
                        confirmField.style.boxShadow = '0 0 0 3px rgba(220, 53, 69, 0.1)';
                    }
                }
            }
            
            passwordField.addEventListener('input', validatePasswords);
            confirmField.addEventListener('input', validatePasswords);
        });

        function togglePassword(fieldId, icon) {
            const field = document.getElementById(fieldId);
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
