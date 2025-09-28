<?php
/**
 * Teste de Conexão com Banco de Dados
 */

echo "<h1>🔧 Teste de Conectividade</h1>";

// Teste de conexão com classe Database
require_once '../../config/database.php';

try {
    echo "<h2>1. Testando conexão com banco...</h2>";
    $db = new Database();
    echo "✅ <strong>Conexão estabelecida com sucesso!</strong><br><br>";
    
    echo "<h2>2. Testando consultas básicas...</h2>";
    
    // Teste 1: Contar usuários
    $users_result = $db->query("SELECT COUNT(*) as total FROM users");
    if ($users_result) {
        $total_users = $users_result->fetch_assoc()['total'];
        echo "✅ Total de usuários: <strong>$total_users</strong><br>";
    }
    
    // Teste 2: Contar comentários
    $comments_result = $db->query("SELECT COUNT(*) as total FROM comments");
    if ($comments_result) {
        $total_comments = $comments_result->fetch_assoc()['total'];
        echo "✅ Total de comentários: <strong>$total_comments</strong><br>";
    }
    
    // Teste 3: Listar alguns usuários
    echo "<br><h2>3. Usuários de teste disponíveis:</h2>";
    $users_list = $db->query("SELECT username, password, role FROM users LIMIT 5");
    if ($users_list && $users_list->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Usuário</th><th>Senha</th><th>Papel</th></tr>";
        while($user = $users_list->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['username']) . "</td>";
            echo "<td>" . htmlspecialchars($user['password']) . "</td>";
            echo "<td>" . htmlspecialchars($user['role']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<br>";
    }
    
    echo "<h2>4. Links para teste:</h2>";
    echo "<ul>";
    echo "<li><a href='login.php'>🔐 Página de Login</a></li>";
    echo "<li><a href='register.php'>📝 Página de Cadastro</a></li>";
    echo "<li><a href='dashboard.php'>📊 Dashboard</a></li>";
    echo "<li><a href='index.php'>🏠 Página Principal</a></li>";
    echo "<li><a href='../../admin.php?admin=true'>⚙️ Painel Admin</a></li>";
    echo "</ul>";
    
    echo "<h2>✅ Sistema funcionando corretamente!</h2>";
    echo "<p style='color: green;'><strong>Todas as páginas PHP devem estar funcionais agora.</strong></p>";
    
} catch (Exception $e) {
    echo "❌ <strong>Erro de conexão:</strong> " . $e->getMessage() . "<br>";
    echo "<p style='color: red;'>Verifique se o MySQL está rodando e se o banco 'vulnerable_db' foi criado.</p>";
    echo "<p>Para criar o banco manualmente:</p>";
    echo "<ol>";
    echo "<li>Acesse: <a href='http://localhost/phpmyadmin'>phpMyAdmin</a></li>";
    echo "<li>Crie um banco chamado 'vulnerable_db'</li>";
    echo "<li>Execute o script SQL do arquivo database/create_database.sql</li>";
    echo "</ol>";
}
?>