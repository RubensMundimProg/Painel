<?php
/**
 * Classe de abstração para as controllers do sistema
 * Define as funções principais do sistema
 */
namespace Estrutura\Controller;

use Classes\Service\Alertas;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;

abstract class AbstractEstruturaController extends AbstractActionController{

    public function setRedirect($url, $post=[]){
        $this->setPost($post);
        if(isset($post['Id']) && $post['Id'] != ''){
            $url .= '/'.$post['Id'];
        }

        return $this->redirect()->toUrl($url);
    }

    protected $service = null;
    protected $form = null;
    protected $msgs = [];

    public function init(){
        $this->setServiceObj();
        $this->setFormObj();
    }

    public function setServiceObj(){
        if(!$this->service)
            $this->service = $this->getServiceObj();
    }

    public function setFormObj(){
        if(!$this->form)
            $this->form = $this->getFormObj();
    }

    public function getServiceObj(){
        $classe = get_class($this);
        $explode = explode('\\',$classe);

        ///Extrai os dados para variaveis
        list($namespace, $tipo, $controller) = $explode;

        if($controller == 'IndexController'){// Se controller for index seta o service do namespace
            $obj = str_replace(['Controller','IndexService'],['Service',$namespace], $classe);
        }else{// Se nao ele seta da controller
            $objeto = str_replace('Controller','',$controller);
            $obj = "\\".$namespace.'\Service\\'.$objeto;
        }

        $service = new $obj;
        return $service;
    }

    public function getFormObj(){
        $classe = get_class($this);
        $explode = explode('\\',$classe);

        ///Extrai os dados para variaveis
        list($namespace, $tipo, $controller) = $explode;

        if($controller == 'IndexController'){// Se controller for index seta o service do namespace
            $obj = str_replace(['Controller','IndexForm'],['Form',$namespace], $classe);
        }else{// Se nao ele seta da controller
            $objeto = str_replace('Controller','',$controller);
            $obj = "\\".$namespace.'\Form\\'.$objeto;
        }

        $form = new $obj;
        return $form;
    }

    public function baseUrl(){
        return 'dudys.local';
    }

    public function getPost(){
        $container = new Container('Post');
        $dados = $container->offsetGet('dados');
        $container->offsetUnset('dados');

        return $dados;
    }

    public function setPost($post){
        $container = new Container('Post');
        $container->offsetSet('dados',$post);
    }

    public function addErrorMessage($message)
    {
        if(!is_array($message)) $message = array($message);

        foreach($message as $msg)
        {
            $arrErros = $this->flashMessenger()->getCurrentErrorMessages();
            if(!in_array($msg,$arrErros))
                $this->flashMessenger()->addErrorMessage($msg);
        }
        return;
    }

    public function addSuccessMessage($message)
    {
        if(!is_array($message)) $message = array($message);

        foreach($message as $msg)
        {
            $this->flashMessenger()->addSuccessMessage($message);
        }

        return;
    }

    public function addInfoMessage($message)
    {
        if(!is_array($message)) $message = array($message);

        foreach($message as $msg)
        {
            $this->flashMessenger()->addInfoMessage($message);
        }
    }

    public function addValidateMessages(\Zend\Form\Form $form )
    {
        $arrMsgs = $form->getMessages();

        if(!is_array($arrMsgs) ) return ;
        foreach($arrMsgs as $atributo => $mensagens )
        {
            foreach($mensagens as $mensagem )
            {
                $attr = $form->get($atributo)->getLabel() ? $form->get($atributo)->getLabel() : $atributo;
                $mensagemPro = 'O Campo '.$attr.' é de preenchimento obrigatório';
                $this->addErrorMessage( $mensagemPro );

//                PARA USAR NO RETORNO JSON
                $this->msgs[]= $mensagemPro;
            }
        }
    }

    public function getValidateMessages(\Zend\Form\Form $form,$type='array'){

        $this->addValidateMessages($form);

        $permitidos = ['json','string','array'];

        if(!in_array($type,$permitidos)) throw new \Exception('Formato inválido!');

        if($type=='json'){
            return json_encode($this->msgs);
        }

        if($type=='string'){
            $msgs_tratadas = '';
            foreach($this->msgs as $item){
                $msgs_tratadas = $msgs_tratadas.$item."<br/>";
            }

            return $msgs_tratadas;
        }

        return $this->msgs;

    }

    public function uploadFile($files,$diretory = '')
    {
        $chave = key($files);

        $retorno[$chave] = '';
        foreach($files as $file){
            if(!isset($file['name'])){
                foreach($file as $item){
                    $filter = new \Zend\Filter\File\RenameUpload('./public/anexos/');
                    $filter->setUseUploadName(true);
                    $filter->setUseUploadExtension(true);
                    $filter->setRandomize(true);
                    $filter->setOverwrite(false);
                    $name = $filter->filter($item);
                    $retorno[$chave] .= '|'.str_replace('./public','',$name['tmp_name']);
                }
            }else{
                $item = $file;
                $filter = new \Zend\Filter\File\RenameUpload('./public/anexos/');
                $filter->setUseUploadName(true);
                $filter->setUseUploadExtension(true);
                $filter->setRandomize(true);
                $filter->setOverwrite(false);
                $name = $filter->filter($item);
                $retorno[$chave] .= '|'.str_replace('./public','',$name['tmp_name']);
            }

        }

        $retorno[$chave] = ltrim($retorno[$chave], '|');
        return $retorno;
    }

}