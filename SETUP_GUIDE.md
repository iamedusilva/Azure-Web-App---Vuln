# 🚀 Guia de Instalação Rápida

## ⚡ Setup em 5 Minutos

### 1. Pré-requisitos (2 min)
```bash
# Verificar PHP
php --version  # Necessário: PHP 8.x

# Verificar extensões
php -m | grep -i pdo
php -m | grep -i sqlsrv
```

### 2. Clone e Configuração (1 min)
```bash
# Clone o projeto
git clone https://github.com/seu-usuario/Azure-Web-App---Vuln.git
cd Azure-Web-App---Vuln

# Configurar permissões (Linux/Mac)
chmod -R 755 app/public/
chmod -R 777 logs/ # se existir
```

### 3. Banco de Dados (2 min)
```sql
-- 1. Criar banco no Azure SQL Database
-- 2. Executar: database/create_database_azure.sql
-- 3. Anotar: servidor, usuário, senha, database
```

**Configurar conexão em `config/database.php`:**
```php
private $azure_server = "tcp:SEU_SERVIDOR.database.windows.net,1433";
private $azure_username = "SEU_USUARIO";
private $azure_password = "SUA_SENHA";
private $azure_database = "SEU_BANCO";
```

### 4. Testar Instalação
```bash
# Acessar no navegador:
http://localhost/Azure-Web-App---Vuln/app/public/

# Testar login com usuário padrão:
Usuário: admin
Senha: admin123
```

---

## 🔧 Solução de Problemas Comuns

### Erro: "Could not find driver"
```bash
# Instalar extensões SQL Server para PHP
# Windows (XAMPP):
# 1. Baixar drivers do Microsoft SQL Server para PHP
# 2. Copiar para pasta ext/ do PHP
# 3. Adicionar ao php.ini:
extension=php_pdo_sqlsrv_81_ts_x64.dll
extension=php_sqlsrv_81_ts_x64.dll
```

### Erro de Conexão Azure SQL
```php
// Verificar configurações de firewall no Azure
// Adicionar IP atual nas regras de firewall
// Testar conexão:
$connectionInfo = array(
    "Database" => "vulnerable_db",
    "Uid" => "usuario",
    "PWD" => "senha"
);
$conn = sqlsrv_connect("servidor.database.windows.net", $connectionInfo);
```

### CSS/Images não carregam
```bash
# Verificar estrutura de pastas:
app/public/assets/css/
app/public/assets/images/

# Verificar permissões:
chmod -R 755 app/public/assets/
```

---

## 📝 Checklist de Instalação

- [ ] PHP 8.x instalado
- [ ] Extensões PDO e SQLSRV habilitadas
- [ ] Projeto clonado/baixado
- [ ] Azure SQL Database criado
- [ ] Script SQL executado
- [ ] Credenciais configuradas em database.php
- [ ] Página inicial carrega (http://localhost/.../app/public/)
- [ ] Login funciona com credenciais padrão
- [ ] Dashboard carrega sem erros
- [ ] CSS e imagens aparecem corretamente

---

## 🎯 Primeiros Passos após Instalação

### 1. Explorar a Interface
- Navegar pela página inicial
- Fazer login com admin/admin123
- Explorar o dashboard e modais
- Testar responsividade em mobile

### 2. Testar Vulnerabilidades
```sql
-- SQL Injection básico
Username: admin' OR '1'='1' --
Password: qualquer

-- XSS no sistema de comentários
<script>alert('XSS funcionando!')</script>
```

### 3. Análise do Código
- Examinar `config/database.php`
- Estudar `app/public/dashboard.php`
- Identificar pontos vulneráveis
- Comparar com boas práticas de segurança

### 4. Documentação
- Ler README.md completo
- Consultar TECHNICAL_DOCS.md
- Explorar comentários no código
- Mapear todas as vulnerabilidades

---

## 🆘 Suporte

### Problemas Técnicos
1. Verificar logs de erro do PHP
2. Testar conexão com banco separadamente
3. Validar configurações de firewall
4. Consultar documentação oficial Azure SQL

### Problemas de Segurança
> ⚠️ **LEMBRETE:** Este é um projeto educacional com vulnerabilidades intencionais!

### Contato
- 📧 Email: suporte@projeto-educacional.com
- 📚 Wiki: [Link para documentação]
- 🐛 Issues: [Link para issues do GitHub]

---

**✅ Instalação Completa!** Agora você pode começar a explorar as vulnerabilidades educacionais de forma segura.