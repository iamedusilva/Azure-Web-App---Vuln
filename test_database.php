<?php
/**
 * Teste das conexões MySQL e Azure SQL Database
 */

require_once 'config/database.php';

echo "<h1>Teste de Conexões - Database Dual</h1>";

// ================================
// TESTE 1: CONEXÃO MYSQL
// ================================
echo "<h2>1. Testando Conexão MySQL</h2>";
try {
    $db_mysql = new Database("mysql");
    echo "✅ Conexão MySQL estabelecida com sucesso!<br>";
    echo "Tipo de conexão: " . $db_mysql->getConnectionType() . "<br>";
} catch (Exception $e) {
    echo "❌ Erro na conexão MySQL: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// ================================
// TESTE 2: CONEXÃO AZURE SQL
// ================================
echo "<h2>2. Testando Conexão Azure SQL Database</h2>";
try {
    $db_azure = new Database("azure");
    echo "✅ Conexão Azure SQL estabelecida com sucesso!<br>";
    echo "Tipo de conexão: " . $db_azure->getConnectionType() . "<br>";
} catch (Exception $e) {
    echo "❌ Erro na conexão Azure SQL: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// ================================
// TESTE 3: DETECÇÃO AUTOMÁTICA
// ================================
echo "<h2>3. Testando Detecção Automática</h2>";
try {
    $db_auto = new Database();
    echo "✅ Conexão automática estabelecida!<br>";
    echo "Tipo detectado: " . $db_auto->getConnectionType() . "<br>";
} catch (Exception $e) {
    echo "❌ Erro na detecção automática: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// ================================
// TESTE 4: QUERY SIMPLES MYSQL
// ================================
if (isset($db_mysql)) {
    echo "<h2>4. Teste de Query MySQL</h2>";
    try {
        $result = $db_mysql->query("SELECT 1 as test");
        if ($result) {
            echo "✅ Query MySQL executada com sucesso!<br>";
            if (is_object($result) && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo "Resultado: " . $row['test'] . "<br>";
            }
        } else {
            echo "❌ Falha na execução da query MySQL<br>";
        }
    } catch (Exception $e) {
        echo "❌ Erro na query MySQL: " . $e->getMessage() . "<br>";
    }
}

echo "<hr>";

// ================================
// TESTE 5: QUERY SIMPLES AZURE
// ================================
if (isset($db_azure)) {
    echo "<h2>5. Teste de Query Azure SQL</h2>";
    try {
        $result = $db_azure->query("SELECT 1 as test");
        if ($result) {
            echo "✅ Query Azure SQL executada com sucesso!<br>";
            if (is_array($result) && count($result) > 0) {
                echo "Resultado: " . $result[0]['test'] . "<br>";
            }
        } else {
            echo "❌ Falha na execução da query Azure SQL<br>";
        }
    } catch (Exception $e) {
        echo "❌ Erro na query Azure SQL: " . $e->getMessage() . "<br>";
    }
}

echo "<hr>";

// ================================
// INFORMAÇÕES DO SISTEMA
// ================================
echo "<h2>6. Informações do Sistema</h2>";
echo "Versão PHP: " . phpversion() . "<br>";
echo "Extensão MySQLi: " . (extension_loaded('mysqli') ? '✅ Carregada' : '❌ Não carregada') . "<br>";
echo "Extensão PDO: " . (extension_loaded('pdo') ? '✅ Carregada' : '❌ Não carregada') . "<br>";
echo "Extensão PDO_MySQL: " . (extension_loaded('pdo_mysql') ? '✅ Carregada' : '❌ Não carregada') . "<br>";
echo "Extensão PDO_SQLSRV: " . (extension_loaded('pdo_sqlsrv') ? '✅ Carregada' : '❌ Não carregada') . "<br>";
echo "Extensão SQLSRV: " . (extension_loaded('sqlsrv') ? '✅ Carregada' : '❌ Não carregada') . "<br>";

?>