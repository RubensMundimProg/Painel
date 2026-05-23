# Migracao PHP 8.4 e Mezzio

## Decisao tecnica

O caminho seguro e:

1. Atualizar o ambiente atual para PHP 8.4 mantendo Laminas MVC 3.8.
2. Criar uma entrada Mezzio paralela ao MVC.
3. Migrar rotas por grupos funcionais.
4. Remover Laminas MVC somente quando as rotas principais estiverem em Mezzio.
5. Depois da remocao do MVC, avaliar PHP 8.5.

Motivo: `laminas/laminas-mvc` 3.8 suporta PHP 8.1 ate 8.4. A linha atual do Mezzio suporta PHP 8.2 ate 8.5, mas a aplicacao ainda depende fortemente de controllers MVC.

Em 21/05/2026, o PHP mais recente publicado e 8.5.6, e a linha 8.4 esta em 8.4.21. Para este projeto, manter PHP 8.4.x ate remover `laminas/laminas-mvc` e a opcao com menor risco.

## Atualizacao imediata para PHP 8.4 no IIS

No servidor:

1. Baixar PHP 8.4 x64 Non Thread Safe.
2. Instalar em uma pasta nova, por exemplo:

```text
C:\Program Files (x86)\PHP\v8.4\php.exe
```

3. Copiar/adaptar o `php.ini` atual do PHP 8.1.
4. Conferir extensoes usadas pela aplicacao:

```ini
extension=dom
extension=intl
extension=mbstring
extension=pdo_mysql
extension=mysqli
extension=curl
extension=openssl
extension=fileinfo
extension=gd
extension=zip
```

5. Conferir configuracoes importantes:

```ini
date.timezone=America/Fortaleza
extension_dir="ext"
cgi.force_redirect=0
cgi.fix_pathinfo=1
fastcgi.impersonate=1
upload_tmp_dir="C:\Windows\Temp"
session.save_path="C:\Windows\Temp"
memory_limit=1024M
max_execution_time=300
max_input_vars=5000
```

6. No IIS/FastCGI, trocar o executavel PHP para:

```text
C:\Program Files (x86)\PHP\v8.4\php-cgi.exe
```

7. Atualizar as tasks para usarem o PHP 8.4:

```text
Program/script:
C:\Program Files (x86)\PHP\v8.4\php.exe

Add arguments:
"C:\inetpub\wwwroot\aplicacoes\painel\bin\painel-cli.php" cache-validados

Start in:
C:\inetpub\wwwroot\aplicacoes\painel
```

8. Rodar validacoes:

```powershell
& "C:\Program Files (x86)\PHP\v8.4\php.exe" -v
& "C:\Program Files (x86)\PHP\v8.4\php.exe" -m
& "C:\Program Files (x86)\PHP\v8.4\php.exe" -r "require 'vendor/autoload.php'; echo 'autoload ok', PHP_EOL;"
& "C:\Program Files (x86)\PHP\v8.4\php.exe" bin\painel-cli.php --check
```

## Composer para PHP 8.4

Na maquina com internet:

1. Instalar PHP 8.4 localmente.
2. Rodar Composer com PHP 8.4.
3. Alterar temporariamente ou definitivamente o `config.platform.php` para 8.4.x somente quando o servidor ja estiver pronto para PHP 8.4.
4. Executar:

```powershell
& "C:\Program Files (x86)\PHP\v8.4\php.exe" composer.phar update --with-all-dependencies
```

5. Copiar para o servidor:

```text
composer.json
composer.lock
vendor
arquivos alterados da aplicacao
```

## Plano Mezzio

### Fase 1 - Bootstrap paralelo

Criar uma entrada separada, sem substituir o MVC. Nesta fase ja foram adicionados:

```text
public/index-mezzio.php
config/mezzio/container.php
config/mezzio/pipeline.php
config/mezzio/routes.php
src/App/ConfigProvider.php
src/App/Handler/HealthHandler.php
```

Essa entrada responde inicialmente uma rota de saude:

```text
GET /health
```

Validacao manual via CLI:

```powershell
$env:REQUEST_METHOD='GET'
$env:REQUEST_URI='/health'
& "C:\Program Files (x86)\PHP\v8.4\php.exe" public\index-mezzio.php
```

A resposta esperada e um JSON com `status: ok` e `runtime: mezzio`.

### Fase 2 - Container compartilhado

Reaproveitar configuracoes e services que ja existem:

```text
config/autoload/global.php
config/autoload/production.php
module/*/config/module.config.php
```

O objetivo e permitir que handlers Mezzio usem os mesmos services/tables ja corrigidos para PHP 8.1/8.4.

### Fase 3 - Rotas sem layout

Migrar primeiro rotas JSON/CLI/AJAX:

```text
/api/*
/dashboard/*
/rss/*
/validados/atualizar
/triagem/remover-anexo
```

Essas rotas tem menos dependencia de layout MVC e sao melhores para validar Mezzio.

### Fase 4 - Telas MVC

Migrar telas completas por modulo:

```text
Autenticacao
Application/Validados
Application/Triagem
Dashboard
Mobile
Relatorios
```

Cada tela precisa virar handler + template, substituindo:

```text
AbstractActionController
ViewModel
JsonModel
$this->params()
$this->redirect()
$this->url()
```

por PSR-7/PSR-15, response factories e templates Mezzio.

### Fase 5 - Corte final

Somente apos as rotas criticas estarem em Mezzio:

1. Trocar `public/index.php` para Mezzio.
2. Remover `laminas/laminas-mvc`.
3. Remover configs MVC antigas.
4. Atualizar Composer para PHP 8.5.

## Ordem recomendada de migracao

1. Healthcheck Mezzio.
2. Rotas CLI ja mapeadas no `bin/painel-cli.php`.
3. RSS/INMET.
4. AJAX de Validados/Triagem.
5. Dashboard JSON/cache.
6. Login/logout/callback.
7. Telas principais.
8. Relatorios/PDF.

## Interface grafica

A modernizacao visual inicial foi aplicada como camada conservadora:

```text
public/assets/css/painel-modern.css
```

O arquivo e carregado depois dos CSS antigos no layout administrativo. Assim, a aplicacao ganha uma aparencia mais atual em cabecalho, menu, formularios, tabelas, botoes, modais e alertas, mas os estilos antigos especificos continuam disponiveis para telas que dependem deles.

## Riscos conhecidos

- Muitos controllers dependem de `AbstractEstruturaController`.
- Ha uso de sessao Laminas nas telas.
- Ha helpers de view antigos em layouts `.phtml`.
- Algumas rotinas usam `die`, `echo` e estado global.
- As tasks precisam continuar funcionando durante a migracao.
- O servidor de homologacao nao tem internet, entao `vendor` deve continuar sendo gerado fora do servidor.
