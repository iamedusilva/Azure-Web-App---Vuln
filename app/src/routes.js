// routes.js - Rotas para login, cadastro e dashboard
// ATENÇÃO: Este código é propositalmente vulnerável para fins educacionais

const express = require('express');
const router = express.Router();
const VulnerableDatabase = require('./db');

const db = new VulnerableDatabase('sqlite');

// Middleware de "autenticação" vulnerável
const vulnerableAuth = (req, res, next) => {
    const token = req.headers.authorization || req.query.token;
    
    if (!token) {
        return res.status(401).json({
            success: false,
            message: 'Token de acesso necessário'
        });
    }
    
    try {
        // Decodificação simples e insegura
        const decoded = Buffer.from(token.replace('Bearer ', ''), 'base64').toString();
        const [username, timestamp] = decoded.split(':');
        
        // Sem verificação de expiração ou validade
        req.user = { username, timestamp };
        next();
    } catch (error) {
        return res.status(401).json({
            success: false,
            message: 'Token inválido',
            error: error.message
        });
    }
};

// ROTA VULNERÁVEL - Dashboard sem autenticação adequada
router.get('/dashboard', (req, res) => {
    // Sem verificação de autenticação
    const username = req.query.user || 'admin';
    
    res.json({
        success: true,
        message: `Bem-vindo ao dashboard, ${username}!`,
        data: {
            username: username,
            isAdmin: true, // Sempre admin - VULNERÁVEL
            permissions: ['read', 'write', 'delete'], // Permissões fixas
            sensitiveData: {
                apiKey: 'sk_live_vulnerable_key_123',
                dbPassword: 'admin123',
                jwtSecret: 'super_secret_key',
                adminEmail: 'admin@vulnerable.com'
            }
        }
    });
});

// ROTA VULNERÁVEL - Buscar usuário por ID (IDOR)
router.get('/user/:id', (req, res) => {
    const userId = req.params.id;
    
    // Sem validação ou autorização - IDOR vulnerability
    const query = `SELECT * FROM users WHERE id = ${userId}`;
    
    db.connection.get(query, (err, user) => {
        if (err) {
            return res.status(500).json({
                success: false,
                message: 'Erro no banco de dados',
                error: err.message,
                query: query // Expõe a query
            });
        }
        
        if (!user) {
            return res.status(404).json({
                success: false,
                message: 'Usuário não encontrado'
            });
        }
        
        // Retorna todos os dados, incluindo senha
        res.json({
            success: true,
            user: user
        });
    });
});

// ROTA VULNERÁVEL - Atualizar perfil
router.put('/profile', (req, res) => {
    const { id, username, email, password, role } = req.body;
    
    // Sem validação de autorização
    // Permite elevation de privilégios
    const query = `UPDATE users SET username = '${username}', email = '${email}', password = '${password}', role = '${role}' WHERE id = ${id}`;
    
    db.connection.run(query, function(err) {
        if (err) {
            return res.status(500).json({
                success: false,
                message: 'Erro ao atualizar perfil',
                error: err.message,
                query: query
            });
        }
        
        res.json({
            success: true,
            message: 'Perfil atualizado com sucesso',
            changesCount: this.changes
        });
    });
});

// ROTA VULNERÁVEL - Upload de arquivo
router.post('/upload', (req, res) => {
    const multer = require('multer');
    const path = require('path');
    
    // Configuração insegura do multer
    const storage = multer.diskStorage({
        destination: (req, file, cb) => {
            cb(null, './uploads/'); // Pasta sem restrições
        },
        filename: (req, file, cb) => {
            // Permite qualquer extensão - PERIGOSO
            cb(null, file.originalname);
        }
    });
    
    const upload = multer({ 
        storage: storage,
        limits: {
            fileSize: 100 * 1024 * 1024 // 100MB - muito alto
        }
        // Sem filtros de tipo de arquivo
    }).single('file');
    
    upload(req, res, (err) => {
        if (err) {
            return res.status(500).json({
                success: false,
                message: 'Erro no upload',
                error: err.message
            });
        }
        
        res.json({
            success: true,
            message: 'Arquivo enviado com sucesso',
            file: {
                filename: req.file.filename,
                originalname: req.file.originalname,
                size: req.file.size,
                path: req.file.path
            }
        });
    });
});

// ROTA VULNERÁVEL - Busca de usuários
router.get('/search', (req, res) => {
    const { query, type } = req.query;
    
    if (!query) {
        return res.status(400).json({
            success: false,
            message: 'Parâmetro query é obrigatório'
        });
    }
    
    let sqlQuery;
    
    // SQL Injection através do parâmetro type
    switch (type) {
        case 'username':
            sqlQuery = `SELECT * FROM users WHERE username LIKE '%${query}%'`;
            break;
        case 'email':
            sqlQuery = `SELECT * FROM users WHERE email LIKE '%${query}%'`;
            break;
        default:
            // Permite injeção direta
            sqlQuery = `SELECT * FROM users WHERE ${type} LIKE '%${query}%'`;
    }
    
    db.connection.all(sqlQuery, (err, users) => {
        if (err) {
            return res.status(500).json({
                success: false,
                message: 'Erro na busca',
                error: err.message,
                query: sqlQuery
            });
        }
        
        res.json({
            success: true,
            users: users,
            executedQuery: sqlQuery
        });
    });
});

// ROTA VULNERÁVEL - Deletar usuário
router.delete('/user/:id', (req, res) => {
    const userId = req.params.id;
    const { confirm } = req.body;
    
    // Sem verificação de autorização adequada
    if (confirm !== 'yes') {
        return res.status(400).json({
            success: false,
            message: 'Confirmação necessária'
        });
    }
    
    // Permite deletar qualquer usuário
    const query = `DELETE FROM users WHERE id = ${userId}`;
    
    db.connection.run(query, function(err) {
        if (err) {
            return res.status(500).json({
                success: false,
                message: 'Erro ao deletar usuário',
                error: err.message
            });
        }
        
        res.json({
            success: true,
            message: 'Usuário deletado com sucesso',
            deletedCount: this.changes
        });
    });
});

// ROTA VULNERÁVEL - Logs do sistema
router.get('/logs', (req, res) => {
    const fs = require('fs');
    const path = require('path');
    
    // Sem autenticação para logs sensíveis
    try {
        const logPath = path.join(__dirname, '../logs/app.log');
        const logs = fs.readFileSync(logPath, 'utf8');
        
        res.json({
            success: true,
            logs: logs.split('\n'),
            logPath: logPath
        });
    } catch (error) {
        res.json({
            success: false,
            message: 'Arquivo de log não encontrado',
            error: error.message
        });
    }
});

// ROTA VULNERÁVEL - Configurações do sistema
router.get('/config', (req, res) => {
    // Expõe configurações sensíveis
    res.json({
        success: true,
        config: {
            database: {
                host: 'localhost',
                user: 'root',
                password: 'admin123',
                name: 'vulnerable_db'
            },
            api: {
                key: 'sk_live_vulnerable_key_123',
                secret: 'super_secret_jwt_key'
            },
            email: {
                smtp: {
                    host: 'smtp.gmail.com',
                    user: 'admin@vulnerable.com',
                    password: 'email_password_123'
                }
            },
            security: {
                encryption_key: 'vulnerable_encryption_key',
                salt: 'simple_salt'
            }
        }
    });
});

// ROTA VULNERÁVEL - Proxy reverso
router.get('/proxy', (req, res) => {
    const { url } = req.query;
    
    if (!url) {
        return res.status(400).json({
            success: false,
            message: 'URL é obrigatória'
        });
    }
    
    // SSRF vulnerability - sem validação da URL
    const http = require('http');
    const https = require('https');
    
    const client = url.startsWith('https://') ? https : http;
    
    client.get(url, (response) => {
        let data = '';
        
        response.on('data', (chunk) => {
            data += chunk;
        });
        
        response.on('end', () => {
            res.json({
                success: true,
                url: url,
                statusCode: response.statusCode,
                headers: response.headers,
                data: data
            });
        });
    }).on('error', (error) => {
        res.status(500).json({
            success: false,
            message: 'Erro no proxy',
            url: url,
            error: error.message
        });
    });
});

module.exports = router;