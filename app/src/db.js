// db.js - Conexão com banco de dados (SQLite/MySQL)
// ATENÇÃO: Este código é propositalmente vulnerável para fins educacionais

const sqlite3 = require('sqlite3').verbose();
const mysql = require('mysql2');

class VulnerableDatabase {
    constructor(type = 'sqlite') {
        this.type = type;
        this.connection = null;
    }

    // Conexão SQLite (vulnerável)
    connectSQLite() {
        this.connection = new sqlite3.Database('./vulnerable.db', (err) => {
            if (err) {
                console.error('Erro ao conectar com SQLite:', err);
            } else {
                console.log('Conectado ao SQLite');
                this.initializeTables();
            }
        });
    }

    // Conexão MySQL (vulnerável)
    connectMySQL() {
        this.connection = mysql.createConnection({
            host: 'localhost',
            user: 'root',
            password: '', // Senha vazia - VULNERÁVEL
            database: 'vulnerable_db',
            multipleStatements: true // VULNERÁVEL - permite múltiplas queries
        });

        this.connection.connect((err) => {
            if (err) {
                console.error('Erro ao conectar com MySQL:', err);
            } else {
                console.log('Conectado ao MySQL');
                this.initializeTables();
            }
        });
    }

    // Inicializar tabelas
    initializeTables() {
        const createUsersTable = `
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL,
                password TEXT NOT NULL,
                email TEXT,
                full_name TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        `;

        const createCommentsTable = `
            CREATE TABLE IF NOT EXISTS comments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                comment TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        `;

        if (this.type === 'sqlite') {
            this.connection.run(createUsersTable);
            this.connection.run(createCommentsTable);
            this.seedData();
        } else {
            this.connection.query(createUsersTable);
            this.connection.query(createCommentsTable);
            this.seedData();
        }
    }

    // Inserir dados iniciais
    seedData() {
        const users = [
            ['admin', 'admin123', 'admin@test.com', 'Administrador'],
            ['user1', 'password', 'user1@test.com', 'João Silva'],
            ['guest', '123456', 'guest@test.com', 'Visitante']
        ];

        users.forEach(user => {
            this.insertUser(user[0], user[1], user[2], user[3]);
        });

        const comments = [
            ['João', 'Ótimo sistema para aprender!'],
            ['Maria', 'As vulnerabilidades estão bem demonstradas'],
            ['Pedro', '<script>alert("XSS")</script>']
        ];

        comments.forEach(comment => {
            this.insertComment(comment[0], comment[1]);
        });
    }

    // MÉTODO VULNERÁVEL - SQL Injection
    authenticateUser(username, password) {
        return new Promise((resolve, reject) => {
            // Query vulnerável - concatenação direta
            const query = `SELECT * FROM users WHERE username = '${username}' AND password = '${password}'`;
            
            console.log('Query executada:', query); // Log para demonstração
            
            if (this.type === 'sqlite') {
                this.connection.get(query, (err, row) => {
                    if (err) {
                        reject(err);
                    } else {
                        resolve(row);
                    }
                });
            } else {
                this.connection.query(query, (err, results) => {
                    if (err) {
                        reject(err);
                    } else {
                        resolve(results[0]);
                    }
                });
            }
        });
    }

    // MÉTODO VULNERÁVEL - SQL Injection no registro
    insertUser(username, password, email, fullName) {
        return new Promise((resolve, reject) => {
            // Query vulnerável - sem sanitização
            const query = `INSERT INTO users (username, password, email, full_name) VALUES ('${username}', '${password}', '${email}', '${fullName}')`;
            
            console.log('Query de inserção:', query);
            
            if (this.type === 'sqlite') {
                this.connection.run(query, function(err) {
                    if (err) {
                        reject(err);
                    } else {
                        resolve({ id: this.lastID });
                    }
                });
            } else {
                this.connection.query(query, (err, results) => {
                    if (err) {
                        reject(err);
                    } else {
                        resolve({ id: results.insertId });
                    }
                });
            }
        });
    }

    // MÉTODO VULNERÁVEL - XSS nos comentários
    insertComment(name, comment) {
        return new Promise((resolve, reject) => {
            // Não há sanitização dos dados - vulnerável a XSS
            const query = `INSERT INTO comments (name, comment) VALUES ('${name}', '${comment}')`;
            
            if (this.type === 'sqlite') {
                this.connection.run(query, function(err) {
                    if (err) {
                        reject(err);
                    } else {
                        resolve({ id: this.lastID });
                    }
                });
            } else {
                this.connection.query(query, (err, results) => {
                    if (err) {
                        reject(err);
                    } else {
                        resolve({ id: results.insertId });
                    }
                });
            }
        });
    }

    // Buscar comentários (sem sanitização)
    getComments() {
        return new Promise((resolve, reject) => {
            const query = 'SELECT * FROM comments ORDER BY created_at DESC';
            
            if (this.type === 'sqlite') {
                this.connection.all(query, (err, rows) => {
                    if (err) {
                        reject(err);
                    } else {
                        resolve(rows);
                    }
                });
            } else {
                this.connection.query(query, (err, results) => {
                    if (err) {
                        reject(err);
                    } else {
                        resolve(results);
                    }
                });
            }
        });
    }

    // Buscar todos os usuários (informação sensível)
    getAllUsers() {
        return new Promise((resolve, reject) => {
            // Query perigosa - expõe senhas
            const query = 'SELECT id, username, password, email, full_name FROM users';
            
            if (this.type === 'sqlite') {
                this.connection.all(query, (err, rows) => {
                    if (err) {
                        reject(err);
                    } else {
                        resolve(rows);
                    }
                });
            } else {
                this.connection.query(query, (err, results) => {
                    if (err) {
                        reject(err);
                    } else {
                        resolve(results);
                    }
                });
            }
        });
    }

    // Fechar conexão
    close() {
        if (this.connection) {
            if (this.type === 'sqlite') {
                this.connection.close();
            } else {
                this.connection.end();
            }
        }
    }
}

// Exemplos de uso vulnerável:
/*
const db = new VulnerableDatabase('sqlite');
db.connectSQLite();

// Exemplo de SQL Injection
db.authenticateUser("admin' OR '1'='1'--", "qualquer_coisa")
  .then(result => console.log('Login com SQLi:', result))
  .catch(err => console.error(err));

// Exemplo de XSS
db.insertComment("Hacker", "<script>alert('XSS')</script>")
  .then(() => console.log('Comentário XSS inserido'))
  .catch(err => console.error(err));
*/

module.exports = VulnerableDatabase;