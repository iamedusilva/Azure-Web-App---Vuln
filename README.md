# Instruções de Instalação e Configuração

## 📋 Pré-requisitos

- XAMPP instalado e funcionando
- PHP 7.4+ 
- MySQL/MariaDB ativo
- Navegador web

## 🗄️ Configuração do Banco de Dados

### 1. Executar o Script SQL

Há duas formas de executar o script de criação do banco:

#### Opção A: Via phpMyAdmin
1. Abra o phpMyAdmin: `http://localhost/phpmyadmin`
2. Clique em "SQL" no menu superior
3. Copie todo o conteúdo do arquivo `database/create_database.sql`
4. Cole no campo de texto e clique em "Executar"

#### Opção B: Via Linha de Comando
```bash
# Navegue até o diretório do projeto
cd c:\xampp\htdocs\Azure-Web-App---Vuln

# Execute o script SQL
mysql -u root -p < database/create_database.sql
```

### 2. Verificar a Instalação

Após executar o script, você deve ter:
- ✅ Banco `vulnerable_db` criado
- ✅ 6 tabelas criadas (users, comments, login_logs, sessions, uploaded_files, config)
- ✅ Dados de exemplo inseridos
- ✅ 7 usuários de teste
- ✅ Comentários com XSS
- ✅ Configurações sensíveis

## 🚀 Executando a Aplicação

1. Certifique-se de que o Apache e MySQL estão rodando no XAMPP
2. Acesse: `http://localhost/Azure-Web-App---Vuln/index.php`
3. A aplicação deve carregar e mostrar os comentários existentes

## 🔐 Usuários de Teste

Utilize estes usuários para testar as funcionalidades:

| Username | Password | Role  | Descrição |
|----------|----------|-------|-----------|
| admin    | admin    | admin | Administrador |
| root     | 123456   | admin | Super usuário |
| user1    | password | user  | Usuário comum |
| guest    | guest    | guest | Visitante |
| test     | test123  | user  | Para testes |
| demo     | demo     | user  | Demonstração |
| manager  | manager123| user | Gerente |

## 🔍 Testando as Vulnerabilidades

### SQL Injection no Login
Tente estes payloads no campo de usuário:
```sql
admin' OR '1'='1'--
' OR 1=1#
admin' OR '1'='1' LIMIT 1--
```

### XSS nos Comentários
Tente estes payloads no campo de comentário:
```html
<script>alert('XSS')</script>
<img src="x" onerror="alert('XSS')">
<svg onload="alert('XSS')">
```

### Informações Sensíveis Expostas
- Senhas em texto plano na tabela `users`
- Logs de tentativas de login na tabela `login_logs`
- Configurações sensíveis na tabela `config`

## 📁 Estrutura dos Arquivos

```
c:\xampp\htdocs\Azure-Web-App---Vuln\
├── index.php                  # Página principal (vulnerável)
├── config/
│   └── database.php          # Classe de conexão PHP (vulnerável)
├── database/
│   └── create_database.sql   # Script de criação do banco
├── app/
│   ├── public/
│   │   ├── dashboard.html
│   │   ├── index.html
│   │   ├── login.html
│   │   └── register.html
│   ├── src/
│   │   ├── db.js            # Conexão Node.js (existente)
│   │   ├── routes.js
│   │   ├── server.js
│   │   └── utils.js
│   └── tests/
│       └── xss-tests.md
└── README.md                # Este arquivo
```

## ⚠️ Vulnerabilidades Implementadas

### 1. SQL Injection
- **Localização**: Login e comentários
- **Como testar**: Use payloads SQL nos campos de entrada
- **Impacto**: Bypass de autenticação, extração de dados

### 2. Cross-Site Scripting (XSS)
- **Localização**: Sistema de comentários
- **Como testar**: Insira código JavaScript nos comentários
- **Impacto**: Execução de código no navegador da vítima

### 3. Exposição de Dados Sensíveis
- **Localização**: Banco de dados
- **Como verificar**: Consulte as tabelas diretamente
- **Impacto**: Vazamento de senhas, tokens, configurações

### 4. Logging Excessivo
- **Localização**: Tabela `login_logs`
- **Problema**: Armazena senhas tentadas
- **Impacto**: Exposição de credenciais em logs

### 5. Configurações Inseguras
- **Localização**: Usuários do banco, permissões
- **Problema**: Permissões excessivas, senhas fracas
- **Impacato**: Escalação de privilégios

## 🛡️ IMPORTANTE - AVISO DE SEGURANÇA

**⚠️ ESTA APLICAÇÃO É PROPOSITALMENTE VULNERÁVEL ⚠️**

- **NÃO use em produção**
- **NÃO exponha na internet**
- **Use apenas para aprendizado**
- **Mantenha em ambiente isolado**

## 📚 Recursos Educacionais

### Para Estudar SQL Injection:
1. Tente diferentes payloads de SQL injection
2. Observe as queries geradas nos comentários HTML
3. Use ferramentas como Burp Suite ou OWASP ZAP

### Para Estudar XSS:
1. Experimente diferentes tipos de XSS
2. Observe como o código é executado
3. Teste filtros de XSS básicos

### Para Estudar Exposição de Dados:
1. Examine as tabelas do banco de dados
2. Veja como informações sensíveis são armazenadas
3. Analise os logs gerados pela aplicação

## 🔧 Troubleshooting

### Erro de Conexão com Banco
- Verifique se o MySQL está rodando no XAMPP
- Confirme se as credenciais no `config/database.php` estão corretas
- Execute o script SQL novamente se necessário

### Página não Carrega
- Verifique se o Apache está rodando
- Confirme o caminho da aplicação
- Verifique logs de erro do Apache

### Erro PHP
- Verifique se a versão do PHP é 7.4+
- Confirme se as extensões mysqli estão habilitadas
- Verifique logs de erro do PHP

## 📞 Suporte

Este é um projeto educacional. Para questões:
1. Revise a documentação
2. Verifique a configuração do XAMPP
3. Consulte logs de erro do sistema

---

**Lembre-se: Esta aplicação é vulnerável POR DESIGN. Use com responsabilidade!**