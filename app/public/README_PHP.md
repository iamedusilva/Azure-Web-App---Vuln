# 🔧 Instruções para as Páginas PHP no Diretório Public

## 📋 Configuração Necessária

Antes de usar as páginas PHP, certifique-se de que:

### 1. Banco de Dados Configurado
Execute o script SQL de criação:
```bash
# Via linha de comando
mysql -u root -p < database/create_database.sql

# Ou via phpMyAdmin
# Copie e execute o conteúdo de database/create_database.sql
```

### 2. Estrutura de Arquivos
Certifique-se de que existe:
```
c:\xampp\htdocs\Azure-Web-App---Vuln\
├── config\database.php          # ✅ Classe de conexão
├── database\create_database.sql # ✅ Script SQL
├── app\public\
│   ├── index.php               # ✅ Página principal (nova)
│   ├── login.php               # ✅ Login com banco
│   ├── register.php            # ✅ Cadastro com banco  
│   ├── dashboard.php           # ✅ Dashboard do usuário
│   ├── login.html              # ⚠️ Versão antiga (só frontend)
│   └── register.html           # ⚠️ Versão antiga (só frontend)
```

## 🌐 URLs de Acesso

### Páginas Funcionais (PHP + Banco):
- **Página Principal:** `http://localhost/Azure-Web-App---Vuln/app/public/index.php`
- **Login:** `http://localhost/Azure-Web-App---Vuln/app/public/login.php`
- **Cadastro:** `http://localhost/Azure-Web-App---Vuln/app/public/register.php`
- **Dashboard:** `http://localhost/Azure-Web-App---Vuln/app/public/dashboard.php`

### Páginas Antigas (HTML apenas):
- `login.html` - Apenas simulação visual
- `register.html` - Apenas simulação visual
- `dashboard.html` - Apenas estático

## 🔐 Funcionamento do Sistema de Login

### Usuários Pré-cadastrados:
| Username | Password | Role  | Descrição |
|----------|----------|-------|-----------|
| admin    | admin    | admin | Administrador |
| root     | 123456   | admin | Super usuário |
| user1    | password | user  | Usuário comum |
| guest    | guest    | guest | Visitante |

### Fluxo de Autenticação:
1. **Login** (`login.php`):
   - Valida credenciais contra o banco `users`
   - Cria sessão PHP
   - Registra tentativa em `login_logs` (incluindo senhas!)
   - Redireciona para `dashboard.php`

2. **Dashboard** (`dashboard.php`):
   - Verifica sessão (vulnerável)
   - Permite IDOR via `?user_id=X`
   - Mostra dados do usuário
   - Permite edição de perfil

3. **Cadastro** (`register.php`):
   - Insere novo usuário no banco
   - Senhas em texto plano (vulnerável)
   - Validação mínima
   - Logs de cadastro

## 🎯 Vulnerabilidades Implementadas

### 1. SQL Injection no Login
**Local:** `login.php`
**Como testar:**
```sql
# Campo usuário:
admin' OR '1'='1'--
admin' OR 1=1#
' UNION SELECT 1,2,3,4,username,password,7,8 FROM users--
```

### 2. SQL Injection no Cadastro
**Local:** `register.php`
**Como testar:**
```sql
# Campo username:
test'; INSERT INTO users VALUES(99,'hacker','123','hack@evil.com','Hacker','admin',1,NOW(),NOW()); --
```

### 3. IDOR no Dashboard  
**Local:** `dashboard.php`
**Como testar:**
```
dashboard.php?user_id=1  # Ver dados do admin
dashboard.php?user_id=2  # Ver dados de outro usuário
```

### 4. XSS nos Dados
**Locais:** Comentários, perfil, cadastro
**Como testar:**
```html
<script>alert('XSS')</script>
<img src=x onerror=alert('XSS')>
```

### 5. Exposição de Dados Sensíveis
- Senhas visíveis no dashboard
- Logs com senhas tentadas
- Estatísticas públicas com dados sensíveis

### 6. Bypass de Autenticação
- Dashboard acessível via GET: `?user_id=X`
- Verificação de sessão fraca
- Admin panel: `?admin=true`

## 📊 Funcionalidades do Sistema

### Login (`login.php`)
✅ **Funciona:**
- Autenticação real contra banco
- Criação de sessões PHP
- Redirecionamento pós-login
- Log de tentativas (vulnerável)

✅ **Vulnerabilidades:**
- SQL injection nos campos
- Senhas logadas em texto plano
- Bypass via SQL injection

### Cadastro (`register.php`)
✅ **Funciona:**
- Inserção real no banco de dados
- Validação básica de campos
- Verificação de usuário existente
- Redirecionamento pós-cadastro

✅ **Vulnerabilidades:**
- SQL injection em todos os campos
- Senhas em texto plano
- XSS nos campos de texto
- Validação insuficiente

### Dashboard (`dashboard.php`)
✅ **Funciona:**
- Exibição de dados do usuário
- Edição de perfil
- Estatísticas do sistema
- Links de navegação

✅ **Vulnerabilidades:**
- IDOR via parâmetro GET
- Exposição de senhas
- XSS nos dados exibidos
- Update de perfil vulnerável

## 🔧 Solução de Problemas

### Erro "Database connection failed"
1. Verifique se MySQL está rodando no XAMPP
2. Execute o script `database/create_database.sql`
3. Confirme as credenciais em `config/database.php`

### Erro "Table doesn't exist"
Execute o script SQL completo:
```bash
mysql -u root -p < database/create_database.sql
```

### Página não carrega
1. Verifique se Apache está rodando
2. Confirme a URL: `http://localhost/Azure-Web-App---Vuln/app/public/login.php`
3. Verifique logs do Apache

### Sessão não funciona
1. Certifique-se de que `session_start()` está sendo chamado
2. Verifique se cookies estão habilitados no navegador
3. Teste com dados válidos primeiro

## 🚀 Testando o Sistema Completo

### 1. Teste de Cadastro:
```
1. Acesse: register.php
2. Preencha os campos normalmente
3. Clique em "Cadastrar"
4. Deve redirecionar para login.php
```

### 2. Teste de Login:
```
1. Acesse: login.php  
2. Use: admin / admin
3. Deve redirecionar para dashboard.php
4. Verifique se dados aparecem corretamente
```

### 3. Teste de SQL Injection:
```
1. No login, use: admin' OR '1'='1'--
2. Senha: qualquer coisa
3. Deve fazer login como admin
```

### 4. Teste de IDOR:
```
1. Após login, vá para: dashboard.php?user_id=2
2. Deve mostrar dados de outro usuário
```

## ⚠️ Notas Importantes

1. **Sempre use as versões PHP** (`.php`) para funcionalidade completa
2. **As versões HTML** (`.html`) são apenas para demonstração visual
3. **Execute o script SQL** antes de testar as páginas PHP
4. **Mantenha o XAMPP rodando** durante os testes
5. **Use apenas para aprendizado** - nunca em produção!

## 📝 Log de Mudanças

- ✅ Criadas versões PHP funcionais de todas as páginas
- ✅ Integração completa com banco de dados MySQL
- ✅ Sistema de sessões implementado
- ✅ Vulnerabilidades funcionais (não apenas simuladas)
- ✅ Cadastro real de usuários no banco
- ✅ Dashboard com dados reais
- ✅ Logs de atividade implementados
- ✅ IDOR funcional
- ✅ SQL injection real