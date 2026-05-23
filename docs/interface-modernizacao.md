# Modernizacao da interface

## Escopo da primeira etapa

A camada visual nova foi criada sem remover a interface antiga. A tela antiga de Validados continua em:

```text
/validados
```

A tela nova fica disponivel para teste em:

```text
/validados?ui=modern
```

Depois que `?ui=modern` for usado, a preferencia fica gravada na sessao e as telas administrativas passam a usar o novo layout sempre que possivel. Para voltar ao layout antigo:

```text
?ui=classic
```

Nenhum controller, service, rota de negocio ou estrutura de banco foi substituido nesta etapa. A tela moderna usa os mesmos dados, os mesmos endpoints AJAX e os mesmos nomes de classes que as rotinas atuais ja esperam.

## Inventario atual

Layout principal antigo:

```text
module/moduloapi/Estrutura/view/layout/admin.phtml
module/Application/view/layout/menu.phtml
module/moduloapi/Estrutura/view/layout/mensagens.phtml
```

Assets antigos mais relevantes:

```text
public/assets/css/risk-manager.css
public/assets/css/relogio.css
public/assets/css/risk-manager-mobile.css
public/assets/js/risk-manager.js
public/assets/jquery/jquery-1.11.1.min.js
public/assets/bootstrap/
public/assets/datatable/
public/assets/datepicker/
public/assets/chosen/
public/assets/fullcalendar/
public/assets/gantt/
public/dashboard-assets/
```

Telas criticas identificadas:

```text
module/Application/view/application/validados/index.phtml
module/Application/view/application/validados/fechados.phtml
module/Application/view/application/triagem/index.phtml
module/Application/view/application/triagem/recusados.phtml
module/Application/view/application/relatorios/
module/Application/view/application/calendario/
module/Application/view/application/gantt/
module/Dashboard/view/dashboard/dashboard/
module/Mobile/view/layout/mobile.phtml
```

## Nova camada visual

Bootstrap 5 esta instalado pelo Composer:

```text
twbs/bootstrap 5.3.8
```

Os arquivos usados pela aplicacao foram copiados para assets locais, mantendo compatibilidade com o servidor sem internet:

```text
public/assets/modern/vendor/bootstrap/css/bootstrap.min.css
public/assets/modern/vendor/bootstrap/js/bootstrap.bundle.min.js
public/assets/modern/css/painel-modern-v2.css
public/assets/modern/js/painel-modern.js
```

Novo layout base:

```text
module/Application/view/layout/modern.phtml
```

Registro no template map:

```text
layout/modern
```

Componentes reutilizaveis:

```text
module/Application/view/modern/components/button.phtml
module/Application/view/modern/components/card.phtml
module/Application/view/modern/components/badge.phtml
module/Application/view/modern/components/table.phtml
module/Application/view/modern/components/filter.phtml
module/Application/view/modern/components/modal.phtml
module/Application/view/modern/components/toast.phtml
```

## Tela de Validados

View nova:

```text
module/Application/view/application/validados/index-modern.phtml
```

Tela inicial moderna:

```text
module/Application/view/application/index/index-modern.phtml
```

Ativacao no controller:

```text
/validados?ui=modern
```

O controller continua montando os mesmos dados. Quando o parametro `ui=modern` existe, ele troca apenas o layout e o template:

```text
layout/modern
application/validados/index-modern
```

Classes antigas preservadas para manter as acoes:

```text
update-progress
view-progress
change-status
update-status
view-anexo
status-registro
form-ajax
```

## Como testar

1. Abrir a tela antiga e confirmar que continua igual:

```text
/validados
```

2. Abrir a tela moderna:

```text
/validados?ui=modern
```

3. Validar na tela moderna:

```text
filtrar
limpar filtro
pesquisar na lista
atualizar progresso
visualizar historico
fechar alerta
visualizar anexos
trocar avaliacao pedagogica
abrir pelo celular
```

4. Se algo falhar, testar a mesma acao na tela antiga para separar erro visual de erro de regra de negocio.

## Guia para migrar as proximas telas

1. Criar uma view paralela com sufixo `-modern.phtml`.
2. Manter a view antiga ativa por padrao.
3. Adicionar parametro `ui=modern` no controller da tela.
4. Usar `layout/modern` apenas quando o parametro estiver presente.
5. Preservar nomes de campos, classes JS, ids de modais e endpoints AJAX existentes.
6. Trocar imagens antigas de botoes por botoes Bootstrap 5 com texto claro.
7. Usar `modern-filter` para filtros, `modern-table` para tabelas, `modern-badge` para status e `modern-card` para agrupamentos.
8. Validar desktop e celular antes de tornar a tela moderna padrao.

Ordem recomendada:

```text
Validados
Triagem
Relatorios/PDF
Dashboard JSON/cache
Dashboard visual
Mapas
RSS/INMET
Graficos
Mobile
```

## Observacoes tecnicas

O arquivo `public/assets/modern/js/painel-modern.js` cria uma ponte para que chamadas antigas como `$('#modal').modal('show')` continuem funcionando com Bootstrap 5. Isso permite migrar tela por tela sem reescrever todos os scripts antigos de uma vez.

Quando toda a interface estiver migrada, o proximo passo sera extrair os fluxos AJAX para handlers Mezzio e remover dependencias do layout Laminas MVC.

## Dashboard

O dashboard usa layout proprio e muitos assets especificos de mapas/graficos. A tentativa de camada visual por CSS foi deixada disponivel apenas para teste manual, sem carregar automaticamente no `ui=modern`, porque misturar o layout antigo do dashboard com a nova casca causou travamentos e conflito visual.

```text
public/dashboard-assets/css/dashboard-modern.css
module/Dashboard/view/layout/layout.phtml
```

Teste manual, se necessario:

```text
/dashboard?dashboard-ui=modern
```

Essa escolha evita quebrar carrossel, Highcharts, OpenLayers, RSS/INMET e scripts de atualizacao. A migracao completa do dashboard deve ser feita por pagina parcial:

```text
pag-aplicacao-a.phtml
pag-aplicacao-dia.phtml
pag-aplicacao-b.phtml
pag-aplicacao-c.phtml
pag-aplicacao-d.phtml
pag-aplicacao-e.phtml
pag-aplicacao-f.phtml
pag-aplicacao-g.phtml
pag-mapa.phtml
pag-midia.phtml
pag-ultima-milha.phtml
```

Prioridade visual para dashboard:

```text
1. Cards de indicadores.
2. Graficos Highcharts.
3. Tabelas de alertas.
4. Mapas.
5. Clima e tempo/INMET.
6. Ultima milha.
```
