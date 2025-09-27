import { NextRequest, NextResponse } from 'next/server'

// Simulação de armazenamento de comentários (em memória)
let comments = [
  { id: 1, author: 'Admin', content: 'Bem-vindo ao site de demonstração!' },
  { id: 2, author: 'User1', content: 'Este site tem vulnerabilidades propositalmente.' }
]

export async function POST(request: NextRequest) {
  try {
    const { author, content } = await request.json()

    // VULNERABLE: XSS Armazenado - Salvando input do usuário sem sanitização
    // O conteúdo malicioso será armazenado e renderizado posteriormente
    
    console.log('Novo comentário recebido:', { author, content })
    
    // VULNERABLE: Armazenando o conteúdo diretamente sem validação ou sanitização
    const newComment = {
      id: comments.length + 1,
      author: author,
      content: content, // Conteúdo não sanitizado - vulnerável a XSS
      timestamp: new Date().toISOString()
    }
    
    comments.push(newComment)
    
    // VULNERABLE: Retornando sucesso sem validar o conteúdo
    return NextResponse.json({
      success: true,
      message: 'Comentário adicionado com sucesso!',
      comment: newComment
    })
    
  } catch (error) {
    return NextResponse.json({
      success: false,
      error: 'Erro ao adicionar comentário',
      details: error instanceof Error ? error.message : 'Erro desconhecido'
    }, { status: 500 })
  }
}

export async function GET() {
  try {
    // VULNERABLE: Retornando todos os comentários sem sanitização
    // O frontend renderizará estes comentários usando dangerouslySetInnerHTML
    return NextResponse.json({
      comments: comments,
      total: comments.length
    })
    
  } catch (error) {
    return NextResponse.json({
      error: 'Erro ao buscar comentários',
      details: error instanceof Error ? error.message : 'Erro desconhecido'
    }, { status: 500 })
  }
}