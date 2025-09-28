# 🌐 Migração para Azure SQL Database - Guia Completo

## 📋 Principais Diferenças MySQL → T-SQL

### 1. **Sintaxe e Tipos de Dados**

| MySQL | Azure SQL Database (T-SQL) |
|-------|----------------------------|
| `AUTO_INCREMENT` | `IDENTITY(1,1)` |
| `VARCHAR(n)` | `NVARCHAR(n)` (Unicode) |
| `TEXT` | `NVARCHAR(MAX)` |
| `BOOLEAN` | `BIT` |
| `TIMESTAMP` | `DATETIME2` |
| `ENUM('a','b','c')` | `CHECK (column IN ('a','b','c'))` |
| `CURRENT_TIMESTAMP` | `GETUTCDATE()` |
| `IF NOT EXISTS` | `IF OBJECT_ID() IS NULL` |
| `LIMIT n` | `TOP n` |
| `TRUE/FALSE` | `1/0` |

### 2. **Triggers de Update Automático**

**MySQL:**
```sql
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
```

**T-SQL:**
```sql
updated_at DATETIME2 NOT NULL DEFAULT GETUTCDATE(),

-- Trigger separado:
CREATE TRIGGER tr_table_updated_at
ON table_name
AFTER UPDATE AS
BEGIN
    UPDATE table_name 
    SET updated_at = GETUTCDATE()
    FROM table_name t
    INNER JOIN inserted i ON t.id = i.id;
END
```

## 🔧 Configuração do Azure SQL Database

### 1. **Criar Banco no Azure Portal**

1. **Acesse**: https://portal.azure.com
2. **Criar Recurso** → **SQL Database**
3. **Configurações básicas**:
   - Subscription: Sua assinatura
   - Resource Group: Novo ou existente
   - Database name: `vulnerable_db`
   - Server: Criar novo servidor
   - Want to use SQL elastic pool: No

4. **Servidor SQL**:
   - Server name: `seu-servidor-vulneravel` (único globalmente)
   - Server admin login: `azureuser`
   - Password: `MinhaSenh@123!` (forte)
   - Location: Brazil South ou mais próximo

5. **Compute + Storage**:
   - Service tier: Basic (mais barato para testes)
   - Compute tier: Serverless (para desenvolvimento)

### 2. **Configurar Firewall**

1. No Azure Portal, vá para seu SQL Server
2. **Security** → **Firewalls and virtual networks**
3. **Add client IP** (adiciona seu IP atual)
4. Para testes locais, adicione: **0.0.0.0** a **255.255.255.255** (⚠️ NÃO em produção!)

### 3. **Obter String de Conexão**

1. Vá para o banco de dados criado
2. **Settings** → **Connection strings**
3. Copie a string **PHP (PDO)**:
```php
$dsn = "sqlsrv:server=seu-servidor.database.windows.net,1433;Database=vulnerable_db;LoginTimeout=30;Encrypt=1;TrustServerCertificate=0";
```

## 🐘 Instalação dos Drivers PHP

### **Windows (XAMPP)**

1. **Baixar Microsoft Drivers for PHP**:
   - https://docs.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server

2. **Instalar ODBC Driver**:
   - Download: Microsoft ODBC Driver 17 for SQL Server
   - Executar como administrador

3. **Copiar DLLs PHP**:
   ```
   Baixe: Microsoft Drivers 5.10 for PHP for SQL Server
   Extraia: php_sqlsrv_XX_nts.dll e php_pdo_sqlsrv_XX_nts.dll
   Copie para: C:\xampp\php\ext\
   ```

4. **Editar php.ini** (`C:\xampp\php\php.ini`):
   ```ini
   extension=sqlsrv
   extension=pdo_sqlsrv
   ```

5. **Reiniciar Apache** no XAMPP

### **Linux (Ubuntu/Debian)**

```bash
# Adicionar repositório Microsoft
curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
curl https://packages.microsoft.com/config/ubuntu/20.04/prod.list > /etc/apt/sources.list.d/mssql-release.list

# Instalar ODBC Driver
apt-get update
ACCEPT_EULA=Y apt-get install -y msodbcsql17

# Instalar PHP drivers
apt-get install -y php-sqlsrv php-pdo-sqlsrv

# Reiniciar servidor web
systemctl restart apache2
```

## 📄 Executando o Script SQL

### **Via Azure Data Studio** (Recomendado)

1. **Instalar**: https://docs.microsoft.com/en-us/sql/azure-data-studio/download
2. **Conectar** ao seu Azure SQL Database
3. **Abrir** o arquivo `create_database_azure.sql`
4. **Executar** o script completo

### **Via SQL Server Management Studio (SSMS)**

1. **Instalar SSMS**: https://docs.microsoft.com/en-us/sql/ssms/download-sql-server-management-studio-ssms
2. **Conectar**:
   - Server: `seu-servidor.database.windows.net`
   - Authentication: SQL Server Authentication
   - Login: `azureuser`
   - Password: sua senha
3. **Executar** o script

### **Via Azure Portal Query Editor**

1. No Azure Portal, vá para seu banco de dados
2. **Query editor (preview)**
3. **Login** com credenciais SQL
4. **Cole** o script `create_database_azure.sql`
5. **Run** o script

### **Via Command Line** (sqlcmd)

```bash
# Instalar sqlcmd
# Windows: Incluído com SSMS
# Linux: apt-get install mssql-tools

sqlcmd -S seu-servidor.database.windows.net -d vulnerable_db -U azureuser -P "MinhaSenh@123!" -i create_database_azure.sql
```

## 🔧 Configuração da Aplicação PHP

### 1. **Atualizar Credenciais**

Edite `config/azure_database.php`:
```php
private $server = "seu-servidor.database.windows.net";
private $database = "vulnerable_db";
private $username = "azureuser";
private $password = "MinhaSenh@123!";
```

### 2. **Atualizar Referências**

Modifique os arquivos PHP para usar a nova classe:

**Opção A** - Substituir includes:
```php
// De:
require_once '../../config/database.php';

// Para:
require_once '../../config/azure_database.php';
```

**Opção B** - Usar alias (mais fácil):
```php
// A classe Database herda de AzureDatabase
// Código existente continua funcionando sem alterações
require_once '../../config/azure_database.php';
$db = new Database(); // Funciona normalmente
```

### 3. **Criar Arquivo de Teste**

```php
<?php
// test_azure_connection.php
require_once '../../config/azure_database.php';

try {
    echo "<h1>🌐 Teste de Conexão Azure SQL Database</h1>";
    
    $db = new AzureDatabase();
    echo "✅ Conexão estabelecida!<br>";
    
    // Teste de contagem
    $userCount = $db->countRecords('users');
    echo "✅ Total de usuários: $userCount<br>";
    
    // Teste de sistema info
    $systemInfo = $db->getSystemInfo();
    echo "<h3>Informações do Sistema:</h3>";
    foreach($systemInfo as $info) {
        echo "- {$info['info_type']}: {$info['info_value']}<br>";
    }
    
    echo "<h3>✅ Azure SQL Database funcionando perfeitamente!</h3>";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>
```

## 🎯 Principais Mudanças no Código

### **Queries com TOP ao invés de LIMIT**
```php
// MySQL:
"SELECT * FROM users LIMIT 10"

// T-SQL:
"SELECT TOP 10 * FROM users"
```

### **Datas com GETUTCDATE()**
```php
// MySQL:
"INSERT INTO table (created_at) VALUES (NOW())"

// T-SQL:
"INSERT INTO table (created_at) VALUES (GETUTCDATE())"
```

### **Booleans como BIT**
```php
// MySQL:
"WHERE success = TRUE"

// T-SQL:
"WHERE success = 1"
```

### **Stored Procedures**
```sql
-- MySQL:
DELIMITER $$
CREATE PROCEDURE GetUser(IN user_id INT)
BEGIN
    SELECT * FROM users WHERE id = user_id;
END$$
DELIMITER ;

-- T-SQL:
CREATE PROCEDURE GetUser
    @user_id INT
AS
BEGIN
    SELECT * FROM users WHERE id = @user_id;
END
```

## 💰 Custos e Otimização

### **Custos Típicos** (Brasil Sul):
- **Basic**: ~R$ 15/mês (5 DTUs)
- **Serverless**: ~R$ 30-100/mês (conforme uso)
- **Standard S0**: ~R$ 45/mês (10 DTUs)

### **Otimizações**:
1. **Use Serverless** para desenvolvimento (pausa automaticamente)
2. **Configure Auto-pause**: 1 hora de inatividade
3. **Monitor custos** no Azure Portal
4. **Delete recursos** após testes

## ✅ Checklist de Migração

- [ ] ✅ **Banco Azure criado** e acessível
- [ ] ✅ **Firewall configurado** para seu IP
- [ ] ✅ **Drivers PHP instalados** e funcionando
- [ ] ✅ **Script T-SQL executado** com sucesso
- [ ] ✅ **Credenciais atualizadas** no código PHP
- [ ] ✅ **Conexão testada** com sucesso
- [ ] ✅ **Aplicação funcionando** com Azure SQL
- [ ] ✅ **Vulnerabilidades testadas** no Azure

## 🚨 Importantes Diferenças de Comportamento

### **1. Case Sensitivity**
- Azure SQL pode ser case-sensitive dependendo do collation
- Use sempre aspas corretas: `[table]` ou `"column"`

### **2. Transactions**
- Azure SQL é mais rigoroso com transações
- Sempre use `BEGIN TRANSACTION` / `COMMIT` explicitamente

### **3. Error Handling**
- Mensagens de erro diferentes
- Códigos de erro específicos do SQL Server

### **4. Permissions**
- Sistema de permissões mais granular
- Roles diferentes do MySQL

## 🔐 Segurança Adicional no Azure

Mesmo sendo vulnerável por design, o Azure oferece:

- **Threat Detection**: Detecta tentativas de SQL injection
- **Auditing**: Log completo de todas as atividades
- **Encryption**: Dados criptografados em trânsito e repouso
- **Firewall**: Proteção a nível de servidor

Para fins educacionais, você pode **desabilitar** alguns recursos:
```sql
-- Desabilitar threat detection (se necessário para testes)
-- Configurar no Azure Portal > Security > Advanced Threat Protection
```

## 📞 Suporte e Troubleshooting

### **Problemas Comuns**:

1. **"Could not find driver"**
   - Drivers PHP não instalados corretamente
   - Verificar `phpinfo()` se extensões estão carregadas

2. **"Login failed for user"**
   - Credenciais incorretas
   - Firewall bloqueando conexão

3. **"Cannot open server"**
   - Nome do servidor incorreto
   - Porta incorreta (use 1433)

4. **"SSL connection is required"**
   - Azure força SSL por padrão
   - Usar `Encrypt=1` na string de conexão

### **Logs para Debug**:
- Azure Portal → SQL Database → Monitoring → Metrics
- Query Performance Insight
- Azure Activity Log

---

## 🎉 **Migração Completa!**

Após seguir este guia, você terá:
- ✅ **Azure SQL Database** configurado e funcionando
- ✅ **Aplicação PHP** conectada ao Azure
- ✅ **Todas as vulnerabilidades** funcionando em nuvem
- ✅ **Ambiente educacional** completo no Azure

**🌐 Seu sistema vulnerável agora roda na nuvem da Microsoft!**