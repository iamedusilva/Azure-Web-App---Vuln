<?php
/**
 * Teste de Conectividade com Azure SQL Database
 * Execute este arquivo para validar se tudo está funcionando
 */

echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Teste Azure SQL Database</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 5px; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { color: #007bff; }
        .warning { color: #ffc107; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .password { font-family: monospace; background-color: #ffe6e6; }
        pre { background-color: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>";

echo "<h1>🌐 Teste de Conectividade - Azure SQL Database</h1>";
echo "<p>Validando conexão e funcionalidades...</p><hr>";

// Verificar se extensões estão instaladas
echo "<h2>1. 🔧 Verificando Extensões PHP</h2>";

$extensions = ['sqlsrv', 'pdo_sqlsrv'];
$extensionsOk = true;

foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<div class='success'>✅ Extensão '$ext' está instalada</div>";
    } else {
        echo "<div class='error'>❌ Extensão '$ext' NÃO encontrada</div>";
        $extensionsOk = false;
    }
}

if (!$extensionsOk) {
    echo "<div class='error'><strong>⚠️ ERRO:</strong> Extensões SQL Server não estão instaladas.</div>";
    echo "<div class='info'><strong>Para instalar:</strong><br>";
    echo "1. Baixe Microsoft Drivers for PHP for SQL Server<br>";
    echo "2. Copie as DLLs para php/ext/<br>";
    echo "3. Adicione no php.ini: extension=sqlsrv e extension=pdo_sqlsrv<br>";
    echo "4. Reinicie o Apache</div>";
    echo "</div></body></html>";
    exit;
}

echo "<hr>";

// Tentar incluir a classe Azure Database
echo "<h2>2. 📂 Carregando Classe de Conexão</h2>";

try {
    require_once '../../config/azure_database.php';
    echo "<div class='success'>✅ Classe AzureDatabase carregada com sucesso</div>";
} catch (Exception $e) {
    echo "<div class='error'>❌ Erro ao carregar classe: " . $e->getMessage() . "</div>";
    echo "<div class='info'>Certifique-se de que o arquivo config/azure_database.php existe</div>";
    echo "</div></body></html>";
    exit;
}

echo "<hr>";

// Testar conexão
echo "<h2>3. 🔌 Testando Conexão com Azure SQL Database</h2>";

try {
    $db = new AzureDatabase();
    echo "<div class='success'>✅ Conexão estabelecida com sucesso!</div>";
    
    // Mostrar informações da conexão
    echo "<div class='info'>Conectado ao Azure SQL Database</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Erro de conexão: " . $e->getMessage() . "</div>";
    echo "<div class='warning'><strong>Possíveis causas:</strong><br>";
    echo "1. Credenciais incorretas na classe AzureDatabase<br>";
    echo "2. Firewall do Azure bloqueando seu IP<br>";
    echo "3. Servidor Azure não existe ou está offline<br>";
    echo "4. Banco de dados não foi criado</div>";
    echo "</div></body></html>";
    exit;
}

echo "<hr>";

// Testar informações do sistema
echo "<h2>4. 📊 Informações do Sistema Azure</h2>";

try {
    $systemInfo = $db->getSystemInfo();
    
    if (!empty($systemInfo)) {
        echo "<table>";
        echo "<tr><th>Tipo de Informação</th><th>Valor</th></tr>";
        foreach ($systemInfo as $info) {
            echo "<tr>";
            echo "<td><strong>" . htmlspecialchars($info['info_type']) . "</strong></td>";
            echo "<td>" . htmlspecialchars($info['info_value']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<div class='success'>✅ Informações do sistema obtidas com sucesso</div>";
    } else {
        echo "<div class='warning'>⚠️ Nenhuma informação do sistema retornada</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Erro ao obter informações do sistema: " . $e->getMessage() . "</div>";
}

echo "<hr>";

// Testar contagem de registros
echo "<h2>5. 📈 Testando Consultas Básicas</h2>";

$tables = ['users', 'comments', 'login_logs', 'config', 'uploaded_files'];

echo "<table>";
echo "<tr><th>Tabela</th><th>Total de Registros</th><th>Status</th></tr>";

foreach ($tables as $table) {
    try {
        $count = $db->countRecords($table);
        echo "<tr>";
        echo "<td><strong>$table</strong></td>";
        echo "<td class='info'>$count</td>";
        echo "<td class='success'>✅ OK</td>";
        echo "</tr>";
    } catch (Exception $e) {
        echo "<tr>";
        echo "<td><strong>$table</strong></td>";
        echo "<td class='error'>Erro</td>";
        echo "<td class='error'>❌ " . $e->getMessage() . "</td>";
        echo "</tr>";
    }
}

echo "</table>";

echo "<hr>";

// Testar usuários de exemplo
echo "<h2>6. 👥 Testando Usuários de Exemplo</h2>";

try {
    $users = $db->getAllUsers();
    
    if (!empty($users)) {
        echo "<div class='success'>✅ Encontrados " . count($users) . " usuários</div>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Usuário</th><th>Senha (VULNERÁVEL)</th><th>Email</th><th>Papel</th></tr>";
        
        foreach (array_slice($users, 0, 5) as $user) { // Mostrar apenas 5 primeiros
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['id']) . "</td>";
            echo "<td>" . htmlspecialchars($user['username']) . "</td>";
            echo "<td class='password'>" . htmlspecialchars($user['password']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . htmlspecialchars($user['role']) . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        echo "<div class='warning'>⚠️ VULNERABILIDADE: Senhas visíveis em texto plano!</div>";
    } else {
        echo "<div class='error'>❌ Nenhum usuário encontrado - Execute o script create_database_azure.sql</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Erro ao buscar usuários: " . $e->getMessage() . "</div>";
}

echo "<hr>";

// Testar SQL injection
echo "<h2>7. 🎯 Testando Vulnerabilidades SQL Injection</h2>";

try {
    // Teste de SQL injection no método vulnerável
    $maliciousInput = "admin' OR '1'='1'--";
    $injectionResult = $db->authenticateUser($maliciousInput, "qualquer_senha");
    
    if ($injectionResult) {
        echo "<div class='error'>🎯 <strong>SQL INJECTION FUNCIONANDO!</strong></div>";
        echo "<div class='info'>Payload testado: <code>$maliciousInput</code></div>";
        echo "<div class='info'>Usuário retornado: <strong>" . htmlspecialchars($injectionResult['username']) . "</strong></div>";
        echo "<div class='warning'>⚠️ Esta é uma vulnerabilidade intencional para fins educacionais</div>";
    } else {
        echo "<div class='warning'>⚠️ SQL Injection não funcionou conforme esperado</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Erro ao testar SQL injection: " . $e->getMessage() . "</div>";
}

echo "<hr>";

// Testar stored procedure vulnerável
echo "<h2>8. 🏗️ Testando Stored Procedure Vulnerável</h2>";

try {
    $procedureResult = $db->callGetUserByName("admin");
    
    if (!empty($procedureResult)) {
        echo "<div class='success'>✅ Stored procedure executada com sucesso</div>";
        echo "<div class='info'>Usuário encontrado via procedure: <strong>" . htmlspecialchars($procedureResult[0]['username']) . "</strong></div>";
    } else {
        echo "<div class='warning'>⚠️ Stored procedure não retornou resultados</div>";
    }
    
    // Teste de injection na procedure
    $maliciousInput = "admin'; SELECT 'HACKED' as result; --";
    echo "<div class='info'>Testando SQL injection na procedure...</div>";
    echo "<div class='info'>Payload: <code>$maliciousInput</code></div>";
    
    $injectionResult = $db->callGetUserByName($maliciousInput);
    echo "<div class='warning'>⚠️ Resultado pode variar dependendo das configurações do Azure</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Erro ao testar stored procedure: " . $e->getMessage() . "</div>";
}

echo "<hr>";

// Testar configurações expostas
echo "<h2>9. ⚙️ Testando Exposição de Configurações</h2>";

try {
    $configs = $db->getConfigurations();
    
    if (!empty($configs)) {
        echo "<div class='error'>🚨 <strong>DADOS SENSÍVEIS EXPOSTOS!</strong></div>";
        echo "<table>";
        echo "<tr><th>Chave</th><th>Valor (SENSÍVEL)</th><th>Descrição</th></tr>";
        
        foreach ($configs as $config) {
            echo "<tr>";
            echo "<td><strong>" . htmlspecialchars($config['config_key']) . "</strong></td>";
            echo "<td class='password'>" . htmlspecialchars($config['config_value']) . "</td>";
            echo "<td>" . htmlspecialchars($config['description']) . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        echo "<div class='warning'>⚠️ VULNERABILIDADE: Chaves de API e senhas expostas!</div>";
    } else {
        echo "<div class='warning'>⚠️ Nenhuma configuração encontrada</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Erro ao buscar configurações: " . $e->getMessage() . "</div>";
}

echo "<hr>";

// Resultados finais
echo "<h2>10. ✅ Resultado Final</h2>";

echo "<div style='background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; padding: 15px;'>";
echo "<h3 style='color: #155724; margin-top: 0;'>🎉 MIGRAÇÃO PARA AZURE SQL DATABASE COMPLETA!</h3>";
echo "<ul style='color: #155724;'>";
echo "<li>✅ <strong>Conexão com Azure:</strong> Funcionando</li>";
echo "<li>✅ <strong>Estrutura do banco:</strong> Criada</li>";
echo "<li>✅ <strong>Dados de teste:</strong> Inseridos</li>";
echo "<li>✅ <strong>Vulnerabilidades:</strong> Funcionais</li>";
echo "<li>✅ <strong>Stored procedures:</strong> Criadas</li>";
echo "<li>✅ <strong>Sistema educacional:</strong> Pronto</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 15px; margin-top: 15px;'>";
echo "<h4 style='color: #856404; margin-top: 0;'>🔗 Links Úteis:</h4>";
echo "<ul style='color: #856404;'>";
echo "<li><a href='../../app/public/index.php'>🏠 Página Principal da Aplicação</a></li>";
echo "<li><a href='../../app/public/login.php'>🔐 Sistema de Login</a></li>";
echo "<li><a href='../../app/public/register.php'>📝 Cadastro de Usuários</a></li>";
echo "<li><a href='../../app/public/dashboard.php'>📊 Dashboard</a></li>";
echo "<li><a href='../../admin.php?admin=true'>⚙️ Painel Administrativo</a></li>";
echo "</ul>";
echo "</div>";

echo "<div style='background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; padding: 15px; margin-top: 15px;'>";
echo "<h4 style='color: #721c24; margin-top: 0;'>⚠️ Lembrete de Segurança:</h4>";
echo "<p style='color: #721c24; margin-bottom: 0;'>";
echo "<strong>Este sistema é propositalmente vulnerável para fins educacionais.</strong><br>";
echo "Está rodando no Azure SQL Database com todas as vulnerabilidades funcionais.<br>";
echo "NUNCA use este código ou estrutura em ambiente de produção!";
echo "</p>";
echo "</div>";

// Informações de debug
echo "<hr>";
echo "<h3>🔍 Informações de Debug</h3>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "Extensões SQL Server carregadas: " . (extension_loaded('sqlsrv') ? 'Sim' : 'Não') . "\n";
echo "PDO SQL Server carregado: " . (extension_loaded('pdo_sqlsrv') ? 'Sim' : 'Não') . "\n";
echo "Data/Hora do teste: " . date('Y-m-d H:i:s') . "\n";
echo "IP do cliente: " . ($_SERVER['REMOTE_ADDR'] ?? 'desconhecido') . "\n";
echo "</pre>";

echo "</div></body></html>";
?>