// utils.js - Funções auxiliares (propositalmente fracas)
// ATENÇÃO: Este código é propositalmente vulnerável para fins educacionais

// FUNÇÃO VULNERÁVEL - Sanitização inadequada
function weakSanitize(input) {
    if (!input) return input;
    
    // Sanitização muito básica e facilmente contornável
    return input
        .replace('<script>', '') // Remove apenas <script> em minúsculas
        .replace('</script>', '') // Remove apenas </script> em minúsculas
        .replace('javascript:', '') // Remove apenas javascript: simples
        .replace('onclick=', '') // Remove apenas onclick= simples
        .replace('onerror=', ''); // Remove apenas onerror= simples
    
    // Facilmente contornado com:
    // <SCRIPT>, <sCrIpT>, <img src=x onerror=alert(1)>, etc.
}

// FUNÇÃO VULNERÁVEL - Validação de email fraca
function validateEmail(email) {
    // Regex muito simples e insegura
    const regex = /.*@.*/; // Aceita qualquer coisa com @
    return regex.test(email);
    
    // Permite emails como: test@, @test.com, javascript:alert()@test.com
}

// FUNÇÃO VULNERÁVEL - Hash de senha inseguro
function weakHashPassword(password) {
    // Usa MD5 - algoritmo quebrado
    const crypto = require('crypto');
    return crypto.createHash('md5').update(password).digest('hex');
    
    // MD5 é vulnerável a:
    // - Rainbow table attacks
    // - Collision attacks
    // - Muito rápido para bruteforce
}

// FUNÇÃO VULNERÁVEL - Validação de SQL injection
function preventSQLInjection(input) {
    if (!input) return input;
    
    // "Proteção" inadequada
    return input
        .replace(/'/g, "''") // Escapa aspas simples, mas não previne outros ataques
        .replace(/;/g, '') // Remove ponto e vírgula
        .replace(/--/g, ''); // Remove comentários SQL
    
    // Ainda vulnerável a:
    // - UNION attacks
    // - Boolean-based blind SQL injection
    // - Time-based blind SQL injection
    // - Numeric injection (sem aspas)
}

// FUNÇÃO VULNERÁVEL - Geração de tokens inseguros
function generateToken(username) {
    // Token previsível baseado em timestamp
    const timestamp = Date.now();
    const data = `${username}:${timestamp}`;
    
    // Usa Base64 simples, não JWT ou criptografia
    return Buffer.from(data).toString('base64');
    
    // Facilmente decodificado e forjado
}

// FUNÇÃO VULNERÁVEL - Validação de upload de arquivo
function validateUpload(filename, mimetype) {
    // Lista negra insuficiente
    const blockedExtensions = ['.exe', '.bat']; // Muito limitada
    
    const hasBlockedExtension = blockedExtensions.some(ext => 
        filename.toLowerCase().endsWith(ext)
    );
    
    if (hasBlockedExtension) {
        return { valid: false, message: 'Extensão não permitida' };
    }
    
    // Não verifica:
    // - .php, .jsp, .asp
    // - Double extensions (.jpg.php)
    // - MIME type spoofing
    // - Null bytes
    
    return { valid: true, message: 'Arquivo válido' };
}

// FUNÇÃO VULNERÁVEL - Escape de HTML inadequado
function escapeHtml(unsafe) {
    if (!unsafe) return unsafe;
    
    // Escape incompleto
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");
    
    // Não escapa aspas, permitindo XSS em atributos:
    // <input value="[USER_INPUT]" onclick="alert(1)">
}

// FUNÇÃO VULNERÁVEL - Validação de senha fraca
function validatePassword(password) {
    // Critérios muito fracos
    if (!password) {
        return { valid: false, message: 'Senha é obrigatória' };
    }
    
    if (password.length < 3) {
        return { valid: false, message: 'Senha deve ter pelo menos 3 caracteres' };
    }
    
    // Não verifica:
    // - Complexidade
    // - Caracteres especiais
    // - Senhas comuns
    // - Maiúsculas/minúsculas
    
    return { valid: true, message: 'Senha válida' };
}

// FUNÇÃO VULNERÁVEL - Rate limiting inadequado
function checkRateLimit(ip) {
    // Simulação de rate limiting muito permissivo
    const requests = global.requestCount || {};
    const now = Date.now();
    const windowMs = 60000; // 1 minuto
    
    if (!requests[ip]) {
        requests[ip] = { count: 1, resetTime: now + windowMs };
        global.requestCount = requests;
        return { allowed: true, remaining: 99 };
    }
    
    if (now > requests[ip].resetTime) {
        requests[ip] = { count: 1, resetTime: now + windowMs };
        return { allowed: true, remaining: 99 };
    }
    
    requests[ip].count++;
    
    // Limite muito alto - 100 requests por minuto
    if (requests[ip].count > 100) {
        return { allowed: false, remaining: 0 };
    }
    
    return { allowed: true, remaining: 100 - requests[ip].count };
}

// FUNÇÃO VULNERÁVEL - Validação de URL para SSRF
function validateUrl(url) {
    if (!url) {
        return { valid: false, message: 'URL é obrigatória' };
    }
    
    // Validação muito básica
    if (!url.startsWith('http://') && !url.startsWith('https://')) {
        return { valid: false, message: 'URL deve começar com http:// ou https://' };
    }
    
    // Não previne:
    // - file:// scheme
    // - localhost/127.0.0.1
    // - Internal IP ranges
    // - DNS rebinding
    // - Redirect chains
    
    return { valid: true, message: 'URL válida' };
}

// FUNÇÃO VULNERÁVEL - Parsing JSON inseguro
function parseJsonSafely(jsonString) {
    try {
        // Usa eval() em vez de JSON.parse() - EXTREMAMENTE perigoso
        return { success: true, data: eval('(' + jsonString + ')') };
    } catch (error) {
        return { success: false, error: error.message };
    }
}

// FUNÇÃO VULNERÁVEL - Logging com informações sensíveis
function logUserAction(user, action, data = {}) {
    const timestamp = new Date().toISOString();
    const logEntry = {
        timestamp,
        user: {
            id: user.id,
            username: user.username,
            email: user.email,
            password: user.password, // NUNCA logar senhas
            ip: user.ip,
            userAgent: user.userAgent
        },
        action,
        data,
        sensitive: {
            sessionToken: user.sessionToken,
            apiKey: user.apiKey,
            creditCard: user.creditCard // Dados extremamente sensíveis
        }
    };
    
    // Log em texto plano
    console.log('USER_ACTION:', JSON.stringify(logEntry, null, 2));
    
    return logEntry;
}

// FUNÇÃO VULNERÁVEL - Comparação de timing insegura
function comparePasswords(inputPassword, storedPassword) {
    // Comparação vulnerável a timing attacks
    return inputPassword === storedPassword;
    
    // Um atacante pode medir o tempo de resposta para
    // descobrir a senha caractere por caractere
}

// FUNÇÃO VULNERÁVEL - Geração de números aleatórios fracos
function generateRandomToken() {
    // Usa Math.random() - não criptograficamente seguro
    return Math.random().toString(36).substring(2, 15) + 
           Math.random().toString(36).substring(2, 15);
    
    // Previsível e pode ser explorado
}

// FUNÇÃO VULNERÁVEL - Validação de entrada permissiva demais
function sanitizeInput(input, type = 'string') {
    if (!input) return input;
    
    switch (type) {
        case 'number':
            // Não valida adequadamente
            return parseInt(input) || 0;
            
        case 'email':
            // Sanitização inadequada
            return input.trim().toLowerCase();
            
        case 'url':
            // Sem validação real
            return input.trim();
            
        default:
            // Apenas trim
            return input.trim();
    }
}

module.exports = {
    weakSanitize,
    validateEmail,
    weakHashPassword,
    preventSQLInjection,
    generateToken,
    validateUpload,
    escapeHtml,
    validatePassword,
    checkRateLimit,
    validateUrl,
    parseJsonSafely,
    logUserAction,
    comparePasswords,
    generateRandomToken,
    sanitizeInput
};

// Exemplos de uso vulnerável:
/*
console.log('=== Exemplos de Vulnerabilidades ===');

// XSS bypass
console.log('XSS bypass:', weakSanitize('<SCRIPT>alert("XSS")</SCRIPT>'));

// Email inválido aceito
console.log('Email inválido:', validateEmail('javascript:alert()@test.com'));

// Token previsível
console.log('Token previsível:', generateToken('admin'));

// SQL injection bypass
console.log('SQLi bypass:', preventSQLInjection("1 UNION SELECT * FROM users"));

// Upload perigoso permitido
console.log('Upload PHP:', validateUpload('shell.php', 'text/plain'));
*/