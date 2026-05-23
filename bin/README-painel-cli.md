# Painel CLI

Entrada CLI para executar as rotinas antigas de console sem `laminas-console` e sem passar pelo `public/index.php`.

## Execucao manual

No ambiente local:

```powershell
& "C:\Program Files (x86)\PHP\v8.1\php.exe" bin\painel-cli.php cache-validados
& "C:\Program Files (x86)\PHP\v8.1\php.exe" bin\painel-cli.php dados-api
& "C:\Program Files (x86)\PHP\v8.1\php.exe" bin\painel-cli.php build-dashboard
& "C:\Program Files (x86)\PHP\v8.1\php.exe" bin\painel-cli.php calendario-gantt
& "C:\Program Files (x86)\PHP\v8.1\php.exe" bin\painel-cli.php rss
& "C:\Program Files (x86)\PHP\v8.1\php.exe" bin\painel-cli.php geojson
& "C:\Program Files (x86)\PHP\v8.1\php.exe" bin\painel-cli.php backup-ocorrencias
```

No servidor de homologacao, a partir da raiz da aplicacao:

```powershell
cd C:\inetpub\wwwroot\aplicacoes\painel
& "C:\Program Files (x86)\PHP\v8.1\php.exe" bin\painel-cli.php cache-validados
```

Para listar os comandos:

```powershell
& "C:\Program Files (x86)\PHP\v8.1\php.exe" bin\painel-cli.php --help
```

Para conferir se os comandos resolvem controller/action sem executar rotina:

```powershell
& "C:\Program Files (x86)\PHP\v8.1\php.exe" bin\painel-cli.php --check
```

Se uma tarefa antiga estiver chamando `public\index.php`, troque apenas o alvo do PHP. Exemplo:

```powershell
# antigo
& "C:\Program Files (x86)\PHP\v8.1\php.exe" public\index.php cache-validados

# novo
& "C:\Program Files (x86)\PHP\v8.1\php.exe" bin\painel-cli.php cache-validados
```

Comandos antigos encontrados no Task Scheduler e equivalentes novos:

```text
php index.php build-dashboard          -> bin\painel-cli.php build-dashboard
php index.php dados-api                -> bin\painel-cli.php dados-api
php index.php rss                      -> bin\painel-cli.php rss
php index.php kml-ultima-milha         -> bin\painel-cli.php kml-ultima-milha
php index.php limpar-acesso            -> bin\painel-cli.php limpar-acesso
php index.php calendario-gantt         -> bin\painel-cli.php calendario-gantt
php index.php geojson                  -> bin\painel-cli.php geojson
php index.php cache-pre                -> bin\painel-cli.php cache-pre
php index.php atualiza-label-ativos    -> bin\painel-cli.php atualiza-label-ativos
php index.php atualizar-label-ativos   -> bin\painel-cli.php atualizar-label-ativos
php index.php backup-ocorrencias       -> bin\painel-cli.php backup-ocorrencias
```

As tarefas `twitter` e `restart-twitter` sao legado. Como a integracao do Twitter/X foi retirada, deixe essas tarefas desabilitadas. Para clima e tempo do INMET, mantenha apenas `rss`.

## Task Scheduler

Exemplo para criar uma tarefa diaria de cache dos validados as 06:00:

```powershell
schtasks /Create /TN "Painel cache-validados" /SC DAILY /ST 06:00 /TR "\"C:\Program Files (x86)\PHP\v8.1\php.exe\" \"C:\inetpub\wwwroot\aplicacoes\painel\bin\painel-cli.php\" cache-validados" /F
```

Exemplo para atualizar o dashboard a cada 30 minutos:

```powershell
schtasks /Create /TN "Painel build-dashboard" /SC MINUTE /MO 30 /TR "\"C:\Program Files (x86)\PHP\v8.1\php.exe\" \"C:\inetpub\wwwroot\aplicacoes\painel\bin\painel-cli.php\" build-dashboard" /F
```

Exemplo para gerar calendario/Gantt diariamente:

```powershell
schtasks /Create /TN "Painel calendario-gantt" /SC DAILY /ST 05:30 /TR "\"C:\Program Files (x86)\PHP\v8.1\php.exe\" \"C:\inetpub\wwwroot\aplicacoes\painel\bin\painel-cli.php\" calendario-gantt" /F
```

## Logs

O script grava execucao, saida e erros em:

```text
data\logs\cli.log
```

O comando `cache-validados` e executado em uma passagem por chamada do CLI. Isso evita que uma tarefa agendada fique presa no `while (true)` legado da action.

Quando executado por este CLI, `cache-validados` grava os caches locais em `public\filter\`. O cache remoto em `\\DOVERLANDIA\cmi` fica restrito ao fluxo legado continuo; se esse compartilhamento estiver indisponivel, a rotina apenas registra aviso em `data\erro_validados_cache.txt`.
