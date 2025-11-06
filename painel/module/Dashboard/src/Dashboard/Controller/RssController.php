<?php

namespace Dashboard\Controller;

use Estrutura\Controller\AbstractEstruturaController;
use Zend\View\Model\JsonModel;

class RssController extends AbstractEstruturaController
{

    public function getRssAction()
    {
        try{

            $txt = file_get_contents('./data/twitter/tags.txt');
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

            $xmls = scandir('./data/rss/');
            $rss = new \DOMDocument();

            $tratados = [];
            foreach ($xmls as $xml) {

                if(in_array($xml,['.','..'])) continue;

                $rss->load('./data/rss/'.$xml);

                $name = str_replace('.xml','',$xml);

                foreach ($rss->getElementsByTagName('item') as $node)
                {
                    if(preg_match('/inmet/',$name)){
                        $pieces = explode('<br />',nl2br($node->nodeValue));
                        $info = explode('<tr>',$pieces[3]);
                        $descricao = strip_tags(explode('<td>',$info[6])[1]);
                        $area = strip_tags(explode('<td>',$info[7])[1]);
                        $tratados[$name][] = [
                            'title' => $area.' => '.$descricao,
                            'date' => date('d/m/Y H:i', strtotime($node->getElementsByTagName('pubDate')->item(0)->nodeValue))
                        ];
                        continue;
                    }
                    if ($this->substrCountArray( $node->getElementsByTagName('title')->item(0)->nodeValue, $words ) > 0) {
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

        public function inmetAvisosAction()
    {
        try {
            $result = $this->fetchInmetAlerts();

            return new JsonModel([
                'error' => false,
                'message' => $result['message'],
                'alerts' => $result['alerts'],
                'fetched_at' => $result['fetched_at'],
                'source' => $result['source'],
                'source_url' => $result['source_url'],
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'error' => true,
                'message' => $e->getMessage(),
                'alerts' => [],
            ]);
        }
    }

    protected function fetchInmetAlerts()
    {
        $feedUrls = [
            'https://apiprevmet3.inmet.gov.br/avisos/rss',
            'https://alerts.inmet.gov.br/cap_12/rss/alert-as.rss',
        ];
        $timezone = new \DateTimeZone('America/Sao_Paulo');
        $result = [
            'alerts' => [],
            'message' => '',
            'source' => 'live',
            'fetched_at' => (new \DateTime('now', $timezone))->format(DATE_ATOM),
            'source_url' => null,
        ];

        $errors = [];
        $response = null;
        foreach ($feedUrls as $feedUrl) {
            $response = $this->downloadInmetFeed($feedUrl, $errors);
            if ($response !== null) {
                $result['source_url'] = $feedUrl;
                break;
            }
        }

        if ($response === null) {
            $cachePath = $this->getInmetCachePath();
            $cached = $this->loadCachedInmetFeed($cachePath);

            if ($cached !== null) {
                $result['message'] = $this->buildCacheMessage($errors);
                $result['source'] = 'cache';
                $result['fetched_at'] = $this->formatCacheTimestamp($cachePath, $timezone);
                $response = $cached;
            }
        }

        if ($response === null) {
            $errorMessage = 'Não foi possível recuperar os dados meteorológicos do INMET.';
            if (!empty($errors)) {
                $errorMessage .= ' ' . implode(' ', $errors);
            }

            throw new \RuntimeException(trim($errorMessage));
        }

        $normalizedResponse = $this->normalizeFeedResponse($response);

        $rss = @simplexml_load_string($normalizedResponse);
        if ($rss === false || !isset($rss->channel)) {
            throw new \RuntimeException('O INMET retornou uma resposta inválida.');
        }

        $limit = 12;
        $count = 0;
        foreach ($rss->channel->item as $item) {
            $description = (string) $item->description;
            $parsedDescription = $this->parseInmetDescription($description);

            $publishedAt = $this->formatDateTime((string) $item->pubDate);

            $result['alerts'][] = array_merge([
                'title' => trim((string) $item->title),
                'link' => trim((string) $item->link),
                'guid' => trim((string) $item->guid),
                'published_at' => $publishedAt['human'],
                'published_at_iso' => $publishedAt['iso'],
            ], $parsedDescription);

            $count++;
            if ($count >= $limit) {
                break;
            }
        }

        return $result;
    }

    protected function downloadInmetFeed($url, array &$errors = [])
    {
        $headers = [
            'User-Agent: PainelDashboard/1.0',
            'Accept: application/rss+xml, application/xml;q=0.9, */*;q=0.8',
            'Accept-Encoding: identity',
        ];

        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'header' => implode("\r\n", $headers) . "\r\n",
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response !== false) {
            return $response;
        }

        $attemptErrors = [sprintf('Falha ao acessar %s.', $url)];

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_USERAGENT => 'PainelDashboard/1.0',
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_ENCODING => '',
            ]);
            $result = curl_exec($ch);
            if ($result === false) {
                $attemptErrors[] = sprintf('cURL: %s', curl_error($ch));
            }
            curl_close($ch);

            if ($result !== false) {
                return $result;
            }
        }

        $errors = array_merge($errors, $attemptErrors);
        return null;
    }

    protected function normalizeFeedResponse($response)
    {
        if ($response === null || $response === '') {
            return '';
        }

        if (strpos($response, '<rss') !== false) {
            return $response;
        }

        if (function_exists('gzdecode')) {
            $decoded = @gzdecode($response);
            if ($decoded !== false && strpos($decoded, '<rss') !== false) {
                return $decoded;
            }
        }

        if (function_exists('gzinflate')) {
            $inflated = @gzinflate($response);
            if ($inflated !== false && strpos($inflated, '<rss') !== false) {
                return $inflated;
            }
        }

        return $response;
    }

    protected function buildCacheMessage(array $errors)
    {
        if (empty($errors)) {
            return 'Avisos carregados a partir da última sincronização local.';
        }

        return 'Avisos carregados a partir da última sincronização local. ' . implode(' ', $errors);
    }

    protected function getInmetCachePath()
    {
        $rootDir = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
        $path = $rootDir . '/data/rss/inmet.xml';
        return file_exists($path) ? $path : null;
    }

    protected function loadCachedInmetFeed($path)
    {
        if ($path === null || !is_readable($path)) {
            return null;
        }

        $contents = @file_get_contents($path);
        return $contents !== false ? $contents : null;
    }

    protected function formatCacheTimestamp($path, \DateTimeZone $timezone)
    {
        if ($path === null || !file_exists($path)) {
            return (new \DateTime('now', $timezone))->format(DATE_ATOM);
        }

        $modified = @filemtime($path);
        if ($modified === false) {
            return (new \DateTime('now', $timezone))->format(DATE_ATOM);
        }

        $date = new \DateTime('@' . $modified);
        $date->setTimezone($timezone);

        return $date->format(DATE_ATOM);
    }

    protected function parseInmetDescription($html)
    {
        $details = [
            'status' => null,
            'event' => null,
            'severity' => null,
            'start' => null,
            'start_iso' => null,
            'end' => null,
            'end_iso' => null,
            'description' => null,
            'area' => null,
            'graphic' => null,
        ];

        if (trim($html) === '') {
            return $details;
        }

        $document = new \DOMDocument();
        $previousLibxml = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();
        libxml_use_internal_errors($previousLibxml);

        $rows = $document->getElementsByTagName('tr');
        foreach ($rows as $row) {
            /** @var \DOMElement|null $header */
            $header = $row->getElementsByTagName('th')->item(0);
            /** @var \DOMElement|null $valueNode */
            $valueNode = $row->getElementsByTagName('td')->item(0);

            if (!$header || !$valueNode) {
                continue;
            }

            $normalizedKey = $this->normalizeKey($header->textContent);
            $value = trim($valueNode->textContent);

            switch ($normalizedKey) {
                case 'status':
                    $details['status'] = $value;
                    break;
                case 'evento':
                    $details['event'] = $value;
                    break;
                case 'severidade':
                    $details['severity'] = $value;
                    break;
                case 'inicio':
                    $start = $this->formatDateTime($value);
                    $details['start'] = $start['human'];
                    $details['start_iso'] = $start['iso'];
                    break;
                case 'fim':
                    $end = $this->formatDateTime($value);
                    $details['end'] = $end['human'];
                    $details['end_iso'] = $end['iso'];
                    break;
                case 'descricao':
                    $details['description'] = $value;
                    break;
                case 'area':
                    $details['area'] = $value;
                    break;
                case 'link_grafico':
                    $linkNode = $valueNode->getElementsByTagName('a')->item(0);
                    $details['graphic'] = $linkNode ? trim($linkNode->getAttribute('href')) : $value;
                    break;
                default:
                    break;
            }
        }

        return $details;
    }

    protected function normalizeKey($key)
    {
        $normalized = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $key);
        if ($normalized === false) {
            $normalized = $key;
        }
        $normalized = strtolower($normalized);
        $normalized = preg_replace('/[^a-z]+/', '_', $normalized);
        return trim($normalized, '_');
    }

    protected function formatDateTime($value)
    {
        $value = trim($value);
        if ($value === '') {
            return [
                'human' => null,
                'iso' => null,
            ];
        }

        $normalized = preg_replace('/\.\d+$/', '', $value);
        $timezone = new \DateTimeZone('America/Sao_Paulo');
        $formats = [DATE_RSS, 'Y-m-d H:i:s', 'Y-m-d H:i'];

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $normalized, $timezone);
            if ($date instanceof \DateTime) {
                return [
                    'human' => $date->format('d/m/Y H:i'),
                    'iso' => $date->format(DATE_ATOM),
                ];
            }
        }

        try {
            $date = new \DateTime($normalized, $timezone);
            return [
                'human' => $date->format('d/m/Y H:i'),
                'iso' => $date->format(DATE_ATOM),
            ];
        } catch (\Exception $e) {
            return [
                'human' => $value,
                'iso' => null,
            ];
        }
    }

    public function substrCountArray( $haystack, $needle ) {
        $count = 0;
        foreach ($needle as $substring) {
            $count += substr_count( $haystack, $substring);
        }
        return $count;
    }

    public function loadRssAction()
    {
        //CARREGA TODOS OS RSS

        echo 'Loading https://apiprevmet3.inmet.gov.br/avisos/rss'.PHP_EOL;
        file_put_contents('./data/rss/inmet.xml',file_get_contents("https://apiprevmet3.inmet.gov.br/avisos/rss"));

        echo 'Loading http://g1.globo.com/dynamo/brasil/rss2.xml'.PHP_EOL;
        file_put_contents('./data/rss/globo-news.xml',file_get_contents("http://g1.globo.com/dynamo/brasil/rss2.xml"));

        echo 'Loading http://g1.globo.com/dynamo/educacao/rss2.xml'.PHP_EOL;
        file_put_contents('./data/rss/globo-educacao.xml',file_get_contents("http://g1.globo.com/dynamo/educacao/rss2.xml"));

        echo 'Loading http://news.google.com.br/news?pz=1&cf=all&ned=pt-BR_br&hl=pt-BR&output=rss'.PHP_EOL;
        file_put_contents('./data/rss/google-news.xml',file_get_contents("http://news.google.com.br/news?pz=1&cf=all&ned=pt-BR_br&hl=pt-BR&output=rss"));

        die;
