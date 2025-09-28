@echo off
echo ================================================
echo SCRIPT DE CONFIGURACAO - APLICACAO VULNERAVEL
echo ================================================
echo.
echo ATENCAO: Esta aplicacao e propositalmente vulneravel!
echo Use apenas para fins educacionais e em ambiente isolado.
echo.

REM Verificar se MySQL está rodando
echo [1/4] Verificando se MySQL esta rodando...
netstat -an | find "3306" >nul
if %errorlevel%==0 (
    echo ✓ MySQL detectado na porta 3306
) else (
    echo ❌ MySQL nao detectado. Certifique-se de que o XAMPP MySQL esta rodando.
    echo    Abra o XAMPP Control Panel e inicie o MySQL
    pause
    exit /b 1
)

REM Verificar se Apache está rodando
echo.
echo [2/4] Verificando se Apache esta rodando...
netstat -an | find "80" >nul
if %errorlevel%==0 (
    echo ✓ Apache detectado na porta 80
) else (
    echo ❌ Apache nao detectado. Certifique-se de que o XAMPP Apache esta rodando.
    echo    Abra o XAMPP Control Panel e inicie o Apache
    pause
    exit /b 1
)

REM Tentar criar o banco de dados
echo.
echo [3/4] Criando banco de dados...
echo Esta etapa pode solicitar a senha do MySQL (geralmente vazia no XAMPP)

REM Caminho para o MySQL no XAMPP
set MYSQL_PATH="C:\xampp\mysql\bin\mysql.exe"

REM Verificar se o mysql.exe existe
if not exist %MYSQL_PATH% (
    echo ❌ MySQL nao encontrado no caminho padrao do XAMPP
    echo    Caminho procurado: %MYSQL_PATH%
    echo    Ajuste o caminho ou execute manualmente:
    echo    mysql -u root -p ^< database/create_database.sql
    pause
    exit /b 1
)

REM Executar o script SQL
echo Executando script SQL...
%MYSQL_PATH% -u root -p < database\create_database.sql

if %errorlevel%==0 (
    echo ✓ Banco de dados criado com sucesso!
) else (
    echo ❌ Erro ao criar banco de dados. Tente manualmente:
    echo    1. Abra phpMyAdmin: http://localhost/phpmyadmin
    echo    2. Cole o conteudo de database/create_database.sql
    echo    3. Execute o script
    pause
    exit /b 1
)

REM Verificar se os arquivos foram criados
echo.
echo [4/4] Verificando instalacao...

if exist "config\database.php" (
    echo ✓ Arquivo de configuracao criado
) else (
    echo ❌ Arquivo config/database.php nao encontrado
)

if exist "database\create_database.sql" (
    echo ✓ Script SQL encontrado
) else (
    echo ❌ Script database/create_database.sql nao encontrado
)

if exist "admin.php" (
    echo ✓ Painel admin criado
) else (
    echo ❌ Painel admin nao encontrado
)

echo.
echo ================================================
echo CONFIGURACAO CONCLUIDA!
echo ================================================
echo.
echo 🌐 Acesse a aplicacao:
echo    Pagina Principal: http://localhost/Azure-Web-App---Vuln/index.php
echo    Painel Admin:     http://localhost/Azure-Web-App---Vuln/admin.php?admin=true
echo    phpMyAdmin:       http://localhost/phpmyadmin
echo.
echo 👥 Usuarios de teste:
echo    admin/admin, root/123456, user1/password, guest/guest
echo.
echo 🔍 Para testar vulnerabilidades:
echo    SQL Injection: admin' OR '1'='1'--
echo    XSS: ^<script^>alert('XSS')^</script^>
echo.
echo ⚠️  LEMBRE-SE: Esta aplicacao e VULNERAVEL por design!
echo    Use apenas para aprendizado e mantenha isolada!
echo.
pause