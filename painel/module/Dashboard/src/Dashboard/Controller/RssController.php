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

        echo 'Loading https://alerts.inmet.gov.br/cap_12/rss/alert-as.rss'.PHP_EOL;
        file_put_contents('./data/rss/inmet.xml',file_get_contents("https://alerts.inmet.gov.br/cap_12/rss/alert-as.rss"));

        echo 'Loading http://g1.globo.com/dynamo/brasil/rss2.xml'.PHP_EOL;
        file_put_contents('./data/rss/globo-news.xml',file_get_contents("http://g1.globo.com/dynamo/brasil/rss2.xml"));

        echo 'Loading http://g1.globo.com/dynamo/educacao/rss2.xml'.PHP_EOL;
        file_put_contents('./data/rss/globo-educacao.xml',file_get_contents("http://g1.globo.com/dynamo/educacao/rss2.xml"));

        echo 'Loading http://news.google.com.br/news?pz=1&cf=all&ned=pt-BR_br&hl=pt-BR&output=rss'.PHP_EOL;
        file_put_contents('./data/rss/google-news.xml',file_get_contents("http://news.google.com.br/news?pz=1&cf=all&ned=pt-BR_br&hl=pt-BR&output=rss"));

        die;

    }

} 