<?php

namespace Dashboard\Controller;

use Dashboard\Service\Phirehose;
use Dashboard\Service\FilterTrackConsumer;

use Dashboard\Service\Twitter;
use Dashboard\Service\TwitterAPIExchange;
use Estrutura\Controller\AbstractEstruturaController;
use Estrutura\Service\Config;
use Zend\Serializer\Adapter\Json;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class TwitterController extends AbstractEstruturaController
{

    public function indexAction()
    {
        return new ViewModel();
    }

    public function infoAction()
    {
        echo phpinfo();die;
    }

    public function saveDataAction()
    {
        $dados = $this->tweetsAction(true);
        file_put_contents('./data/arquivos/twitter.json',$dados);
        echo 'finalizado!';die;
    }

    public function tweetsAction($return=false)
    {

        $txt = file_get_contents('./data/twitter/tags.txt');
        $words = explode(';',$txt);
        ksort($words);

        $dados = [];
        foreach($words as $word){
            if($return){
                echo 'processando a palavra: '.$word."\n\r";
            }
            $dados[$word] = $this->tratarJsonTwitter($word);
            if($return){
                echo 'finalizado a palavra: '.$word."\n\r";
                sleep(1);
            }
        }

        if($return)return json_encode($dados);
        return new JsonModel($dados);
    }

    protected function tratarJsonTwitter($tag)
    {

        /** Perform a GET request and echo the response **/
        /** Note: Set the GET field BEFORE calling buildOauth(); **/
        $settings = Config::getConfig('twitter');
        $url = 'https://api.twitter.com/1.1/search/tweets.json';
        $getfield = '?q='.$tag;
        $requestMethod = 'GET';
        $twitter = new TwitterAPIExchange($settings);
        $dados = $twitter->setGetfield($getfield)
                ->buildOauth($url, $requestMethod)
                ->performRequest();

        $array = json_decode($dados,true);
        $tratado = [];

        foreach($array['statuses'] as $tweet){
            $aux = ['created_at'=>date('d/m H:i:s', strtotime($tweet['created_at'])),'message'=>$tweet['text'],'user_id'=>$tweet['user']['id'],'user'=>$tweet['user']['screen_name'],'name'=>$tweet['user']['name'],'location'=>$tweet['user']['location'],'image'=>$tweet['user']['profile_image_url']];
            if(isset($tweet['entities']['media']['media_url'])){
                $aux = array_merge(['tweet_image'=>$tweet['entities']['media']['media_url']],$aux);
            }

            $tratado[] = $aux;
        }

        return $tratado;

    }

    public function tagsAction()
    {
        $txt = file_get_contents('./data/twitter/tags.txt');
        echo $txt;die;
    }

    public function getTweetsJsonAction(){
        $tweets = file_get_contents('./data/arquivos/twitter.json');

        echo $tweets;die;
    }

    public function loadTagsAction()
    {
        $txt = file_get_contents('./data/twitter/tags.txt');
        $txt = str_replace('+',' ',$txt);
        return new JsonModel([$txt]);
    }

    public function saveTagsAction()
    {
        try {
            $post = [];
            $request = $this->getRequest();
            $post = $request->getPost();

            $words = explode(';',rtrim($post['words'],';'));

            $tratado = [];
            foreach($words as $word){
                if($word) $tratado[$word] = $word;
            }

            $tags = implode(';', array_keys($tratado));

            $tags = str_replace(' ','+',$tags);
            file_put_contents('./data/twitter/tags.txt',$tags);
            file_put_contents('./data/twitter/alterado.txt',1);

            $retorno  = ['error'=>false,'words'=>$tags];

        }catch (\Exception $e){
            $retorno  = ['error'=>true,'msg'=>'Não foi possível efetuar a alteração!'];
        }

        return new JsonModel($retorno);
    }

    public function streamingAction()
    {
        file_put_contents('./data/twitter/alterado.txt',0);
        $twitter = new Twitter();
        $twitter->start();
    }

    public function restartStreamingAction()
    {
        if(file_get_contents('./data/twitter/alterado.txt')){
            file_put_contents('./data/twitter/alterado.txt',0);
            $twitter = new Twitter();
            $twitter->start();
        }else{
            die;
        }
    }

    public function startAction()
    {

        $txts = file_get_contents('.'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'twitter'.DIRECTORY_SEPARATOR.'tags.txt');
        $words = explode(';',$txts);
        ksort($words);

        $aux = [];
        foreach($words as $word){

            $txt = '';
            if(file_exists('.'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'arquivos'.DIRECTORY_SEPARATOR.'tweets'.DIRECTORY_SEPARATOR.''.$this->cleanWordAction($word).'.json')){
                $txt = json_decode(file_get_contents('.'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'arquivos'.DIRECTORY_SEPARATOR.'tweets'.DIRECTORY_SEPARATOR.''.$this->cleanWordAction($word).'.json'));
            }else{
                file_put_contents('.'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'arquivos'.DIRECTORY_SEPARATOR.'tweets'.DIRECTORY_SEPARATOR.''.$this->cleanWordAction($word).'.json','');
            }

            $aux[$word] = $txt;
        }

        return new JsonModel($aux);
    }

    public function cleanWordAction($word)
    {
        return trim(str_replace('?','',utf8_decode($word)));
    }

    public function getTweetsAction()
    {
        $dados = json_decode(file_get_contents('./data/arquivos/tweets/vestibular.json'),true);
        debug($dados);
    }

} 