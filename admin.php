<?php
/**
 * Página de Administração Vulnerável
 * ATENÇÃO: Este código é propositalmente vulnerável para fins educacionais
 */

// Incluir classe de conexão
require_once 'config/database.php';

// Função vulnerável para verificar se é admin (pode ser bypassada)
function isAdmin() {
    // VULNERÁVEL - verifica apenas se existe um parâmetro GET
    return isset($_GET['admin']) && $_GET['admin'] == 'true';
}

// VULNERÁVEL - sem autenticação adequada
if (!isAdmin()) {
    // Mas permite bypass com parâmetro GET
    echo "<p style='color: red;'>Acesso negado! <a href='?admin=true'>Clique aqui se você é admin</a></p>";
}

$db = new Database();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo Vulnerável</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 5px; }
        .section { margin-bottom: 30px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .danger { background-color: #ffe6e6; border-color: #ff9999; }
        .warning { background-color: #fff3cd; border-color: #ffc107; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .password { font-family: monospace; background-color: #ffe6e6; }
        input[type="text"], textarea { width: 100%; padding: 5px; }
        button { background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        .danger-btn { background-color: #dc3545; }
        .small { font-size: 0.8em; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔒 Painel Administrativo (Vulnerável)</h1>
        <p class="warning"><strong>⚠️ AVISO:</strong> Esta página demonstra vulnerabilidades sérias de segurança!</p>
        
        <?php if (isAdmin()): ?>
        
        <!-- Seção de Usuários -->
        <div class="section danger">
            <h2>👥 Usuários do Sistema</h2>
            <p><strong>VULNERABILIDADE:</strong> Senhas expostas em texto plano!</p>
            
            <table>
                <tr>
                    <th>ID</th>
                    <th>Usuário</th>
                    <th>Senha (EXPOSTA!)</th>
                    <th>Email</th>
                    <th>Nome Completo</th>
                    <th>Papel</th>
                    <th>Criado em</th>
                </tr>
                <?php
                $users = $db->getAllUsers();
                foreach($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= $user['username'] ?></td>
                    <td class="password"><?= $user['password'] ?></td>
                    <td><?= $user['email'] ?></td>
                    <td><?= $user['full_name'] ?></td>
                    <td><?= $user['role'] ?></td>
                    <td><?= $user['created_at'] ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <!-- Seção de Logs de Login -->
        <div class="section danger">
            <h2>📋 Logs de Login (PERIGOSO!)</h2>
            <p><strong>VULNERABILIDADE:</strong> Armazena senhas tentadas em texto plano!</p>
            
            <table>
                <tr>
                    <th>ID</th>
                    <th>Usuário</th>
                    <th>Senha Tentada (EXPOSTA!)</th>
                    <th>IP</th>
                    <th>User Agent</th>
                    <th>Sucesso</th>
                    <th>Data</th>
                </tr>
                <?php
                $logs = $db->query("SELECT * FROM login_logs ORDER BY created_at DESC LIMIT 20");
                while($log = $logs->fetch_assoc()): ?>
                <tr>
                    <td><?= $log['id'] ?></td>
                    <td><?= $log['username'] ?></td>
                    <td class="password"><?= $log['password_attempted'] ?></td>
                    <td><?= $log['ip_address'] ?></td>
                    <td class="small"><?= htmlspecialchars(substr($log['user_agent'], 0, 50)) ?>...</td>
                    <td><?= $log['success'] ? '✅' : '❌' ?></td>
                    <td><?= $log['created_at'] ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <!-- Seção de Configurações Sensíveis -->
        <div class="section danger">
            <h2>⚙️ Configurações do Sistema (CRÍTICO!)</h2>
            <p><strong>VULNERABILIDADE:</strong> Chaves de API e senhas expostas!</p>
            
            <table>
                <tr>
                    <th>Chave</th>
                    <th>Valor (SENSÍVEL!)</th>
                    <th>Descrição</th>
                </tr>
                <?php
                $configs = $db->query("SELECT * FROM config");
                while($config = $configs->fetch_assoc()): ?>
                <tr>
                    <td><?= $config['config_key'] ?></td>
                    <td class="password"><?= $config['config_value'] ?></td>
                    <td><?= $config['description'] ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <!-- Seção de Query Personalizada (SQL Injection) -->
        <div class="section danger">
            <h2>💉 Executar Query Personalizada (SQL INJECTION!)</h2>
            <p><strong>VULNERABILIDADE EXTREMA:</strong> Permite execução de qualquer SQL!</p>
            
            <?php
            if ($_POST['custom_query']) {
                $query = $_POST['custom_query'];
                echo "<h3>Resultado da Query:</h3>";
                echo "<p><code>" . htmlspecialchars($query) . "</code></p>";
                
                try {
                    $result = $db->executeCustomQuery($query);
                    
                    if ($result === true) {
                        echo "<p style='color: green;'>Query executada com sucesso!</p>";
                    } elseif ($result && $result->num_rows > 0) {
                        echo "<table><tr>";
                        $fields = $result->fetch_fields();
                        foreach($fields as $field) {
                            echo "<th>" . $field->name . "</th>";
                        }
                        echo "</tr>";
                        
                        $result->data_seek(0);
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            foreach($row as $value) {
                                echo "<td>" . htmlspecialchars($value) . "</td>";
                            }
                            echo "</tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p>Query executada, nenhum resultado retornado.</p>";
                    }
                } catch (Exception $e) {
                    echo "<p style='color: red;'>Erro: " . $e->getMessage() . "</p>";
                }
            }
            ?>
            
            <form method="post">
                <label>Query SQL:</label><br>
                <textarea name="custom_query" rows="4" placeholder="SELECT * FROM users; -- Ou qualquer outra query..."><?= $_POST['custom_query'] ?? '' ?></textarea><br>
                <button type="submit" class="danger-btn">⚠️ EXECUTAR (PERIGOSO!)</button>
            </form>
            
            <h4>Exemplos de Queries Perigosas:</h4>
            <ul>
                <li><code>SELECT * FROM users</code> - Listar todos os usuários</li>
                <li><code>SELECT * FROM config WHERE config_key LIKE '%key%'</code> - Buscar chaves</li>
                <li><code>SHOW TABLES</code> - Listar tabelas</li>
                <li><code>SELECT USER(), VERSION(), DATABASE()</code> - Info do sistema</li>
                <li><code>SELECT LOAD_FILE('/etc/passwd')</code> - Ler arquivos do sistema (se permitido)</li>
            </ul>
        </div>

        <!-- Seção de Sistema -->
        <div class="section warning">
            <h2>🖥️ Informações do Sistema</h2>
            <table>
                <tr><td><strong>PHP Version:</strong></td><td><?= phpversion() ?></td></tr>
                <tr><td><strong>Server Software:</strong></td><td><?= $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' ?></td></tr>
                <tr><td><strong>Document Root:</strong></td><td><?= $_SERVER['DOCUMENT_ROOT'] ?? 'N/A' ?></td></tr>
                <tr><td><strong>Current User:</strong></td><td><?= get_current_user() ?></td></tr>
                <tr><td><strong>Server Time:</strong></td><td><?= date('Y-m-d H:i:s') ?></td></tr>
                <tr><td><strong>User Agent:</strong></td><td><?= $_SERVER['HTTP_USER_AGENT'] ?? 'N/A' ?></td></tr>
                <tr><td><strong>Client IP:</strong></td><td><?= $_SERVER['REMOTE_ADDR'] ?? 'N/A' ?></td></tr>
            </table>
        </div>

        <!-- Seção de Comentários com XSS -->
        <div class="section danger">
            <h2>💬 Comentários Recentes (XSS Vulnerável)</h2>
            <div>
                <?php
                $comments = $db->getComments();
                foreach($comments as $comment): ?>
                <div style="border: 1px solid #ccc; padding: 10px; margin: 5px 0;">
                    <strong><?= $comment['name'] ?></strong> - <?= $comment['created_at'] ?><br>
                    <?= $comment['comment'] ?> <!-- VULNERÁVEL A XSS - não sanitizado! -->
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Links para outras vulnerabilidades -->
        <div class="section">
            <h2>🔗 Outras Páginas Vulneráveis</h2>
            <p><a href="index.php">← Voltar para página principal</a></p>
            <p><a href="?admin=false">Simular logout (bypass fácil)</a></p>
            <p><a href="app/public/dashboard.html">Dashboard HTML</a></p>
            <p><a href="app/public/login.html">Login HTML</a></p>
        </div>

        <?php else: ?>
        
        <div class="section">
            <h2>Acesso Negado</h2>
            <p>Você precisa ser administrador para acessar esta página.</p>
            <p><em>Dica: Observe a URL... 😉</em></p>
        </div>
        
        <?php endif; ?>
        
        <hr>
        <p class="small">
            <strong>⚠️ LEMBRETE DE SEGURANÇA:</strong><br>
            Esta página demonstra múltiplas vulnerabilidades graves:<br>
            • Exposição de senhas em texto plano<br>
            • SQL Injection via query personalizada<br>
            • XSS em comentários<br>
            • Bypass de autenticação trivial<br>
            • Information disclosure<br>
            • Logging inseguro<br><br>
            <em>NUNCA implemente código como este em produção!</em>
        </p>
    </div>
</body>
</html>