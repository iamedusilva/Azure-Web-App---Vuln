import { NextRequest, NextResponse } from 'next/server'

// Simulação de dados para busca
const mockData = [
  { title: 'Produto A', description: 'Descrição do produto A' },
  { title: 'Produto B', description: 'Descrição do produto B' },
  { title: 'Produto C', description: 'Descrição do produto C' },
  { title: 'Serviço X', description: 'Descrição do serviço X' },
  { title: 'Serviço Y', description: 'Descrição do serviço Y' }
]

export async function POST(request: NextRequest) {
  try {
    const { search } = await request.json()

    // VULNERABLE: XSS Refletido - Retornando o input do usuário sem sanitização
    // O input será renderizado diretamente no frontend usando dangerouslySetInnerHTML
    
    console.log('Termo de busca recebido:', search)
    
    // Simulação de busca
    const results = mockData.filter(item => 
      item.title.toLowerCase().includes(search.toLowerCase()) ||
      item.description.toLowerCase().includes(search.toLowerCase())
    )
    
    // VULNERABLE: Construindo o resultado HTML diretamente com o input do usuário
    let resultHTML = ''
    
    if (results.length > 0) {
      resultHTML = `<p>Resultados para "<strong>${search}</strong>":</p><ul>`
      results.forEach(item => {
        resultHTML += `<li><strong>${item.title}</strong>: ${item.description}</li>`
      })
      resultHTML += '</ul>'
    } else {
      resultHTML = `<p>Nenhum resultado encontrado para "<strong>${search}</strong>".</p>`
    }
    
    // VULNERABLE: Retornando HTML não sanitizado que será renderizado diretamente
    return NextResponse.json({
      result: resultHTML,
      searchTerm: search,
      found: results.length
    })
    
  } catch (error) {
    return NextResponse.json({
      error: 'Erro ao processar busca',
      details: error instanceof Error ? error.message : 'Erro desconhecido'
    }, { status: 500 })
  }
}