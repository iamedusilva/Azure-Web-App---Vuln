<?php
/**
 * Página Principal do Diretório Public
 * ATENÇÃO: Este código é propositalmente vulnerável para fins educacionais
 */

// Incluir classe de conexão com banco
require_once '../../config/database.php';

session_start();
$db = new Database();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Vulnerável - Área Pública</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .nav-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 30px 0;
        }
        .nav-btn {
            display: block;
            text-align: center;
            padding: 15px;
            background-color: #337ab7;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .nav-btn:hover {
            background-color: #286090;
        }
        .nav-btn.register {
            background-color: #5cb85c;
        }
        .nav-btn.register:hover {
            background-color: #449d44;
        }
        .nav-btn.admin {
            background-color: #d9534f;
        }
        .nav-btn.admin:hover {
            background-color: #c9302c;
        }
        .vulnerability-info {
            background-color: #fcf8e3;
            border: 1px solid #faebcc;
            color: #8a6d3b;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #337ab7;
        }
        .recent-activity {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #337ab7;
            margin: 20px 0;
        }
        .activity-item {
            padding: 5px 0;
            border-bottom: 1px solid #eee;
            font-size: 0.9em;
        }
        .activity-item:last-child {
            border-bottom: none;
        }
        .user-info {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 0.9em;
            color: #666;
        }
        .danger {
            color: #d9534f;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌐 Sistema Web Vulnerável</h1>
        <p style="text-align: center; color: #666;">
            Área pública da aplicação - Versão PHP com integração ao banco de dados
        </p>

        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="user-info">
                <strong>✅ Você está logado como:</strong> 
                <?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?>
                (<?php echo htmlspecialchars($_SESSION['role']); ?>)
                <a href="dashboard.php" style="margin-left: 15px;">📊 Ir para Dashboard</a>
            </div>
        <?php endif; ?>

        <div class="vulnerability-info">
            <h3>⚠️ AVISO DE SEGURANÇA</h3>
            <p>Esta aplicação contém <strong>vulnerabilidades intencionais</strong> para fins educacionais:</p>
            <ul>
                <li>🎯 <strong>SQL Injection</strong> - Bypass de autenticação e extração de dados</li>
                <li>🚨 <strong>Cross-Site Scripting (XSS)</strong> - Execução de código malicioso</li>
                <li>🔓 <strong>Insecure Direct Object References (IDOR)</strong> - Acesso a dados de outros usuários</li>
                <li>💾 <strong>Sensitive Data Exposure</strong> - Senhas em texto plano</li>
                <li>🔐 <strong>Broken Authentication</strong> - Bypass de controles de acesso</li>
            </ul>
        </div>

        <div class="nav-buttons">
            <a href="login.php" class="nav-btn">
                🔐 <strong>Login</strong><br>
                <small>Teste SQL Injection</small>
            </a>
            
            <a href="register.php" class="nav-btn register">
                📝 <strong>Cadastro</strong><br>
                <small>Registre-se no sistema</small>
            </a>
            
            <a href="dashboard.php" class="nav-btn">
                📊 <strong>Dashboard</strong><br>
                <small>Painel do usuário</small>
            </a>
            
            <a href="../../admin.php?admin=true" class="nav-btn admin">
                ⚙️ <strong>Admin Panel</strong><br>
                <small>Área administrativa</small>
            </a>
            
            <a href="../../index.php" class="nav-btn">
                🏠 <strong>Página Principal</strong><br>
                <small>Versão original</small>
            </a>
        </div>

        <!-- Estatísticas do Sistema -->
        <h3>📊 Estatísticas do Sistema</h3>
        <div class="stats-grid">
            <?php
            try {
                $total_users = $db->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
                $total_comments = $db->query("SELECT COUNT(*) as total FROM comments")->fetch_assoc()['total'];
                $total_login_attempts = $db->query("SELECT COUNT(*) as total FROM login_logs")->fetch_assoc()['total'];
                $failed_logins = $db->query("SELECT COUNT(*) as total FROM login_logs WHERE success = FALSE")->fetch_assoc()['total'];
                $recent_users = $db->query("SELECT COUNT(*) as total FROM users WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetch_assoc()['total'];
            ?>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_users; ?></div>
                    <div>Usuários Registrados</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_comments; ?></div>
                    <div>Comentários</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number danger"><?php echo $failed_logins; ?></div>
                    <div>Logins Falhados</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number"><?php echo $recent_users; ?></div>
                    <div>Novos (7 dias)</div>
                </div>
            <?php
            } catch (Exception $e) {
                echo '<div class="stat-card"><div class="danger">Erro ao carregar stats</div></div>';
            }
            ?>
        </div>

        <!-- Atividade Recente -->
        <div class="recent-activity">
            <h4>📈 Atividade Recente (DADOS SENSÍVEIS EXPOSTOS)</h4>
            <?php
            try {
                // VULNERÁVEL - expõe informações sensíveis
                $recent_logs = $db->query("SELECT username, password_attempted, ip_address, success, created_at 
                                         FROM login_logs 
                                         ORDER BY created_at DESC 
                                         LIMIT 8");
                
                if ($recent_logs && $recent_logs->num_rows > 0):
                    while ($log = $recent_logs->fetch_assoc()): ?>
                        <div class="activity-item">
                            <strong><?php echo $log['success'] ? '✅' : '❌'; ?></strong>
                            Login: <strong><?php echo htmlspecialchars($log['username']); ?></strong>
                            | Senha tentada: <span class="danger"><?php echo htmlspecialchars($log['password_attempted']); ?></span>
                            | IP: <?php echo htmlspecialchars($log['ip_address']); ?>
                            | <?php echo $log['created_at']; ?>
                        </div>
                    <?php endwhile;
                else: ?>
                    <div class="activity-item">Nenhuma atividade recente encontrada.</div>
                <?php endif;
            } catch (Exception $e) {
                echo '<div class="activity-item danger">Erro ao carregar atividade: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
            ?>
        </div>

        <!-- Usuários Recentemente Cadastrados -->
        <div class="recent-activity">
            <h4>👥 Usuários Recentes (VAZAMENTO DE DADOS)</h4>
            <?php
            try {
                // MUITO VULNERÁVEL - expõe senhas em texto plano
                $recent_users_query = $db->query("SELECT username, password, email, full_name, role, created_at 
                                                 FROM users 
                                                 ORDER BY created_at DESC 
                                                 LIMIT 5");
                
                if ($recent_users_query && $recent_users_query->num_rows > 0):
                    while ($user = $recent_users_query->fetch_assoc()): ?>
                        <div class="activity-item">
                            <strong>👤 <?php echo htmlspecialchars($user['username']); ?></strong>
                            (<?php echo htmlspecialchars($user['full_name']); ?>)
                            | Email: <?php echo htmlspecialchars($user['email']); ?>
                            | <span class="danger">Senha: <?php echo htmlspecialchars($user['password']); ?></span>
                            | Papel: <?php echo htmlspecialchars($user['role']); ?>
                            | Criado: <?php echo $user['created_at']; ?>
                        </div>
                    <?php endwhile;
                else: ?>
                    <div class="activity-item">Nenhum usuário encontrado.</div>
                <?php endif;
            } catch (Exception $e) {
                echo '<div class="activity-item danger">Erro ao carregar usuários: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
            ?>
        </div>

        <!-- Configurações Expostas -->
        <div class="recent-activity" style="border-left-color: #d9534f;">
            <h4>⚙️ Configurações do Sistema (CRÍTICO)</h4>
            <?php
            try {
                $configs = $db->query("SELECT config_key, config_value, description FROM config LIMIT 5");
                
                if ($configs && $configs->num_rows > 0):
                    while ($config = $configs->fetch_assoc()): ?>
                        <div class="activity-item">
                            <strong><?php echo htmlspecialchars($config['config_key']); ?>:</strong>
                            <span class="danger"><?php echo htmlspecialchars($config['config_value']); ?></span>
                            <small>(<?php echo htmlspecialchars($config['description']); ?>)</small>
                        </div>
                    <?php endwhile;
                endif;
            } catch (Exception $e) {
                echo '<div class="activity-item danger">Erro ao carregar configurações</div>';
            }
            ?>
        </div>

        <!-- Links de Teste -->
        <div style="background-color: #e9ecef; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h4>🔍 Links para Teste de Vulnerabilidades</h4>
            <p style="font-size: 0.9em;">Clique nos links abaixo para testar diferentes vulnerabilidades:</p>
            <ul style="font-size: 0.9em;">
                <li><a href="dashboard.php?user_id=1">Ver dados do Admin (IDOR)</a></li>
                <li><a href="dashboard.php?user_id=2">Ver dados do User1 (IDOR)</a></li>
                <li><a href="../../admin.php?admin=true">Bypass de autenticação admin</a></li>
                <li><a href="login.php">Teste SQL Injection no login</a></li>
                <li><a href="register.php">Cadastro com dados maliciosos</a></li>
            </ul>
        </div>

        <div class="footer">
            <p><strong>⚠️ IMPORTANTE:</strong> Esta aplicação é <span class="danger">PROPOSITALMENTE VULNERÁVEL</span></p>
            <p>Use apenas para fins educacionais em ambiente controlado</p>
            <p>Criado em: <?php echo date('d/m/Y H:i:s'); ?> | 
               IP do visitante: <?php echo $_SERVER['REMOTE_ADDR'] ?? 'desconhecido'; ?></p>
        </div>
    </div>
</body>
</html>