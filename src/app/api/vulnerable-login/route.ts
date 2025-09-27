import { NextRequest, NextResponse } from 'next/server'

// Simulação de banco de dados vulnerável
const users = [
  { id: 1, username: 'admin', password: 'admin123', role: 'administrator' },
  { id: 2, username: 'user', password: 'user123', role: 'user' },
  { id: 3, username: 'guest', password: 'guest123', role: 'guest' }
]

export async function POST(request: NextRequest) {
  try {
    const { username, password } = await request.json()

    // VULNERABLE: SQL Injection - Construindo query diretamente com input do usuário
    // Em um cenário real, isso seria executado no banco de dados
    // Aqui simulamos a vulnerabilidade construindo uma query string insegura
    
    const vulnerableQuery = `SELECT * FROM users WHERE username = '${username}' AND password = '${password}'`
    
    console.log('Query vulnerável executada:', vulnerableQuery)
    
    // Simulação da execução da query vulnerável
    // Isso demonstra como SQL Injection funcionaria
    let queryResult = null
    
    // Lógica vulnerável que simula SQL injection
    if (username.includes("' OR '1'='1") || username.includes("' OR 1=1--")) {
      // SQL Injection bem-sucedido - retorna todos os usuários
      queryResult = users
    } else if (username.includes("' OR '1'='2")) {
      // SQL Injection que falha
      queryResult = []
    } else {
      // Busca normal
      queryResult = users.filter(user => 
        user.username === username && user.password === password
      )
    }
    
    if (queryResult && queryResult.length > 0) {
      const user = queryResult[0]
      return NextResponse.json({
        message: `Login bem-sucedido! Bem-vindo ${user.username} (${user.role})`,
        user: { username: user.username, role: user.role },
        query: vulnerableQuery
      })
    } else {
      return NextResponse.json({
        error: 'Usuário ou senha inválidos',
        query: vulnerableQuery
      }, { status: 401 })
    }
    
  } catch (error) {
    return NextResponse.json({
      error: 'Erro no servidor',
      details: error instanceof Error ? error.message : 'Erro desconhecido'
    }, { status: 500 })
  }
}