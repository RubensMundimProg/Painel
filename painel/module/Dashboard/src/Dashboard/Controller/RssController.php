<?php

namespace Dashboard\Controller;

use Estrutura\Controller\AbstractEstruturaController;
use Laminas\View\Model\JsonModel;

class RssController extends AbstractEstruturaController
{
    /**
     * Caminho base para os arquivos RSS, relativo ao /public/index.php
     * ./data/rss/ aponta para C:\inetpub\wwwroot\aplicacoes\painel\public\data\rss\
     */
    const RSS_PATH_BASE = './data/rss/'; // SEU CAMINHO CORRETO

    /**
     * Caminho base para os arquivos do Twitter, relativo ao /public/index.php
     * ./data/twitter/ aponta para C:\inetpub\wwwroot\aplicacoes\painel\public\data\twitter\
     */
    const TWITTER_PATH_BASE = './data/twitter/'; // SEU CAMINHO CORRETO

    /**
     * Ação para o Ticker/Letreiro (antigo)
     * Rota: /rss/get-rss
     * Lendo do caminho correto (public/data/rss).
     */
    public function getRssAction()
    {
        try{
            $txt = @file_get_contents(self::TWITTER_PATH_BASE . 'tags.txt');
            $words = explode(';',$txt);

            $wordsTratado = [];
            foreach ($words as $word) {
                if(strpos($word,'+')){
                    $aux = explode('+',$word);
                    $wordsTratado = array_merge($wordsTratado,$aux);
                }else{
                    $wordsTratado[] = $word;
                }
            }
            ksort($wordsTratado);

            $path = self::RSS_PATH_BASE;
            $xmls = @scandir($path);
            $rss = new \DOMDocument();

            $tratados = [];
            
            if (!$xmls) {
                 throw new \Exception('Nao foi possivel ler o diretorio de RSS em: ' . $path);
            }
            
            foreach ($xmls as $xml) {
                if(in_array($xml,['.','..'])) continue;

                $fullPath = $path . $xml;
                if (!is_readable($fullPath) || filesize($fullPath) === 0) {
                    continue;
                }
                if (!@$rss->load($fullPath)) {
                    continue;
                }

                $name = str_replace('.xml','',$xml);

                foreach ($rss->getElementsByTagName('item') as $node)
                {
                    if(preg_match('/inmet/',$name)){
                        $descriptionNode = $node->getElementsByTagName('description')->item(0);
                        $descriptionHtml = '';
                        if ($descriptionNode !== null) {
                            if ($descriptionNode->firstChild instanceof \DOMCdataSection) {
                                $descriptionHtml = $descriptionNode->firstChild->data;
                            } else {
                                $descriptionHtml = $descriptionNode->nodeValue;
                            }
                        }
                        $details = $this->parseInmetDescription($descriptionHtml); 
                        $titleNode = $node->getElementsByTagName('title')->item(0);
                        $fallbackTitle = $titleNode ? trim($titleNode->nodeValue) : '';
                        $area = isset($details['area']) && $details['area'] !== null && $details['area'] !== ''
                            ? $details['area'] : $fallbackTitle;
                        $description = isset($details['description']) && $details['description'] !== null && $details['description'] !== ''
                            ? $details['description'] : $fallbackTitle;
                        $pubDateNode = $node->getElementsByTagName('pubDate')->item(0);
                        $publishedAt = $pubDateNode ? $this->formatDateTime($pubDateNode->nodeValue) : ['human' => null];
                        $title = ($area !== '' && $description !== '')
                            ? $area . ' => ' . $description
                            : ($description !== '' ? $description : $area);
                        $tratados[$name][] = [
                            'title' => $title,
                            'date' => $publishedAt['human'] ? $publishedAt['human'] : ($pubDateNode ? date('d/m/Y H:i', strtotime($pubDateNode->nodeValue)) : ''),
                        ];
                        continue;
                    }
                    
                    if ($node->getElementsByTagName('title')->item(0) && $this->substrCountArray( $node->getElementsByTagName('title')->item(0)->nodeValue, $words ) > 0) {
                        $tratados[$name][] = [
                            'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
                            'date' => date('d/m/Y H:i', strtotime($node->getElementsByTagName('pubDate')->item(0)->nodeValue))
                        ];
                    }
                }
            }
        }catch(\Exception $e){
            return new JsonModel(['error'=>true,'message'=>$e->getMessage(),'dados'=>[]]);
        }
        return new JsonModel(['error'=>false,'message'=>'','dados'=>$tratados]);
    }

    /**
     * Ação para o Painel INMET (Novo)
     * Rota: /rss/inmet-avisos
     * Lendo do cache (public/data/rss/inmet.xml).
     */
    public function inmetAvisosAction()
    {
        $cachePath = $this->getInmetCachePath(); // Caminho CORRETO: ./data/rss/inmet.xml
        $timezone = new \DateTimeZone('America/Sao_Paulo');
        
        try {
            if (!file_exists($cachePath) || !is_readable($cachePath) || filesize($cachePath) < 100) {
                // Fallback para o globo-news se o inmet.xml falhar
                $cachePath = self::RSS_PATH_BASE . 'globo-news.xml'; // ./data/rss/globo-news.xml
                if (!file_exists($cachePath) || !is_readable($cachePath)) {
                     throw new \RuntimeException('Arquivos de cache (inmet.xml, globo-news.xml) não encontrados. A tarefa agendada (php index.php rss) precisa ser executada.');
                }
            }

            $response = @file_get_contents($cachePath);
            if ($response === false) {
                throw new \RuntimeException('Não foi possível ler o arquivo de cache.');
            }

            $normalizedResponse = $this->normalizeFeedResponse($response);
            $rss = $this->loadInmetFeedXml($normalizedResponse);

            if ($rss === null) {
                 throw new \RuntimeException('O arquivo de cache ('.$cachePath.') contém um XML inválido. Verifique se o conteúdo copiado manualmente é um XML válido.');
            }
            
            $alerts = [];
            $limit = 12; // Limite antigo (removido)
            $count = 0;

            foreach ($rss->channel->item as $item) {
                $description = (string) $item->description;
                
                // Reutiliza as funções de parse que você já tinha
                $parsedDescription = $this->parseInmetDescription($description);
                $publishedAt = $this->formatDateTime((string) $item->pubDate);

                $alerts[] = array_merge([ // <--- ESTE ARRAY
                    'title' => trim((string) $item->title),
                    'link' => trim((string) $item->link),
                    'guid' => trim((string) $item->guid),
                    'published_at' => $publishedAt['human'],
                    'published_at_iso' => $publishedAt['iso'],
                    
                    // ========================================================
                    // ESTA É A CORREÇÃO CRÍTICA PARA AS CORES:
                    'severity' => $parsedDescription['severity'], 
                    // ========================================================

                ], $parsedDescription); // O merge já adiciona o resto

                $count++;
                // Removido o limite de 12 para mostrar todos os alertas
                // if ($count >= $limit) {
                //     break;
                // }
            }

            $modifiedTime = @filemtime($cachePath);
            $fetchedAt = (new \DateTime('@' . ($modifiedTime ?: time())))->setTimezone($timezone)->format(DATE_ATOM);

            return new JsonModel([
                'error' => false,
                'message' => 'Avisos carregados da última sincronização local.',
                'alerts' => $alerts,
                'fetched_at' => $fetchedAt,
                'source' => 'cache',
                'source_url' => str_replace('./', '', $cachePath),
            ]);

        } catch (\Exception $e) {
            return new JsonModel([
                'error' => true,
                'message' => $e->getMessage(),
                'alerts' => [],
            ]);
        }
    }

    //
    // -------------------------------------------------------------------
    // FUNÇÕES DE DOWNLOAD E PARSE (HELPER)
    // -------------------------------------------------------------------
    //
    
    // (Esta função não é mais chamada pela 'inmetAvisosAction', apenas pela 'loadRssAction')
    protected function fetchInmetAlerts($preferredSource = 'auto')
    {
        $feedUrls = [
            'https://apiprevmet3.inmet.gov.br/avisos/rss',
            'https://alerts.inmet.gov.br/cap_12/rss/alert-as.rss',
            'https://alertas2.inmet.gov.br/rss',
        ];
        $timezone = new \DateTimeZone('America/Sao_Paulo');
        $result = [ 'alerts' => [], 'message' => '', 'source' => 'live', 'fetched_at' => (new \DateTime('now', $timezone))->format(DATE_ATOM), 'source_url' => null, ];
        $errors = []; $response = null; $preferredSource = strtolower((string) $preferredSource);
        $forceCache = $preferredSource === 'cache'; $forceLive = $preferredSource === 'live';
        if (!$forceCache) {
            foreach ($feedUrls as $feedUrl) {
                // ========================================================
                // ESTA É A MUDANÇA: Usando a função de download corrigida
                // ========================================================
                $response = $this->downloadFeed($feedUrl, $errors); 
                if ($this->isValidInmetResponse($response)) { $result['source_url'] = $feedUrl; break; }
                if ($response !== null && !$this->isValidInmetResponse($response)) { $errors[] = sprintf('Resposta inválida recebida de %s.', $feedUrl); }
                $response = null;
            }
        }
        if ($response === null) {
            $cachePath = $this->getInmetCachePath();
            $cached = $this->loadCachedInmetFeed($cachePath);
            if ($this->isValidInmetResponse($cached)) {
                $result['message'] = $this->buildCacheMessage($errors); $result['source'] = 'cache';
                $result['fetched_at'] = $this->formatCacheTimestamp($cachePath, $timezone); $response = $cached;
            } elseif ($cached !== null && !$this->isValidInmetResponse($cached)) {
                $errors[] = 'O arquivo de cache local está inválido ou corrompido.';
            }
        }
        if ($response === null) {
            $errorMessage = 'Não foi possível recuperar os dados meteorológicos do INMET.';
            if (!empty($errors)) { $errorMessage .= ' ' . implode(' ', $errors); }
            throw new \RuntimeException(trim($errorMessage));
        }
        $normalizedResponse = $this->normalizeFeedResponse($response);
        $rss = $this->loadInmetFeedXml($normalizedResponse);
        if ($rss === null && $result['source'] === 'live' && !$forceLive) {
            $cachePath = $this->getInmetCachePath();
            $cached = $this->loadCachedInmetFeed($cachePath);
            if ($this->isValidInmetResponse($cached)) {
                $result['message'] = $this->buildCacheMessage(array_merge($errors, ['Resposta inválida recebida do serviço ao vivo.']));
                $result['source'] = 'cache'; $result['fetched_at'] = $this->formatCacheTimestamp($cachePath, $timezone);
                $normalizedResponse = $this->normalizeFeedResponse($cached);
                $rss = $this->loadInmetFeedXml($normalizedResponse);
            }
        }
        if ($rss === null) {
            $errorMessage = 'O INMET retornou uma resposta inválida.';
            if (!empty($errors)) { $errorMessage .= ' ' . implode(' ', $errors); }
            throw new \RuntimeException(trim($errorMessage));
        }
        if ($result['source'] === 'live' && $result['source_url']) {
            $this->cacheInmetFeed($normalizedResponse);
        }
        $limit = 12; $count = 0;
        foreach ($rss->channel->item as $item) {
            $description = (string) $item->description;
            $parsedDescription = $this->parseInmetDescription($description);
            $publishedAt = $this->formatDateTime((string) $item->pubDate);
            $result['alerts'][] = array_merge([
                'title' => trim((string) $item->title), 'link' => trim((string) $item->link),
                'guid' => trim((string) $item->guid), 'published_at' => $publishedAt['human'],
                'published_at_iso' => $publishedAt['iso'],
            ], $parsedDescription);
            $count++;
            if ($count >= $limit) { break; }
        }
        return $result;
    }

    // ====================================================================
    // FUNÇÃO DE DOWNLOAD CORRIGIDA (Substitui a sua antiga)
    // ====================================================================
    // Esta função finge ser um navegador e ignora os problemas de SSL
    // do seu servidor antigo.
    protected function downloadFeed($url, array &$errors = [])
    {
        $headers = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Accept: application/xml,text/xml,application/xhtml+xml,text/html;q=0.9,*/*;q=0.8',
            'Accept-Encoding: identity',
            'Accept-Language: en-US,en;q=0.9,pt;q=0.8',
        ];
        
        // Opções para file_get_contents (ignora SSL)
        $context = stream_context_create([
            'http' => [
                'timeout' => 15,
                'header' => implode("\r\n", $headers) . "\r\n",
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        // Verifica se a resposta é um XML válido (como o rss.txt que você mandou)
        if ($response !== false && strpos($response, '<rss') !== false) {
            return $response; // Sucesso com file_get_contents
        }

        $attemptErrors = [sprintf('Falha ao acessar %s via file_get_contents. Resposta: %s', $url, substr($response, 0, 100))];

        // Se file_get_contents falhar, tenta via cURL (mais robusto)
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_ENCODING => '',
                CURLOPT_SSL_VERIFYPEER => false, // Ignora SSL
                CURLOPT_SSL_VERIFYHOST => 0,     // Ignora SSL
            ]);
            
            $result = curl_exec($ch);
            
            if ($result === false) {
                $attemptErrors[] = sprintf('cURL: %s', curl_error($ch));
            }
            curl_close($ch);

            if ($result !== false && strpos($result, '<rss') !== false) {
                return $result; // Sucesso via cURL
            }
        }

        $errors = array_merge($errors, $attemptErrors);
        return null; // Retorna null se ambos falharem
    }
    
    // Esta função agora apenas chama a 'downloadFeed' corrigida
    protected function downloadInmetFeed($url, array &$errors = [])
    {
        return $this->downloadFeed($url, $errors);
    }
    // ====================================================================
    // FIM DA FUNÇÃO DE DOWNLOAD CORRIGIDA
    // ====================================================================

    protected function normalizeFeedResponse($response)
    {
        if ($response === null || $response === '') { return ''; }
        if (strpos($response, '<rss') !== false) { return $response; }
        if (function_exists('gzdecode')) {
            $decoded = @gzdecode($response);
            if ($decoded !== false && strpos($decoded, '<rss') !== false) { return $decoded; }
        }
        if (function_exists('gzinflate')) {
            $inflated = @gzinflate($response);
            if ($inflated !== false && strpos($inflated, '<rss') !== false) { return $inflated; }
        }
        return $response;
    }

    protected function loadInmetFeedXml($response)
    {
        if ($response === null || trim($response) === '') { return null; }
        $previous = libxml_use_internal_errors(true);
        $rss = @simplexml_load_string($response); // Adicionado @
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        if ($rss === false || !isset($rss->channel)) { return null; }
        return $rss;
    }

    protected function isValidInmetResponse($response)
    {
        if ($response === null || $response === '') { return false; }
        // A validação real é se ele contém a tag <rss>
        return (strpos($response, '<rss') !== false);
    }

    protected function buildCacheMessage(array $errors)
    {
        if (empty($errors)) { return 'Avisos carregados a partir da última sincronização local.'; }
        return 'Avisos carregados a partir da última sincronização local. ' . implode(' ', $errors);
    }

    protected function getInmetCachePath($legacy_xml = false)
    {
        // Sempre usa inmet.xml, já que não estamos mais usando JSON
        return self::RSS_PATH_BASE . 'inmet.xml';
    }

    protected function loadCachedInmetFeed($path)
    {
        if ($path === null || !is_readable($path)) { return null; }
        $contents = @file_get_contents($path);
        return $contents !== false ? $contents : null;
    }

    protected function cacheInmetFeed($contents, $legacy_xml = true) // Força a salvar como XML
    {
        if (!is_string($contents) || trim($contents) === '') { return; }
        $path = $this->getInmetCachePath(true); // Caminho Correto
        if ($path === null) { return; }
        $directory = dirname($path);
        if (!is_dir($directory)) { @mkdir($directory, 0775, true); }
        @file_put_contents($path, $contents);
    }

    protected function formatCacheTimestamp($path, \DateTimeZone $timezone)
    {
        if ($path === null || !file_exists($path)) { return (new \DateTime('now', $timezone))->format(DATE_ATOM); }
        $modified = @filemtime($path);
        if ($modified === false) { return (new \DateTime('now', $timezone))->format(DATE_ATOM); }
        $date = new \DateTime('@' . $modified);
        $date->setTimezone($timezone);
        return $date->format(DATE_ATOM);
    }

    protected function parseInmetDescription($html)
    {
        $details = [ 'status' => null, 'event' => null, 'severity' => null, 'start' => null, 'start_iso' => null, 'end' => null, 'end_iso' => null, 'description' => null, 'area' => null, 'graphic' => null, ];
        if (trim($html) === '') { return $details; }
        $document = new \DOMDocument();
        $previousLibxml = libxml_use_internal_errors(true);
        $encodedHtml = '<?xml encoding="UTF-8">' . $html;
        @$document->loadHTML($encodedHtml, LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();
        libxml_use_internal_errors($previousLibxml);
        $rows = $document->getElementsByTagName('tr');
        foreach ($rows as $row) {
            $header = $row->getElementsByTagName('th')->item(0);
            $valueNode = $row->getElementsByTagName('td')->item(0);
            if (!$header || !$valueNode) { continue; }
            $normalizedKey = $this->normalizeKey($header->textContent);
            $value = trim($valueNode->textContent);
            switch ($normalizedKey) {
                case 'status': $details['status'] = $value; break;
                case 'evento': $details['event'] = $value; break;
                case 'severidade': $details['severity'] = $value; break;
                case 'inicio':
                    $start = $this->formatDateTime($value);
                    $details['start'] = $start['human']; $details['start_iso'] = $start['iso'];
                    break;
                case 'fim':
                    $end = $this->formatDateTime($value);
                    $details['end'] = $end['human']; $details['end_iso'] = $end['iso'];
                    break;
                case 'descricao': $details['description'] = $value; break;
                case 'area': $details['area'] = $value; break;
                case 'link_grafico':
                    $linkNode = $valueNode->getElementsByTagName('a')->item(0);
                    $details['graphic'] = $linkNode ? trim($linkNode->getAttribute('href')) : $value;
                    break;
                default: break;
            }
        }
        return $details;
    }

    protected function normalizeKey($key)
    {
        $normalized = $key;
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $key); // Adicionado @
            if ($converted !== false) { $normalized = $converted; }
        }
        $normalized = strtolower($normalized);
        $normalized = preg_replace('/[^a-z]+/', '_', $normalized);
        return trim($normalized, '_');
    }

    protected function formatDateTime($value)
    {
        $value = trim($value);
        if ($value === '') { return ['human' => null, 'iso' => null]; }
        $normalized = preg_replace('/\.\d+$/', '', $value);
        $timezone = new \DateTimeZone('America/Sao_Paulo');
        $formats = [
            DATE_RSS, 'Y-m-d H:i:s', 'Y-m-d H:i', 'd/m/Y H:i:s', 'd/m/Y H:i',
        ];
        foreach ($formats as $format) {
            try {
                $date = \DateTime::createFromFormat($format, $normalized);
                if (!$date) {
                    $date = \DateTime::createFromFormat($format, $normalized, $timezone);
                }
                if ($date instanceof \DateTime) {
                    $date->setTimezone($timezone);
                    return [ 'human' => $date->format('d/m/Y H:i'), 'iso' => $date->format(DATE_ATOM), ];
                }
            } catch (\Exception $e) { /* continua */ }
        }
        try {
            $date = new \DateTime($normalized);
            $date->setTimezone($timezone);
            return [ 'human' => $date->format('d/m/Y H:i'), 'iso' => $date->format(DATE_ATOM), ];
        } catch (\Exception $e) {
            return ['human' => $value, 'iso' => null];
        }
    }

    public function substrCountArray( $haystack, $needle ) {
        $count = 0;
        foreach ($needle as $substring) {
            $count += substr_count( $haystack, $substring);
        }
        return $count;
    }

    // Funções de PROXY (Mantidas)
    protected function resolveProxySettings($url){ /* ...código original mantido... */ 
        $scheme = strtolower(parse_url($url, PHP_URL_SCHEME) ?: 'http'); $host = parse_url($url, PHP_URL_HOST) ?: '';
        $port = parse_url($url, PHP_URL_PORT) ?: ($scheme === 'https' ? 443 : 80);
        if ($this->isHostInNoProxyList($host, $port)) { return null; } $proxyUrl = null;
        if ($scheme === 'https') { $proxyUrl = getenv('https_proxy') ?: getenv('HTTPS_PROXY'); } 
        else { $proxyUrl = getenv('http_proxy') ?: getenv('HTTP_PROXY'); }
        if (!$proxyUrl) { $proxyUrl = getenv('all_proxy') ?: getenv('ALL_PROXY'); }
        if (!$proxyUrl) { return null; }
        return $this->parseProxyUrl($proxyUrl, $scheme);
    }
    protected function isHostInNoProxyList($host, $port){ /* ...código original mantido... */ 
        if ($host === '') { return false; } $noProxy = getenv('no_proxy') ?: getenv('NO_PROXY');
        if (!$noProxy) { return false; } $entries = array_filter(array_map('trim', explode(',', $noProxy)));
        if (empty($entries)) { return false; } $comparisonHost = strtolower($host);
        foreach ($entries as $entry) {
            if ($entry === '') { continue; } if ($entry === '*') { return true; }
            $entryPort = null; $entryHost = $entry;
            if (strpos($entry, ':') !== false) { list($entryHost, $entryPort) = explode(':', $entry, 2); }
            if ($entryPort !== null && $entryPort !== '' && (int) $entryPort !== (int) $port) { continue; }
            $entryHost = trim($entryHost); if ($entryHost === '') { continue; }
            $normalizedHost = strtolower(ltrim($entryHost, '.'));
            if (strpos($normalizedHost, '*') !== false) {
                $pattern = '/^' . str_replace('\\*', '.*', preg_quote($normalizedHost, '/')) . '$/i';
                if (@preg_match($pattern, $comparisonHost)) { return true; }
                continue;
            }
            if ($comparisonHost === $normalizedHost) { return true; }
            if ($this->endsWithHost($comparisonHost, $normalizedHost)) { return true; }
        }
        return false;
    }
    protected function parseProxyUrl($proxyUrl, $targetScheme){ /* ...código original mantido... */ 
        $proxyUrl = trim($proxyUrl); if ($proxyUrl === '') { return null; }
        if (!preg_match('#^[a-zA-Z0-9]+://#', $proxyUrl)) { $proxyUrl = 'http://' . $proxyUrl; }
        $parts = @parse_url($proxyUrl); if ($parts === false || !isset($parts['host'])) { return null; }
        $scheme = isset($parts['scheme']) ? strtolower($parts['scheme']) : 'http';
        $host = $parts['host']; $port = isset($parts['port']) ? $parts['port'] : $this->defaultProxyPort($scheme);
        $auth = null;
        if (isset($parts['user'])) {
            $user = rawurldecode($parts['user']); $pass = isset($parts['pass']) ? rawurldecode($parts['pass']) : '';
            $auth = $pass !== '' ? $user . ':' . $pass : $user;
        }
        $curlType = defined('CURLPROXY_HTTP') ? constant('CURLPROXY_HTTP') : null;
        if (strpos($scheme, 'socks5') === 0 && defined('CURLPROXY_SOCKS5')) { $curlType = constant('CURLPROXY_SOCKS5'); } 
        elseif (strpos($scheme, 'socks4') === 0 && defined('CURLPROXY_SOCKS4')) { $curlType = constant('CURLPROXY_SOCKS4'); }
        $tunnel = ($targetScheme === 'https' && $curlType === (defined('CURLPROXY_HTTP') ? constant('CURLPROXY_HTTP') : null));
        return [
            'scheme' => $scheme, 'host' => $host, 'port' => $port, 'proxy' => $host . ':' . $port, 'auth' => $auth,
            'auth_header' => (strpos($scheme, 'http') === 0) ? $auth : null, 'curl_type' => $curlType, 'tunnel' => $tunnel,
        ];
    }
    protected function defaultProxyPort($scheme){ /* ...código original mantido... */ 
        switch ($scheme) {
            case 'https': return 443;
            case 'socks5': case 'socks5h': case 'socks': case 'socks4': case 'socks4a': return 1080;
            default: return 80;
        }
    }
    protected function endsWithHost($host, $suffix){ /* ...código original mantido... */ 
        if ($suffix === '') { return false; } if (strlen($host) < strlen($suffix)) { return false; }
        if (substr($host, -strlen($suffix)) !== $suffix) { return false; }
        if (strlen($host) === strlen($suffix)) { return true; }
        return substr($host, -strlen($suffix) - 1, 1) === '.';
    }
    
    /**
     * Ação da Tarefa Agendada
     * Rota: php index.php rss
     * Corrigida para buscar da API JSON e salvar TODOS os arquivos em ./data/rss/
     */
    public function loadRssAction()
    {
        $pathBase = self::RSS_PATH_BASE; // Caminho: ./data/rss/

        echo 'Carregando RSS do INMET...' . PHP_EOL;
        $errors = [];
        // Tenta a primeira URL que você disse que funciona
        $inmetFeed = $this->downloadFeed('https://apiprevmet3.inmet.gov.br/avisos/rss', $errors);
        
        if ($inmetFeed === null) {
            echo 'Fallback 1: https://alerts.inmet.gov.br/cap_12/rss/alert-as.rss' . PHP_EOL;
            $inmetFeed = $this->downloadFeed('https://alerts.inmet.gov.br/cap_12/rss/alert-as.rss', $errors);
        }
        if ($inmetFeed === null) {
            echo 'Fallback 2: https://alertas2.inmet.gov.br/rss' . PHP_EOL;
            $inmetFeed = $this->downloadFeed('https://alertas2.inmet.gov.br/rss', $errors);
        }
        
        $targetFile = $pathBase . 'inmet.xml'; // <-- Salva como XML
        
        if ($inmetFeed !== null && is_string($inmetFeed) && strpos($inmetFeed, '<rss') !== false) { // Validação XML simples
            $bytes = @file_put_contents($targetFile, $inmetFeed);
            if ($bytes > 0) {
                echo 'SUCESSO: INMET salvo em ' . $targetFile . ' (' . $bytes . ' bytes escritos)' . PHP_EOL;
            } else {
                echo 'FALHA AO GRAVAR: Nao foi possivel escrever o arquivo em ' . $targetFile . '. Verifique as permissoes da pasta.' . PHP_EOL;
            }
        } else {
            echo 'ERRO DE DOWNLOAD: Nao foi possivel baixar o feed XML. Resposta (ou erro): ' . ($inmetFeed ? substr($inmetFeed, 0, 200) : implode(' | ', $errors)) . PHP_EOL;
        }

        // Mantendo o download dos outros feeds (XML)
        echo 'Loading http://g1.globo.com/dynamo/brasil/rss2.xml'.PHP_EOL;
        @file_put_contents($pathBase . 'globo-news.xml', @file_get_contents("http://g1.globo.com/dynamo/brasil/rss2.xml"));

        echo 'Loading http://g1.globo.com/dynamo/educacao/rss2.xml'.PHP_EOL;
        @file_put_contents($pathBase . 'globo-educacao.xml', @file_get_contents("http://g1.globo.com/dynamo/educacao/rss2.xml"));

        echo 'Loading http://news.google.com.br/news?pz=1&cf=all&ned=pt-BR_br&hl=pt-BR&output=rss'.PHP_EOL;
        @file_put_contents($pathBase . 'google-news.xml', @file_get_contents("http://news.google.com.br/news?pz=1&cf=all&ned=pt-BR_br&hl=pt-BR&output=rss"));

        echo 'Downloads de RSS concluidos.' . PHP_EOL;
        die;
    }
}