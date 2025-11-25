<?php
/**
 * Classe de abstração para as controllers de crud do sistema
 * Define as funções principais dos cruds do sistema
 */

namespace Estrutura\Controller;

use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

abstract class AbstractCrudController extends AbstractEstruturaController{
    public function index($service,$form,$atributos=[]){
        $dadosView = [
            'service'=>$service,
            'form'=>$form,
            'lista'=>$service->filtrarObjeto(),
            'controller'=>$this->params('controller'),
            'atributos'=>$atributos
        ];
        return new ViewModel($dadosView);
    }
    public function cadastro($service,$form,$atributos=[]){
            $id = $this->params("id");
            if($id){
                $form->setData($service->buscar($id)->toArray());
            }

            if($post = $this->getPost()){
                $form->setData($post);
            }

            $dadosView = [
                'service'=>$service,
                'form'=>$form,
                'controller'=>$this->params('controller'),
                'atributos'=>$atributos
            ];

            return new ViewModel($dadosView);
    }

    public function gravar($service, $form){
        try{
            $controller = $this->params('controller');
            $request = $this->getRequest();

            if(!$request->isPost()){
                throw new \Exception('Dados Inválidos');
            }

            $post = $request->getPost()->toArray();
            $files = $request->getFiles();
            $uplod = $this->uploadFile($files);

            $post = array_merge($post, $uplod);
            $form->setData($post);

            if(!$form->isValid()){
                $this->addValidateMessages($form);
                $this->setPost($post);
                $this->redirect()->toRoute('navegacao',array('controller'=>$controller,'action'=>'cadastro'));
                return false;
            }

            $service->exchangeArray($form->getData());
            $service->salvar();
            $this->addSuccessMessage('Registro salvo com sucesso!');
            $this->redirect()->toRoute('navegacao',array('controller'=>$controller,'action'=>'index'));
            return false;
        }catch (\Exception $e){
            $this->setPost($post);
            $this->addErrorMessage($e->getMessage());
            $this->redirect()->toRoute('navegacao',array('controller'=>$controller,'action'=>'cadastro'));
            return false;
        }
    }

    public function excluir($service,$form,$atributos=[]){
        try{
            $request = $this->getRequest();
            if($request->isPost()){
                return new JsonModel();
            }

            $controller = $this->params('controller');

            $id = $this->params('id');
            $service->setId($id);

            $dados = $service->filtrarObjeto()->current();

            if(!$dados)
                throw new \Exception('Registro não encontrado');

            $service->excluir();
            $this->addSuccessMessage('Registro excluido com sucesso');

            return $this->redirect()->toRoute('navegacao',['controller'=>$controller]);
        }catch (\Exception $e){
            debug($e->getMEssage());
        }
    }

    public function uploadFile($files)
    {
        $retorno = [];
        foreach($files as $name => $file){
            $filter = new \Zend\Filter\File\RenameUpload('./data/');
            $filter->setUseUploadName(true);
            $filter->setOverwrite(true);
            $filter->filter($file);
            $retorno[$name] = $file['name'];
        }
        return $retorno;
    }
}