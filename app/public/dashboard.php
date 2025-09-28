<?php
/**
 * Dashboard Vulnerável
 * ATENÇÃO: Este código é propositalmente vulnerável para fins educacionais
 */

// Incluir classe de conexão com banco
require_once '../../config/database.php';

// Verificação de sessão (vulnerável)
session_start();

$user_logged_in = false;
$current_user = null;

// Verificação muito fraca de autenticação
if (isset($_SESSION['user_id']) || isset($_GET['user_id']) || isset($_POST['user_id'])) {
    $user_logged_in = true;
    $user_id = $_SESSION['user_id'] ?? $_GET['user_id'] ?? $_POST['user_id'];
    
    $db = new Database();
    // VULNERÁVEL - query direta sem sanitização
    $current_user = $db->getUserById($user_id);
    
    if (!$current_user) {
        // Se não encontrou por ID, usa dados da sessão ou valores padrão
        $current_user = [
            'id' => $user_id,
            'username' => $_SESSION['username'] ?? 'unknown',
            'full_name' => $_SESSION['full_name'] ?? 'Usuário Desconhecido',
            'role' => $_SESSION['role'] ?? 'user',
            'email' => 'unknown@email.com'
        ];
    }
}

// Processar logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Atualizar perfil (vulnerável)
$profile_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $new_name = $_POST['new_name'] ?? '';
    $new_email = $_POST['new_email'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    
    if (!empty($new_name) || !empty($new_email) || !empty($new_password)) {
        $db = new Database();
        
        // VULNERÁVEL - update sem sanitização
        $updates = [];
        if (!empty($new_name)) $updates[] = "full_name = '$new_name'";
        if (!empty($new_email)) $updates[] = "email = '$new_email'";
        if (!empty($new_password)) $updates[] = "password = '$new_password'";
        
        if (!empty($updates)) {
            $update_query = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = " . $current_user['id'];
            
            if ($db->query($update_query)) {
                $profile_message = "Perfil atualizado com sucesso!";
                // Recarregar dados do usuário
                $current_user = $db->getUserById($current_user['id']);
            } else {
                $profile_message = "Erro ao atualizar perfil.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema Vulnerável</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .header {
            background-color: #337ab7;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card h3 {
            margin-top: 0;
            color: #333;
        }
        .vulnerability-warning {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            text-align: center;
        }
        .stat-item {
            padding: 10px;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #337ab7;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            background-color: #5cb85c;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        .btn:hover {
            background-color: #449d44;
        }
        .btn-danger {
            background-color: #d9534f;
        }
        .btn-danger:hover {
            background-color: #c9302c;
        }
        .btn-info {
            background-color: #5bc0de;
        }
        .success {
            color: #3c763d;
            background-color: #dff0d8;
            border: 1px solid #d6e9c6;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .password-field {
            font-family: monospace;
            background-color: #ffe6e6;
            color: #d9534f;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏠 Dashboard do Sistema</h1>
        <div class="user-info">
            <?php if ($user_logged_in): ?>
                <span>👤 Olá, <strong><?php echo htmlspecialchars($current_user['full_name']); ?></strong></span>
                <span>🏷️ (<?php echo htmlspecialchars($current_user['role']); ?>)</span>
                <a href="?logout=1" style="color: white; text-decoration: none;">🚪 Sair</a>
            <?php else: ?>
                <a href="login.php" style="color: white; text-decoration: none;">🔐 Login</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <?php if (!$user_logged_in): ?>
            <!-- Área não autenticada -->
            <div class="vulnerability-warning">
                <h3>⚠️ Acesso Não Autenticado Detectado!</h3>
                <p>Você está vendo esta página sem estar logado! Isso é uma vulnerabilidade grave.</p>
                <p>💡 <strong>Tente acessar:</strong></p>
                <ul>
                    <li><code>dashboard.php?user_id=1</code> - Ver dados do admin</li>
                    <li><code>dashboard.php?user_id=2</code> - Ver dados de outro usuário</li>
                    <li>Qualquer ID de usuário existente</li>
                </ul>
                <p><a href="login.php" class="btn">🔐 Fazer Login Agora</a></p>
            </div>
        <?php endif; ?>

        <?php if ($user_logged_in): ?>
            <!-- Dashboard autenticado -->
            <div class="vulnerability-warning">
                <strong>🎯 Vulnerabilidades Ativas:</strong>
                • Bypass de autenticação via GET/POST • 
                • IDOR (Insecure Direct Object Reference) • 
                • Perfil editável sem validação • 
                • Exposição de dados sensíveis
            </div>

            <div class="dashboard-grid">
                <!-- Informações do Usuário -->
                <div class="card">
                    <h3>👤 Seus Dados</h3>
                    <table>
                        <tr><td><strong>ID:</strong></td><td><?php echo htmlspecialchars($current_user['id']); ?></td></tr>
                        <tr><td><strong>Usuário:</strong></td><td><?php echo htmlspecialchars($current_user['username']); ?></td></tr>
                        <tr><td><strong>Nome:</strong></td><td><?php echo htmlspecialchars($current_user['full_name']); ?></td></tr>
                        <tr><td><strong>Email:</strong></td><td><?php echo htmlspecialchars($current_user['email']); ?></td></tr>
                        <tr><td><strong>Papel:</strong></td><td><?php echo htmlspecialchars($current_user['role']); ?></td></tr>
                        <tr><td><strong>Senha:</strong></td><td class="password-field"><?php echo htmlspecialchars($current_user['password'] ?? 'N/A'); ?></td></tr>
                    </table>
                    
                    <p style="font-size: 12px; color: #d9534f; margin-top: 10px;">
                        ⚠️ <strong>GRAVE:</strong> Sua senha está visível em texto plano!
                    </p>
                </div>

                <!-- Estatísticas do Sistema -->
                <div class="card">
                    <h3>📊 Estatísticas do Sistema</h3>
                    <?php
                    $db = new Database();
                    $total_users = $db->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
                    $total_comments = $db->query("SELECT COUNT(*) as total FROM comments")->fetch_assoc()['total'];
                    $total_logs = $db->query("SELECT COUNT(*) as total FROM login_logs")->fetch_assoc()['total'];
                    $failed_logins = $db->query("SELECT COUNT(*) as total FROM login_logs WHERE success = FALSE")->fetch_assoc()['total'];
                    ?>
                    <div class="stats">
                        <div class="stat-item">
                            <div class="stat-number"><?php echo $total_users; ?></div>
                            <div>Usuários</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo $total_comments; ?></div>
                            <div>Comentários</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo $failed_logins; ?></div>
                            <div>Login Falhados</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Editar Perfil -->
            <div class="card">
                <h3>✏️ Editar Perfil (VULNERÁVEL)</h3>
                <?php if ($profile_message): ?>
                    <div class="success"><?php echo htmlspecialchars($profile_message); ?></div>
                <?php endif; ?>
                
                <form method="post">
                    <div class="form-group">
                        <label for="new_name">Nome Completo:</label>
                        <input type="text" id="new_name" name="new_name" placeholder="Deixe vazio para não alterar">
                        <small style="color: #666;">🎯 Teste XSS: &lt;script&gt;alert('XSS')&lt;/script&gt;</small>
                    </div>
                    <div class="form-group">
                        <label for="new_email">Email:</label>
                        <input type="email" id="new_email" name="new_email" placeholder="Deixe vazio para não alterar">
                        <small style="color: #666;">💉 Teste SQLi: test@test.com', role='admin' WHERE id=1; --</small>
                    </div>
                    <div class="form-group">
                        <label for="new_password">Nova Senha:</label>
                        <input type="password" id="new_password" name="new_password" placeholder="Deixe vazio para não alterar">
                        <small style="color: #d9534f;">⚠️ Será salva em texto plano!</small>
                    </div>
                    <button type="submit" name="update_profile" class="btn">💾 Salvar Alterações</button>
                </form>
            </div>

            <!-- Links de Teste -->
            <div class="card">
                <h3>🔗 Explorar Vulnerabilidades</h3>
                <p>Teste estas URLs para explorar vulnerabilidades IDOR:</p>
                <div style="margin: 10px 0;">
                    <?php for ($i = 1; $i <= 7; $i++): ?>
                        <a href="?user_id=<?php echo $i; ?>" class="btn btn-info" style="margin: 2px;">Ver User <?php echo $i; ?></a>
                    <?php endfor; ?>
                </div>
                
                <p style="margin-top: 20px;">Links úteis:</p>
                <a href="../../admin.php?admin=true" class="btn">⚙️ Painel Admin</a>
                <a href="../../index.php" class="btn">🏠 Página Principal</a>
                <a href="login.php" class="btn">🔐 Página de Login</a>
                <a href="register.php" class="btn">📝 Cadastro</a>
            </div>

            <!-- Comentários Recentes (com XSS) -->
            <div class="card">
                <h3>💬 Comentários Recentes (XSS Ativo)</h3>
                <?php
                $comments = $db->getComments();
                $recent_comments = array_slice($comments, 0, 5);
                
                if (!empty($recent_comments)):
                    foreach ($recent_comments as $comment): ?>
                        <div style="border-bottom: 1px solid #eee; padding: 10px 0;">
                            <strong><?php echo $comment['name']; // VULNERÁVEL - sem escape ?></strong> - 
                            <small><?php echo $comment['created_at']; ?></small><br>
                            <div><?php echo $comment['comment']; // VULNERÁVEL A XSS ?></div>
                        </div>
                    <?php endforeach;
                else: ?>
                    <p>Nenhum comentário encontrado.</p>
                <?php endif; ?>
                
                <p style="margin-top: 15px;">
                    <a href="../../index.php#comments" class="btn">Ver Todos os Comentários</a>
                </p>
            </div>

        <?php endif; ?>
    </div>

    <script>
        // JavaScript para demonstrar vulnerabilidades
        document.addEventListener('DOMContentLoaded', function() {
            // Mostrar alerta se estiver acessando dados de outro usuário via IDOR
            const urlParams = new URLSearchParams(window.location.search);
            const userIdParam = urlParams.get('user_id');
            
            if (userIdParam) {
                const currentUserId = <?php echo json_encode($current_user['id'] ?? 'null'); ?>;
                if (userIdParam != currentUserId) {
                    setTimeout(() => {
                        alert('🎯 VULNERABILIDADE IDOR EXPLORADA!\n\n' +
                              'Você está vendo dados do usuário ' + userIdParam + 
                              ' sem ter permissão!\n\n' +
                              'Esta é uma falha grave de segurança.');
                    }, 1000);
                }
            }
            
            // Demonstrar que dados sensíveis estão expostos no HTML
            console.log('🚨 DADOS SENSÍVEIS EXPOSTOS NO JAVASCRIPT:');
            console.log('Usuário atual:', <?php echo json_encode($current_user); ?>);
        });
    </script>
</body>
</html>