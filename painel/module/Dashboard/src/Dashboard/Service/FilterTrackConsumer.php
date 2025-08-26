<?php
/**
 * Created by PhpStorm.
 * User: bruno.rosa
 * Date: 22/10/15
 * Time: 11:28
 */

namespace Dashboard\Service;

use Dashboard\Service\Phirehose;
use Dashboard\Service\OauthPhirehose;

class FilterTrackConsumer extends OauthPhirehose
{
    /**
     * Enqueue each status
     *
     * @param string $status
     */

    public $qtTweetsInFile = 20;

    public function enqueueStatus($status)
    {
        /*
         * In this simple example, we will just display to STDOUT rather than enqueue.
         * NOTE: You should NOT be processing tweets at this point in a real application, instead they should be being
         *       enqueued and processed asyncronously from the collection process.
         */

        $txt = file_get_contents('./data/twitter/tags.txt');
        $words = explode(';',$txt);
        ksort($words);

        $datas = json_decode($status, true);

        $data = ['created_at'=>date('d/m H:i:s', strtotime($datas['created_at'])),'message'=>$datas['text'],'user_id'=>$datas['user']['id'],'user'=>$datas['user']['screen_name'],'name'=>$datas['user']['name'],'location'=>$datas['user']['location'],'image'=>$datas['user']['profile_image_url']];
        if(isset($datas['entities']['media'][0]['media_url'])){
            $data = array_merge(['tweet_image'=>$datas['entities']['media'][0]['media_url']],$data);
        }

        if (is_array($data) && isset($data['created_at'])) {

            foreach($words as $word){
                $dadosTexto = explode(' ', strtolower($data['message']));
                $dadosWord = explode('+', strtolower($word));

                $dadosAntigos = [];

                if(count($dadosWord) > 1){
                    $tem = true;
                    foreach($dadosWord as $dword){
                        if(!in_array($dword, $dadosTexto)){
                            $tem = false;
                        }
                    }

                    if($tem){
                        $arquivo = $word;
                        if(file_exists('./data/arquivos/tweets/'.utf8_decode($arquivo).'.json')){
                            $dadosAntigos = json_decode(file_get_contents('./data/arquivos/tweets/'.utf8_decode($arquivo).'.json'),true);
                            $dadosAntigos[] = $data;
                            if(count($dadosAntigos) >= $this->qtTweetsInFile){
                                $aux = count($dadosAntigos) - $this->qtTweetsInFile;
                                $dadosAntigos = array_slice($dadosAntigos, $aux);
                            }
                            file_put_contents('./data/arquivos/tweets/'.utf8_decode($arquivo).'.json',json_encode($dadosAntigos));
                        }else{
                            file_put_contents('./data/arquivos/tweets/'.utf8_decode($arquivo).'.json',json_encode([$data]));
                        }
                    }
                }else{
                    if(in_array($word, $dadosTexto)){
                        $arquivo = $word;

                        if(file_exists('./data/arquivos/tweets/'.utf8_decode($arquivo).'.json')){
                            $dadosAntigos = json_decode(file_get_contents('./data/arquivos/tweets/'.utf8_decode($arquivo).'.json'),true);
                            $dadosAntigos[] = $data;
                            if(count($dadosAntigos) >= $this->qtTweetsInFile){
                                $aux = count($dadosAntigos) - $this->qtTweetsInFile;
                                $dadosAntigos = array_slice($dadosAntigos, $aux);
                            }
                            file_put_contents('./data/arquivos/tweets/'.utf8_decode($arquivo).'.json',json_encode($dadosAntigos));
                        }else{
                            file_put_contents('./data/arquivos/tweets/'.utf8_decode($arquivo).'.json',json_encode([$data]));
                        }
                    }
                }


            }

            echo $data['created_at'] . ' => ' . $data['user'] . "\n\r";
        }
    }
} 