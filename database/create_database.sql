-- ================================================
-- SCRIPT DE CRIAÇÃO DO BANCO DE DADOS VULNERÁVEL
-- ================================================
-- ATENÇÃO: Este banco é propositalmente vulnerável para fins educacionais
-- NÃO USE EM PRODUÇÃO!

-- Criar o banco de dados
CREATE DATABASE IF NOT EXISTS vulnerable_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vulnerable_db;

-- ================================================
-- TABELA DE USUÁRIOS
-- ================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,  -- VULNERÁVEL: senhas em texto plano
    email VARCHAR(100),
    full_name VARCHAR(100),
    role ENUM('admin', 'user', 'guest') DEFAULT 'user',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices básicos (sem considerar segurança)
    INDEX idx_username (username),
    INDEX idx_email (email)
);

-- ================================================
-- TABELA DE COMENTÁRIOS
-- ================================================
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    comment TEXT NOT NULL,  -- VULNERÁVEL: permite qualquer conteúdo (XSS)
    ip_address VARCHAR(45), -- Para demonstrar coleta de IPs
    user_agent TEXT,        -- Para demonstrar coleta de user agents
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Índice para ordenação
    INDEX idx_created_at (created_at)
);

-- ================================================
-- TABELA DE LOGS DE LOGIN (para demonstrar vazamento de dados)
-- ================================================
CREATE TABLE IF NOT EXISTS login_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    password_attempted VARCHAR(255), -- MUITO VULNERÁVEL: armazena senhas tentadas
    ip_address VARCHAR(45),
    user_agent TEXT,
    success BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_username (username),
    INDEX idx_created_at (created_at)
);

-- ================================================
-- TABELA DE SESSÕES (implementação vulnerável)
-- ================================================
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT,
    data TEXT,              -- VULNERÁVEL: dados da sessão não criptografados
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	expires_at TIMESTAMP NULL DEFAULT NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at)
);

-- ================================================
-- TABELA DE ARQUIVOS ENVIADOS
-- ================================================
CREATE TABLE IF NOT EXISTS uploaded_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(100),     -- VULNERÁVEL: confia no tipo enviado pelo cliente
    file_size INT,
    upload_path VARCHAR(500),   -- VULNERÁVEL: caminho completo exposto
    uploader_ip VARCHAR(45),
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_uploaded_by (uploaded_by),
    INDEX idx_file_type (file_type)
);

-- ================================================
-- TABELA DE CONFIGURAÇÕES SENSÍVEIS
-- ================================================
CREATE TABLE IF NOT EXISTS config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(100) NOT NULL UNIQUE,
    config_value TEXT NOT NULL,     -- VULNERÁVEL: valores sensíveis não criptografados
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_config_key (config_key)
);

-- ================================================
-- INSERIR DADOS DE EXEMPLO (VULNERÁVEIS)
-- ================================================

-- Usuários com senhas fracas em texto plano
INSERT INTO users (username, password, email, full_name, role) VALUES 
('admin', 'admin', 'admin@vulnerable-site.com', 'Administrador do Sistema', 'admin'),
('root', '123456', 'root@vulnerable-site.com', 'Super Usuario', 'admin'),
('user1', 'password', 'user1@email.com', 'João Silva', 'user'),
('guest', 'guest', 'guest@email.com', 'Usuário Visitante', 'guest'),
('test', 'test123', 'test@email.com', 'Usuário de Teste', 'user'),
('demo', 'demo', 'demo@email.com', 'Usuário Demo', 'user'),
('manager', 'manager123', 'manager@company.com', 'Gerente', 'user');

-- Comentários com conteúdo XSS
INSERT INTO comments (name, comment, ip_address, user_agent) VALUES 
('João', 'Ótimo sistema para aprender sobre segurança!', '192.168.1.100', 'Mozilla/5.0'),
('Maria', 'As vulnerabilidades estão bem demonstradas', '192.168.1.101', 'Chrome/91.0'),
('Hacker', '<script>alert("XSS Básico")</script>', '10.0.0.1', 'curl/7.68'),
('Pedro', 'Sistema interessante <img src="x" onerror="alert(\'XSS via img\')">', '192.168.1.102', 'Firefox/89.0'),
('Ana', 'Bom para estudos <svg onload="alert(\'SVG XSS\')">', '172.16.0.1', 'Safari/14.0'),
('Carlos', 'Vulnerabilidades: <iframe src="javascript:alert(\'Frame XSS\')"></iframe>', '10.0.0.2', 'Opera/76.0');

-- Logs de login (expondo tentativas de senha)
INSERT INTO login_logs (username, password_attempted, ip_address, user_agent, success) VALUES 
('admin', 'admin', '192.168.1.100', 'Mozilla/5.0', TRUE),
('admin', 'password', '10.0.0.1', 'curl/7.68', FALSE),
('admin', '123456', '10.0.0.1', 'curl/7.68', FALSE),
('root', 'toor', '172.16.0.1', 'Nmap NSE', FALSE),
('user1', 'user1', '192.168.1.101', 'Chrome/91.0', FALSE),
('guest', 'guest', '192.168.1.102', 'Firefox/89.0', TRUE);

-- Configurações sensíveis expostas
INSERT INTO config (config_key, config_value, description) VALUES 
('database_password', 'super_secret_pass', 'Senha do banco de dados principal'),
('api_key', 'sk-1234567890abcdef', 'Chave da API externa'),
('secret_key', 'my_secret_key_123', 'Chave secreta da aplicação'),
('admin_email', 'admin@vulnerable-site.com', 'Email do administrador'),
('debug_mode', 'true', 'Modo de debug ativo'),
('allow_file_upload', 'true', 'Permite upload de arquivos'),
('max_file_size', '10485760', 'Tamanho máximo de arquivo (10MB)'),
('encryption_key', 'weak_encryption_key', 'Chave de criptografia fraca');

-- Exemplos de arquivos "enviados"
INSERT INTO uploaded_files (filename, original_name, file_type, file_size, upload_path, uploader_ip, uploaded_by) VALUES 
('img_001.jpg', 'foto.jpg', 'image/jpeg', 245760, '/uploads/img_001.jpg', '192.168.1.100', 1),
('doc_001.pdf', 'documento.pdf', 'application/pdf', 1048576, '/uploads/doc_001.pdf', '192.168.1.101', 2),
('script.php', 'backdoor.php', 'application/x-php', 2048, '/uploads/script.php', '10.0.0.1', NULL),
('shell.jsp', 'webshell.jsp', 'text/plain', 4096, '/uploads/shell.jsp', '172.16.0.1', NULL);

-- ================================================
-- VIEWS VULNERÁVEIS (expõem dados sensíveis)
-- ================================================

-- View que expõe senhas
CREATE VIEW user_credentials AS 
SELECT id, username, password, email, role 
FROM users 
WHERE is_active = TRUE;

-- View que expõe logs de tentativas de login
CREATE VIEW failed_logins AS 
SELECT username, password_attempted, ip_address, created_at 
FROM login_logs 
WHERE success = FALSE 
ORDER BY created_at DESC;

-- View que expõe configurações
CREATE VIEW system_config AS 
SELECT config_key, config_value, description 
FROM config;

-- ================================================
-- STORED PROCEDURES VULNERÁVEIS
-- ================================================

DELIMITER $$

-- Procedure vulnerável que permite SQL injection
CREATE PROCEDURE GetUserByName(IN user_name VARCHAR(50))
BEGIN
    SET @sql = CONCAT('SELECT * FROM users WHERE username = "', user_name, '"');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

-- Procedure que expõe informações sensíveis
CREATE PROCEDURE GetSystemInfo()
BEGIN
    SELECT 'Database Version' as info_type, VERSION() as info_value
    UNION ALL
    SELECT 'Current User', USER()
    UNION ALL
    SELECT 'Current Database', DATABASE()
    UNION ALL
    SELECT 'Data Directory', @@datadir
    UNION ALL
    SELECT 'Server Hostname', @@hostname;
END$$

-- Procedure para "limpeza" que na verdade não limpa nada
CREATE PROCEDURE CleanLogs(IN days_old INT)
BEGIN
    -- Finge que limpa, mas não faz nada (vulnerabilidade de negação de serviço)
    SELECT CONCAT('Seria para deletar logs de ', days_old, ' dias atrás') as message;
END$$

DELIMITER ;

-- ================================================
-- TRIGGERS PROBLEMÁTICOS
-- ================================================

-- Trigger que loga tentativas de login (inclusive senhas)
DELIMITER $$
CREATE TRIGGER log_login_attempts 
AFTER INSERT ON login_logs
FOR EACH ROW
BEGIN
    -- Log adicional que poderia ser explorado
    INSERT INTO comments (name, comment) 
    VALUES ('System', CONCAT('Login attempt: ', NEW.username, ' with password: ', NEW.password_attempted));
END$$
DELIMITER ;

-- ================================================
-- ÍNDICES E PERMISSÕES PROBLEMÁTICAS
-- ================================================

-- Criar usuário com permissões excessivas
CREATE USER IF NOT EXISTS 'webapp'@'%' IDENTIFIED BY 'weak_password';
GRANT ALL PRIVILEGES ON vulnerable_db.* TO 'webapp'@'%';
GRANT FILE ON *.* TO 'webapp'@'%'; -- MUITO PERIGOSO - permite ler arquivos do sistema

-- Usuário anônimo (muito perigoso)
CREATE USER IF NOT EXISTS ''@'%';
GRANT SELECT ON vulnerable_db.* TO ''@'%';

-- Flush privileges para aplicar mudanças
FLUSH PRIVILEGES;

-- ================================================
-- COMENTÁRIOS SOBRE AS VULNERABILIDADES
-- ================================================

/*
VULNERABILIDADES IMPLEMENTADAS NESTE BANCO:

1. SENHAS EM TEXTO PLANO
   - Todas as senhas são armazenadas sem hash
   - Facilmente visíveis em dumps do banco

2. SQL INJECTION
   - Stored procedures vulneráveis
   - Views que podem ser exploradas
   - Falta de sanitização em queries

3. EXPOSIÇÃO DE DADOS SENSÍVEIS
   - Tabela config com chaves de API
   - Logs de login com senhas tentadas
   - Views que expõem informações críticas

4. XSS (Cross-Site Scripting)
   - Comentários permitem HTML/JavaScript
   - Falta de sanitização na saída

5. CONFIGURAÇÕES INSEGURAS
   - Usuários com senhas fracas
   - Permissões excessivas
   - Usuário anônimo ativo

6. INFORMATION DISCLOSURE
   - Stored procedures que expõem info do sistema
   - Triggers que logam dados sensíveis
   - Caminhos completos de arquivos expostos

7. WEAK ACCESS CONTROL
   - Falta de controle de acesso adequado
   - Usuários com privilégios desnecessários

NUNCA USE ESTE ESQUEMA EM PRODUÇÃO!
É apenas para fins educacionais e demonstração de vulnerabilidades.
*/

-- ================================================
-- FINALIZAÇÃO
-- ================================================

-- Mostrar estatísticas da criação
SELECT 'Banco criado com sucesso!' as status;
SELECT COUNT(*) as total_users FROM users;
SELECT COUNT(*) as total_comments FROM comments;
SELECT COUNT(*) as total_configs FROM config;

-- Fim do scriptusername