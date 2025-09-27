'use client'

import { useState } from 'react'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Alert, AlertDescription } from '@/components/ui/alert'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Textarea } from '@/components/ui/textarea'
import Link from 'next/link'

export default function VulnerableDemo() {
  const [loginResult, setLoginResult] = useState('')
  const [searchResult, setSearchResult] = useState('')
  const [commentResult, setCommentResult] = useState('')
  const [comments, setComments] = useState([
    { id: 1, author: 'Admin', content: 'Bem-vindo ao site de demonstração!' },
    { id: 2, author: 'User1', content: 'Este site tem vulnerabilidades propositalmente.' }
  ])

  const handleVulnerableLogin = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault()
    const formData = new FormData(e.currentTarget)
    const username = formData.get('username') as string
    const password = formData.get('password') as string

    try {
      // VULNERABLE: SQL Injection - Construindo query diretamente com input do usuário
      const response = await fetch('/api/vulnerable-login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password })
      })
      
      const result = await response.json()
      setLoginResult(result.message || result.error)
    } catch (error) {
      setLoginResult('Erro ao conectar com o servidor')
    }
  }

  const handleVulnerableSearch = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault()
    const formData = new FormData(e.currentTarget)
    const searchTerm = formData.get('search') as string

    try {
      // VULNERABLE: XSS Refletido - Renderizando input do usuário diretamente
      const response = await fetch('/api/vulnerable-search', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ search: searchTerm })
      })
      
      const result = await response.json()
      setSearchResult(result.result)
    } catch (error) {
      setSearchResult('Erro ao realizar busca')
    }
  }

  const handleVulnerableComment = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault()
    const formData = new FormData(e.currentTarget)
    const author = formData.get('author') as string
    const content = formData.get('content') as string

    try {
      // VULNERABLE: XSS Armazenado - Salvando e renderizando input malicioso
      const response = await fetch('/api/vulnerable-comment', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ author, content })
      })
      
      const result = await response.json()
      if (result.success) {
        const newComment = {
          id: comments.length + 1,
          author: author,
          content: content
        }
        setComments([...comments, newComment])
        setCommentResult('Comentário adicionado com sucesso!')
        e.currentTarget.reset()
      } else {
        setCommentResult(result.error || 'Erro ao adicionar comentário')
      }
    } catch (error) {
      setCommentResult('Erro ao conectar com o servidor')
    }
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-red-50 to-orange-50 p-4">
      <div className="max-w-6xl mx-auto">
        {/* Aviso Educacional */}
        <Alert className="mb-8 border-red-500 bg-red-50">
          <AlertDescription className="text-red-800 font-semibold">
            ⚠️ AVISO EDUCACIONAL: Este site contém vulnerabilidades de segurança intencionais (SQL Injection e XSS) 
            para fins de aprendizado acadêmico. NUNCA use estas técnicas em sistemas reais ou sem autorização.
          </AlertDescription>
        </Alert>

        <div className="text-center mb-8">
          <h1 className="text-4xl font-bold text-red-800 mb-2">
            Site Vulnerável - Demonstração de Segurança
          </h1>
          <p className="text-gray-600 mb-4">
            Projeto educacional para demonstrar vulnerabilidades comuns em aplicações web
          </p>
          <Link href="/educational">
            <Button variant="outline" className="border-blue-500 text-blue-700 hover:bg-blue-50">
              📚 Ver Guia Educacional Completo
            </Button>
          </Link>
        </div>

        <Tabs defaultValue="login" className="w-full">
          <TabsList className="grid w-full grid-cols-3 mb-8">
            <TabsTrigger value="login">Login (SQL Injection)</TabsTrigger>
            <TabsTrigger value="search">Busca (XSS Refletido)</TabsTrigger>
            <TabsTrigger value="comments">Comentários (XSS Armazenado)</TabsTrigger>
          </TabsList>

          <TabsContent value="login">
            <Card className="max-w-md mx-auto">
              <CardHeader>
                <CardTitle className="text-red-700">Login Vulnerável</CardTitle>
                <CardDescription>
                  Demonstra vulnerabilidade de SQL Injection. Tente: admin' OR '1'='1
                </CardDescription>
              </CardHeader>
              <CardContent>
                <form onSubmit={handleVulnerableLogin} className="space-y-4">
                  <div className="space-y-2">
                    <Label htmlFor="username">Usuário</Label>
                    <Input 
                      id="username" 
                      name="username" 
                      placeholder="Digite o usuário" 
                      required 
                    />
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="password">Senha</Label>
                    <Input 
                      id="password" 
                      name="password" 
                      type="password" 
                      placeholder="Digite a senha" 
                      required 
                    />
                  </div>
                  <Button type="submit" className="w-full bg-red-600 hover:bg-red-700">
                    Entrar
                  </Button>
                </form>
                {loginResult && (
                  <Alert className="mt-4">
                    <AlertDescription>{loginResult}</AlertDescription>
                  </Alert>
                )}
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="search">
            <Card className="max-w-md mx-auto">
              <CardHeader>
                <CardTitle className="text-red-700">Busca Vulnerável</CardTitle>
                <CardDescription>
                  Demonstra XSS Refletido. Tente: &lt;script&gt;alert('XSS')&lt;/script&gt;
                </CardDescription>
              </CardHeader>
              <CardContent>
                <form onSubmit={handleVulnerableSearch} className="space-y-4">
                  <div className="space-y-2">
                    <Label htmlFor="search">Buscar</Label>
                    <Input 
                      id="search" 
                      name="search" 
                      placeholder="Digite sua busca" 
                      required 
                    />
                  </div>
                  <Button type="submit" className="w-full bg-red-600 hover:bg-red-700">
                    Buscar
                  </Button>
                </form>
                {searchResult && (
                  <div className="mt-4 p-4 bg-gray-50 rounded-md">
                    <h4 className="font-semibold mb-2">Resultado da busca:</h4>
                    <div dangerouslySetInnerHTML={{ __html: searchResult }} />
                  </div>
                )}
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="comments">
            <div className="grid md:grid-cols-2 gap-6">
              <Card>
                <CardHeader>
                  <CardTitle className="text-red-700">Adicionar Comentário</CardTitle>
                  <CardDescription>
                    Demonstra XSS Armazenado. Tente: &lt;img src=x onerror=alert('XSS')&gt;
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <form onSubmit={handleVulnerableComment} className="space-y-4">
                    <div className="space-y-2">
                      <Label htmlFor="author">Autor</Label>
                      <Input 
                        id="author" 
                        name="author" 
                        placeholder="Seu nome" 
                        required 
                      />
                    </div>
                    <div className="space-y-2">
                      <Label htmlFor="content">Comentário</Label>
                      <Textarea 
                        id="content" 
                        name="content" 
                        placeholder="Seu comentário" 
                        required 
                      />
                    </div>
                    <Button type="submit" className="w-full bg-red-600 hover:bg-red-700">
                      Comentar
                    </Button>
                  </form>
                  {commentResult && (
                    <Alert className="mt-4">
                      <AlertDescription>{commentResult}</AlertDescription>
                    </Alert>
                  )}
                </CardContent>
              </Card>

              <Card>
                <CardHeader>
                  <CardTitle>Comentários</CardTitle>
                  <CardDescription>Comentários existentes (vulneráveis a XSS)</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4 max-h-96 overflow-y-auto">
                    {comments.map((comment) => (
                      <div key={comment.id} className="p-3 bg-gray-50 rounded-md">
                        <div className="font-semibold text-sm text-gray-700 mb-1">
                          {comment.author}:
                        </div>
                        <div 
                          className="text-gray-600"
                          dangerouslySetInnerHTML={{ __html: comment.content }}
                        />
                      </div>
                    ))}
                  </div>
                </CardContent>
              </Card>
            </div>
          </TabsContent>
        </Tabs>

        {/* Informações Educacionais */}
        <Card className="mt-8">
          <CardHeader>
            <CardTitle className="text-blue-700">Informações Educacionais</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div>
              <h3 className="font-semibold text-red-600">SQL Injection</h3>
              <p className="text-sm text-gray-600">
                Permite que invasores manipulem queries SQL através de inputs maliciosos. 
                Pode resultar em acesso não autorizado, roubo de dados ou destruição do banco.
              </p>
            </div>
            <div>
              <h3 className="font-semibold text-red-600">XSS (Cross-Site Scripting)</h3>
              <p className="text-sm text-gray-600">
                Permite que invasores injetem scripts maliciosos em páginas vistas por outros usuários. 
                Pode roubar cookies, sessões ou redirecionar usuários para sites maliciosos.
              </p>
            </div>
            <div>
              <h3 className="font-semibold text-green-600">Como Proteger</h3>
              <ul className="text-sm text-gray-600 list-disc list-inside">
                <li>Use parameterized queries ou prepared statements</li>
                <li>Valide e sanitize todos os inputs do usuário</li>
                <li>Use output encoding (HTML, JavaScript, URL)</li>
                <li>Implemente Content Security Policy (CSP)</li>
                <li>Use frameworks com proteções built-in</li>
              </ul>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}