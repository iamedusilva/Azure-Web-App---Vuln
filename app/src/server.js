// server.js - Servidor principal (Express.js/Node.js)
// ATENÇÃO: Este código é propositalmente vulnerável para fins educacionais

const express = require('express');
const cors = require('cors');
const path = require('path');
const VulnerableDatabase = require('./db');

const app = express();
const PORT = process.env.PORT || 3000;

// Inicializar banco de dados
const db = new VulnerableDatabase('sqlite');
db.connectSQLite();

// Middlewares vulneráveis
app.use(cors()); // CORS muito permissivo
app.use(express.json()); 
app.use(express.urlencoded({ extended: true }));

// Servir arquivos estáticos
app.use(express.static(path.join(__dirname, '../public')));

// VULNERABILIDADE: Headers de segurança ausentes
// Não há CSP, X-Frame-Options, etc.

// VULNERABILIDADE: Logs excessivos
app.use((req, res, next) => {
    console.log(`${new Date().toISOString()} - ${req.method} ${req.url}`);
    console.log('Headers:', req.headers);
    console.log('Body:', req.body);
    next();
});

// Rota principal
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, '../public/index.html'));
});

// ROTA VULNERÁVEL - Login com SQL Injection
app.post('/api/login', async (req, res) => {
    try {
        const { username, password } = req.body;
        
        // Log das credenciais (VULNERÁVEL)
        console.log(`Tentativa de login: ${username}:${password}`);
        
        // Autenticação vulnerável
        const user = await db.authenticateUser(username, password);
        
        if (user) {
            // Token de sessão simples (VULNERÁVEL)
            const token = Buffer.from(`${username}:${Date.now()}`).toString('base64');
            
            res.json({
                success: true,
                message: 'Login realizado com sucesso',
                user: {
                    id: user.id,
                    username: user.username,
                    email: user.email,
                    full_name: user.full_name
                },
                token: token
            });
        } else {
            res.status(401).json({
                success: false,
                message: 'Credenciais inválidas'
            });
        }
    } catch (error) {
        // Exposição de erros internos (VULNERÁVEL)
        console.error('Erro no login:', error);
        res.status(500).json({
            success: false,
            message: 'Erro interno do servidor',
            error: error.message, // NUNCA expor erro real em produção
            stack: error.stack     // EXTREMAMENTE vulnerável
        });
    }
});

// ROTA VULNERÁVEL - Registro com SQL Injection
app.post('/api/register', async (req, res) => {
    try {
        const { username, password, email, fullname } = req.body;
        
        // Validação inexistente (VULNERÁVEL)
        if (!username || !password) {
            return res.status(400).json({
                success: false,
                message: 'Username e password são obrigatórios'
            });
        }
        
        // Inserir usuário sem sanitização
        const result = await db.insertUser(username, password, email, fullname);
        
        res.json({
            success: true,
            message: 'Usuário cadastrado com sucesso',
            userId: result.id
        });
        
    } catch (error) {
        console.error('Erro no registro:', error);
        res.status(500).json({
            success: false,
            message: 'Erro ao cadastrar usuário',
            error: error.message
        });
    }
});

// ROTA VULNERÁVEL - Comentários com XSS
app.post('/api/comments', async (req, res) => {
    try {
        const { name, comment } = req.body;
        
        // Sem validação ou sanitização (VULNERÁVEL)
        await db.insertComment(name, comment);
        
        res.json({
            success: true,
            message: 'Comentário adicionado com sucesso'
        });
        
    } catch (error) {
        console.error('Erro ao adicionar comentário:', error);
        res.status(500).json({
            success: false,
            message: 'Erro ao adicionar comentário',
            error: error.message
        });
    }
});

// ROTA VULNERÁVEL - Buscar comentários
app.get('/api/comments', async (req, res) => {
    try {
        const comments = await db.getComments();
        
        // Retorna comentários sem sanitizar (XSS)
        res.json({
            success: true,
            comments: comments
        });
        
    } catch (error) {
        console.error('Erro ao buscar comentários:', error);
        res.status(500).json({
            success: false,
            message: 'Erro ao buscar comentários',
            error: error.message
        });
    }
});

// ROTA EXTREMAMENTE VULNERÁVEL - Buscar todos os usuários
app.get('/api/users', async (req, res) => {
    try {
        // Sem autenticação ou autorização
        const users = await db.getAllUsers();
        
        res.json({
            success: true,
            users: users // Expõe senhas em texto plano
        });
        
    } catch (error) {
        console.error('Erro ao buscar usuários:', error);
        res.status(500).json({
            success: false,
            message: 'Erro ao buscar usuários',
            error: error.message
        });
    }
});

// ROTA VULNERÁVEL - Informações do servidor
app.get('/api/server-info', (req, res) => {
    res.json({
        success: true,
        info: {
            node_version: process.version,
            platform: process.platform,
            uptime: process.uptime(),
            memory_usage: process.memoryUsage(),
            env: process.env, // EXTREMAMENTE perigoso
            pwd: process.cwd(),
            pid: process.pid
        }
    });
});

// ROTA VULNERÁVEL - Executar comandos (RCE)
app.post('/api/debug', (req, res) => {
    const { command } = req.body;
    
    if (!command) {
        return res.status(400).json({
            success: false,
            message: 'Comando é obrigatório'
        });
    }
    
    // EXTREMAMENTE PERIGOSO - Remote Code Execution
    const { exec } = require('child_process');
    
    exec(command, (error, stdout, stderr) => {
        res.json({
            success: !error,
            command: command,
            stdout: stdout,
            stderr: stderr,
            error: error ? error.message : null
        });
    });
});

// ROTA VULNERÁVEL - Path Traversal
app.get('/api/files/*', (req, res) => {
    const filePath = req.params[0];
    const fs = require('fs');
    
    // Path traversal vulnerável
    const fullPath = path.join(__dirname, '../../', filePath);
    
    try {
        const content = fs.readFileSync(fullPath, 'utf8');
        res.json({
            success: true,
            path: fullPath,
            content: content
        });
    } catch (error) {
        res.status(404).json({
            success: false,
            message: 'Arquivo não encontrado',
            path: fullPath,
            error: error.message
        });
    }
});

// Middleware de erro global (VULNERÁVEL)
app.use((error, req, res, next) => {
    console.error('Erro global:', error);
    res.status(500).json({
        success: false,
        message: 'Erro interno do servidor',
        error: error.message,
        stack: error.stack, // Expõe stack trace completo
        request: {
            url: req.url,
            method: req.method,
            headers: req.headers,
            body: req.body
        }
    });
});

// Rota 404
app.use('*', (req, res) => {
    res.status(404).json({
        success: false,
        message: 'Endpoint não encontrado',
        available_endpoints: [
            'POST /api/login',
            'POST /api/register', 
            'GET /api/comments',
            'POST /api/comments',
            'GET /api/users',
            'GET /api/server-info',
            'POST /api/debug',
            'GET /api/files/*'
        ]
    });
});

// Iniciar servidor
app.listen(PORT, () => {
    console.log(`🚨 Servidor vulnerável rodando na porta ${PORT}`);
    console.log(`⚠️  ATENÇÃO: Este servidor contém vulnerabilidades intencionais!`);
    console.log(`📖 Acesse http://localhost:${PORT} para começar`);
});

// Tratar encerramento
process.on('SIGINT', () => {
    console.log('\nEncerrando servidor...');
    db.close();
    process.exit(0);
});

module.exports = app;