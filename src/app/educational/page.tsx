'use client'

import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Alert, AlertDescription } from '@/components/ui/alert'
import { Badge } from '@/components/ui/badge'

export default function EducationalPage() {
  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 p-4">
      <div className="max-w-6xl mx-auto">
        {/* Aviso Principal */}
        <Alert className="mb-8 border-red-500 bg-red-50">
          <AlertDescription className="text-red-800 font-semibold text-lg">
            ⚠️ AVISO EDUCACIONAL: Esta página contém informações detalhadas sobre vulnerabilidades de segurança 
            para fins acadêmicos. Todas as técnicas demonstradas devem ser usadas apenas em ambientes controlados 
            e com permissão explícita.
          </AlertDescription>
        </Alert>

        <div className="text-center mb-8">
          <h1 className="text-4xl font-bold text-blue-800 mb-2">
            Guia Educacional de Vulnerabilidades Web
          </h1>
          <p className="text-gray-600 text-lg">
            Aprenda sobre as vulnerabilidades mais comuns e como protegê-las
          </p>
        </div>

        <div className="grid md:grid-cols-2 gap-8 mb-8">
          {/* SQL Injection Section */}
          <Card>
            <CardHeader>
              <CardTitle className="text-red-700 flex items-center gap-2">
                SQL Injection
                <Badge variant="destructive">CRÍTICO</Badge>
              </CardTitle>
              <CardDescription>
                Uma das vulnerabilidades mais perigosas em aplicações web
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div>
                <h3 className="font-semibold text-red-600 mb-2">O que é?</h3>
                <p className="text-sm text-gray-700">
                  SQL Injection ocorre quando um invasor consegue inserir ou "injetar" código SQL malicioso 
                  em campos de entrada, manipulando as queries do banco de dados.
                </p>
              </div>

              <div>
                <h3 className="font-semibold text-red-600 mb-2">Exemplo de Ataque</h3>
                <div className="bg-gray-100 p-3 rounded-md font-mono text-sm">
                  <p><strong>Input normal:</strong> admin</p>
                  <p><strong>Query gerada:</strong> SELECT * FROM users WHERE username = 'admin'</p>
                  <br />
                  <p><strong>Input malicioso:</strong> admin' OR '1'='1</p>
                  <p><strong>Query vulnerável:</strong> SELECT * FROM users WHERE username = 'admin' OR '1'='1'</p>
                </div>
              </div>

              <div>
                <h3 className="font-semibold text-red-600 mb-2">Impactos</h3>
                <ul className="text-sm text-gray-700 list-disc list-inside">
                  <li>Acesso não autorizado ao banco de dados</li>
                  <li>Roubo de dados sensíveis</li>
                  <li>Modificação ou exclusão de dados</li>
                  <li>Elevação de privilégios</li>
                  <li>Comprometimento completo do sistema</li>
                </ul>
              </div>

              <div>
                <h3 className="font-semibold text-green-600 mb-2">Como Proteger</h3>
                <ul className="text-sm text-gray-700 list-disc list-inside">
                  <li>Use parameterized queries ou prepared statements</li>
                  <li>Use ORM (Object-Relational Mapping) frameworks</li>
                  <li>Valide e sanitize todos os inputs</li>
                  <li>Use princípio de menor privilégio no banco</li>
                  <li>Implemente WAF (Web Application Firewall)</li>
                </ul>
              </div>
            </CardContent>
          </Card>

          {/* XSS Section */}
          <Card>
            <CardHeader>
              <CardTitle className="text-red-700 flex items-center gap-2">
                Cross-Site Scripting (XSS)
                <Badge variant="destructive">ALTO</Badge>
              </CardTitle>
              <CardDescription>
                Permite execução de scripts maliciosos no navegador de vítimas
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div>
                <h3 className="font-semibold text-red-600 mb-2">O que é?</h3>
                <p className="text-sm text-gray-700">
                  XSS ocorre quando um invasor consegue injetar código JavaScript malicioso em páginas 
                  web que serão executadas no navegador de outros usuários.
                </p>
              </div>

              <div>
                <h3 className="font-semibold text-red-600 mb-2">Tipos de XSS</h3>
                <div className="space-y-2">
                  <div className="bg-gray-100 p-2 rounded-md">
                    <p className="font-semibold text-sm"><strong>Refletido:</strong> Executado imediatamente via URL/parâmetro</p>
                  </div>
                  <div className="bg-gray-100 p-2 rounded-md">
                    <p className="font-semibold text-sm"><strong>Armazenado:</strong> Salvo no servidor e executado para todos</p>
                  </div>
                  <div className="bg-gray-100 p-2 rounded-md">
                    <p className="font-semibold text-sm"><strong>DOM-based:</strong> Executado no lado do cliente</p>
                  </div>
                </div>
              </div>

              <div>
                <h3 className="font-semibold text-red-600 mb-2">Exemplos de Ataque</h3>
                <div className="bg-gray-100 p-3 rounded-md font-mono text-sm">
                  <p><strong>Script tag:</strong> &lt;script&gt;alert('XSS')&lt;/script&gt;</p>
                  <p><strong>Image tag:</strong> &lt;img src=x onerror=alert('XSS')&gt;</p>
                  <p><strong>Event handler:</strong> &lt;div onclick="alert('XSS')"&gt;Clique aqui&lt;/div&gt;</p>
                </div>
              </div>

              <div>
                <h3 className="font-semibold text-green-600 mb-2">Como Proteger</h3>
                <ul className="text-sm text-gray-700 list-disc list-inside">
                  <li>Use output encoding (HTML, JavaScript, URL)</li>
                  <li>Implemente Content Security Policy (CSP)</li>
                  <li>Valide e sanitize inputs do usuário</li>
                  <li>Use frameworks com proteções built-in</li>
                  <li>Evite usar dangerouslySetInnerHTML</li>
                </ul>
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Best Practices Section */}
        <Card className="mb-8">
          <CardHeader>
            <CardTitle className="text-green-700">Melhores Práticas de Segurança</CardTitle>
            <CardDescription>
              Princípios fundamentais para desenvolver aplicações web seguras
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="grid md:grid-cols-3 gap-6">
              <div>
                <h3 className="font-semibold text-blue-600 mb-3">Validação de Input</h3>
                <ul className="text-sm text-gray-700 space-y-1">
                  <li>• Nunca confie em input do usuário</li>
                  <li>• Valide tipo, formato e tamanho</li>
                  <li>• Use allow-list em vez de block-list</li>
                  <li>• Implemente validação no cliente e servidor</li>
                </ul>
              </div>
              
              <div>
                <h3 className="font-semibold text-blue-600 mb-3">Princípio do Mínimo Privilégio</h3>
                <ul className="text-sm text-gray-700 space-y-1">
                  <li>• Conceda apenas permissões necessárias</li>
                  <li>• Use contas de serviço com privilégios limitados</li>
                  <li>• Implemente RBAC (Role-Based Access Control)</li>
                  <li>• Restrinja acesso a recursos sensíveis</li>
                </ul>
              </div>
              
              <div>
                <h3 className="font-semibold text-blue-600 mb-3">Segurança em Camadas</h3>
                <ul className="text-sm text-gray-700 space-y-1">
                  <li>• Implemente múltiplas camadas de defesa</li>
                  <li>• Use WAF, IDS/IPS</li>
                  <li>• Mantenha sistemas atualizados</li>
                  <li>• Monitore e registre atividades suspeitas</li>
                </ul>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Tools and Resources */}
        <Card>
          <CardHeader>
            <CardTitle className="text-purple-700">Ferramentas e Recursos</CardTitle>
            <CardDescription>
              Ferramentas úteis para testar e melhorar a segurança
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="grid md:grid-cols-2 gap-6">
              <div>
                <h3 className="font-semibold text-purple-600 mb-3">Ferramentas de Teste</h3>
                <ul className="text-sm text-gray-700 space-y-1">
                  <li>• OWASP ZAP - Ferramenta gratuita de testes</li>
                  <li>• Burp Suite - Suite profissional de segurança</li>
                  <li>• SQLMap - Ferramenta especializada em SQLi</li>
                  <li>• XSStrike - Ferramenta para detectar XSS</li>
                  <li>• Nmap - Scanner de rede e portas</li>
                </ul>
              </div>
              
              <div>
                <h3 className="font-semibold text-purple-600 mb-3">Recursos de Aprendizado</h3>
                <ul className="text-sm text-gray-700 space-y-1">
                  <li>• OWASP Top 10 - Lista das vulnerabilidades mais críticas</li>
                  <li>• PortSwigger Web Security Academy</li>
                  <li>• Hack The Box - Plataforma de prática</li>
                  <li>• TryHackMe - Cursos interativos</li>
                  <li>• OWASP Cheat Sheet Series</li>
                </ul>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Final Warning */}
        <Alert className="mt-8 border-orange-500 bg-orange-50">
          <AlertDescription className="text-orange-800">
            <strong>Lembre-se:</strong> Este conhecimento deve ser usado apenas para fins educacionais e éticos. 
            Testar vulnerabilidades em sistemas sem permissão é ilegal e pode resultar em consequências legais sérias. 
            Sempre obtenha autorização explícita antes de realizar qualquer teste de segurança.
          </AlertDescription>
        </Alert>
      </div>
    </div>
  )
}