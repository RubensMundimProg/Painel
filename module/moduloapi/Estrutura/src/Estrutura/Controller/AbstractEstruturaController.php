<?php
/**
 * Classe de abstração para as controllers do sistema
 * Define as funções principais do sistema
 */
namespace Estrutura\Controller;

use Classes\Service\Alertas;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\Container;

abstract class AbstractEstruturaController extends AbstractActionController{
    protected static $serviceManager;

    public function setRedirect($url, $post=[]){
        $this->setPost($post);
        if(isset($post['Id']) && $post['Id'] != ''){
            $url .= '/'.$post['Id'];
        }

        return $this->redirect()->toUrl($url);
    }

    public static function setServiceManager($serviceManager)
    {
        self::$serviceManager = $serviceManager;
    }

    public function getServiceLocator()
    {
        if (self::$serviceManager) {
            return self::$serviceManager;
        }

        $parent = get_parent_class($this);
        if ($parent && method_exists($parent, 'getServiceLocator')) {
            return parent::getServiceLocator();
        }

        return null;
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

    public function addValidateMessages(\Laminas\Form\Form $form )
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

    public function getValidateMessages(\Laminas\Form\Form $form,$type='array'){

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
        if (is_object($files) && method_exists($files, 'toArray')) {
            $files = $files->toArray();
        }

        if (!is_array($files) || !$files) {
            return [];
        }

        $primeiraChave = key($files);
        $uploadDirName = trim($diretory ?: 'anexos', '/\\');
        $uploadDir = BASE_PATCH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $uploadDirName);

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            throw new \RuntimeException('Nao foi possivel criar a pasta de anexos.');
        }

        $retorno = [];
        foreach ($this->normalizeUploadedFiles($files) as $chave => $items) {
            $paths = [];
            foreach ($items as $item) {
                if (!$this->isValidUploadedFile($item)) {
                    continue;
                }

                $filename = $this->buildUploadFilename($item['name']);
                $target = $uploadDir . DIRECTORY_SEPARATOR . $filename;

                if (is_uploaded_file($item['tmp_name'])) {
                    $moved = move_uploaded_file($item['tmp_name'], $target);
                } else {
                    $moved = rename($item['tmp_name'], $target);
                }

                if (!$moved) {
                    throw new \RuntimeException('Nao foi possivel salvar o anexo enviado.');
                }

                $paths[] = '/' . str_replace(DIRECTORY_SEPARATOR, '/', $uploadDirName) . '/' . $filename;
            }

            if ($paths) {
                $retorno[$chave] = implode('|', $paths);
            }
        }

        if (!$retorno && $primeiraChave !== null) {
            return [$primeiraChave => ''];
        }

        return $retorno;
    }

    private function normalizeUploadedFiles(array $files)
    {
        $normalized = [];

        foreach ($files as $field => $file) {
            if (!is_array($file)) {
                continue;
            }

            if (isset($file['name']) && is_array($file['name'])) {
                foreach ($file['name'] as $index => $name) {
                    $normalized[$field][] = [
                        'name' => $name,
                        'type' => $file['type'][$index] ?? null,
                        'tmp_name' => $file['tmp_name'][$index] ?? null,
                        'error' => $file['error'][$index] ?? UPLOAD_ERR_OK,
                        'size' => $file['size'][$index] ?? null,
                    ];
                }
                continue;
            }

            if (isset($file['name'])) {
                $normalized[$field][] = $file;
                continue;
            }

            foreach ($file as $item) {
                if (is_array($item) && isset($item['name'])) {
                    $normalized[$field][] = $item;
                }
            }
        }

        return $normalized;
    }

    private function isValidUploadedFile(array $file)
    {
        return isset($file['tmp_name'], $file['name'])
            && is_string($file['tmp_name'])
            && $file['tmp_name'] !== ''
            && is_file($file['tmp_name'])
            && (int)($file['error'] ?? UPLOAD_ERR_OK) === UPLOAD_ERR_OK;
    }

    private function buildUploadFilename($originalName)
    {
        $originalName = basename(str_replace('\\', '/', (string)$originalName));
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $baseName = preg_replace('/[^A-Za-z0-9._-]+/', '_', $baseName);
        $baseName = trim($baseName, '._-') ?: 'anexo';
        $suffix = date('YmdHis') . '_' . bin2hex(random_bytes(6));

        return $baseName . '_' . $suffix . ($extension ? '.' . preg_replace('/[^A-Za-z0-9]+/', '', $extension) : '');
    }

}
