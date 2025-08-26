<?php

namespace Application\Controller;

use Application\Service\Categoria;
use Classes\Service\Acesso;
use Classes\Service\Atividade;
use Classes\Service\Aviso;
use Estrutura\Controller\AbstractEstruturaController;
use Estrutura\Service\Conexao;
use Estrutura\Service\Config;
use Modulo\Service\UsuarioApi;
use RiskManager\OData\Filter;
use RiskManager\Workflow\Service\Event;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CalendarioController extends AbstractEstruturaController {

    public $progressKeyTranslation = [
        'UpdatedBy'=>'Atualizado Por',
        'Date'=>'Data',
//        'UpdateType'=>'Tipo Atualização',
        'Action'=>'Tipo Ação',
        'Comment'=>'Comentário',
//        'Property'=>'Campo',
//        'OldValue'=>'Antigo Valor',
//        'NewValue'=>'Novo Valor',
//        'AdditionalParameter'=>'Parâmetro Adicional',
    ];
    public $progressUpdateType = [
        'Propriedade do evento atualizada.',
        'Anexo adicionado.',
        'Progresso atualizado ou mudou a situação do evento.',
    ];
    public $progressActionType = [
        'Evento criado.',
        'Evento fechado.',
        'Evento reaberto.',
        'Evento cancelado.',
        'Evento desassociado.',
        'Progresso atualizado.',
        'Evento subordinado criado.',
        'Evento subordinado fechado.',
        'Evento subordinado reaberto.',
        'Evento subordinado cancelado.',
        'Evento subordinado excluído.',
        'Evento subordinado desassociado.',
        'Progresso de evento subordinado atualizado.',
        'Propriedade de evento atualizada.',
        'Envolvidos adicionados.',
        'Envolvidos removidos.',
        'Ativos associados.',
        'Ativos removidos.',
        'Anexo adicionado à aba Progresso do evento.',
        'Propriedade de um evento subordinado atualizada.',
        'Envolvidos adicionados a um evento subordinado.',
        'Envolvidos removidos de um evento subordinado.',
        'Ativos associados a um evento subordinado.',
        'Ativos removidos de um evento subordinado.',
        'Arquivos anexados à aba Progresso de um evento subordinado.',
        'Evento atual associado a um evento pai.',
        'Evento subordinado associado a um evento pai.',
        'Componentes de negócio associados.',
        'Componentes de negócio desassociados.',
        'Componentes de negócio associados a um evento subordinado.',
        'Componentes de negócio desassociados de um evento subordinado.',
        'Plano de continuidade associado a um evento.',
        'Plano de continuidade desassociado de um evento.',
        'Riscos corporativos associados a um evento.',
        'Riscos corporativos desassociados de um evento.',
        'Arquivos anexados a um atributo de evento.',
        'Arquivos removidos de um atributo de evento.',
        'Eventos associados.',
        'Eventos desassociados.',
        'Arquivos anexados a um atributo de um evento subordinado.',
        'Arquivos removidos de um atributo de um evento subordinado.',
        'Atualização de arquivo de um atributo de um evento.',
        'Atualização de arquivo de um atributo de um evento subordinado.'
    ];
    public function indexAction()
    {
        return new ViewModel();
    }

    public function loadEventsAction(){
//        if($this->params('id') || !file_exists('./data/cache/calendar.json')){
            $cores = [
                'indigo',
                'blue',
                'blue-grey',
                'bege',
                'terra-cota',
                'dark-red',
                'brown',
                'salmao',
                'pink',
                'purple',
                'deep-purple',
                'grey',
                'teal',
                'yellow',
                'amber',
                'CornflowerBlue',
                'DarkSlateBlue',
                'SlateBlue',
                'MediumSlateBlue',
                'LightSlateBlue',
                'MediumBlue',
                'RoyalBlue',
                'Blue',
                'DodgerBlue',
                'DeepSkyBlue',
                'SkyBlue',
                'LightSkyBlue',
                'SteelBlue',
                'LightSteelBlue',
                'LightBlue',
                'PowderBlue',
                'PaleTurquoise',
                'DarkTurquoise',
                'MediumTurquoise',
                'Turquoise',
                'Cyan',
                'LightCyan',
            ];

            $this->showlog('Carregando os eventos do tipo: Controle de Processos.');
            $event = new Event();
            $event->setAnonimo();

            $filter = new Filter();
            $filter->where(Filter::F_EQ,Filter::A_STRING, 'EventType','Controle de Processos');
            $filter->setPageSize(1000);
            $event->setFilter($filter);
            $lista = $event->fetchAll();


            $listaAgrupada = $lista;

            $page = 2;
            while(count($lista) == 1000){
                $filter->setPage($page);
                $event->setFilter($filter);
                $lista = $event->fetchAll();
                $listaAgrupada = array_merge($listaAgrupada,$lista);
                $page++;
            }

            $this->showlog(count($listaAgrupada).' eventos encontrados.');

            $sistemas = [];
            $tratado = [];
            $tratadoGantt = [];
            $childEventsAlreadyChecked = [];
            $this->showlog('Modelando os dados...');
            foreach($listaAgrupada as $item){

                if(!$item->getStartDate()) continue;

                if($item->getStatus() == 0) continue;

                if(in_array($item->getCode(),$childEventsAlreadyChecked)) continue;

                // GET CUSTOM ATTR
                $attributes = $item->getCustomAttributes(true);
                if(!isset($attributes['sistema'])) continue;

                // GET PROGRESS
                $progressListTratato = $this->getProgressOfIt($item->getCode());

                // CALC USR
                $usr = $this->calcUsrAndDeadlineDefineColor($item->getUrgency(),$item->getRelevance(),$item->getSeverity(),$item->epocToDate($item->getEndDate(),'Y-m-d'),$item->getStatus(),$progressListTratato);

                $evento = [
                    'className'=>$usr,
                    'code'=>$item->getCode(),
                    'title'=>'['.$attributes['sistema'].'] '.$item->getTitle(),
                    'description'=>$item->getDescription(),
                    'start'=>$item->epocToDate($item->getStartDate(),'Y-m-d'),
                    'end'=>$item->epocToDate($item->getEndDate(),'Y-m-d'),
                    'expectedStart'=>$item->epocToDate($item->getExpectedStartDate(),'Y-m-d'),
                    'expectedEnd'=>$item->epocToDate($item->getExpectedEndDate(),'Y-m-d'),
                    'color'=>$attributes['sistema'],
                    'progress'=>json_encode($progressListTratato)
                ];

                $tratado[] = $evento;

                $startDate = $item->getStartDate();
                if(preg_match('/1970/',$item->epocToDate($item->getStartDate(),'Y'))){
                    $startDate = $item->getCreated();
                }
                $tratadoGantt[] = [
                    'name'=>'['.$attributes['sistema'].'] '.$item->getTitle(),
                    'desc'=>$item->getDescription(),
                    'color'=>$attributes['sistema'],
                    'values'=>[
                        [
                            'from'=> $startDate,
                            'to'=> $item->getEndDate(),
                            'desc'=> $item->getTitle(),
                            'label'=> $item->getTitle(),
                            'customClass'=> $usr,
                            'dataObj'=>$evento
                        ]
                    ]
                ];

                //CHECK IF IT HAS CHILDREN
                $childrenEvent = new Event();
                $childrenEvent->setAnonimo();
                $childrenFilter = new Filter();
                $childrenFilter->where(Filter::F_EQ,Filter::A_STRING, 'ParentEvent',$item->getCode());
                $childrenFilter->setPageSize(1000);
                $childrenEvent->setFilter($childrenFilter);
                $childrenEvents = $childrenEvent->fetchAll();

                foreach ($childrenEvents as $childEvent) {

                    if($childEvent->getStatus() == 0) continue;
                    if(!$childEvent->getStartDate()) continue;

                    // GET CUSTOM ATTR
                    $childAttributes = $childEvent->getCustomAttributes(true);
                    if(!isset($childAttributes['sistema'])) continue;

                    // GET PROGRESS
                    $progressListChildTratato = $this->getProgressOfIt($childEvent->getCode());

                    // CALC USR
                    $usr = $this->calcUsrAndDeadlineDefineColor($childEvent->getUrgency(),$childEvent->getRelevance(),$childEvent->getSeverity(),$childEvent->epocToDate($childEvent->getEndDate(),'Y-m-d'),$childEvent->getStatus(),$progressListChildTratato);


                    $eventoChild = [
                        'className'=>$usr.' child',
                        'code'=>$childEvent->getCode(),
                        'title'=>'['.$childAttributes['sistema'].'] '.$childEvent->getTitle(),
                        'description'=>$childEvent->getDescription(),
                        'start'=>$childEvent->epocToDate($childEvent->getStartDate(),'Y-m-d'),
                        'end'=>$childEvent->epocToDate($childEvent->getEndDate(),'Y-m-d'),
                        'expectedStart'=>$childEvent->epocToDate($item->getExpectedStartDate(),'Y-m-d'),
                        'expectedEnd'=>$childEvent->epocToDate($item->getExpectedEndDate(),'Y-m-d'),
                        'color'=>$childAttributes['sistema'],
                        'progress'=>json_encode($progressListChildTratato)
                    ];
                    $tratado[] = $eventoChild;

                    $childEventstartDate = $childEvent->getStartDate();
                    if(preg_match('/1970/',$childEvent->epocToDate($childEvent->getStartDate(),'Y'))){
                        $childEventstartDate = $childEvent->getCreated();
                    }
                    $tratadoGantt[] = [
                        'name'=>'['.$childAttributes['sistema'].'] '.$childEvent->getTitle(),
                        'desc'=>$childEvent->getDescription(),
                        'color'=>$childAttributes['sistema'],
                        'values'=>[
                            [
                                'from'=> $childEventstartDate,
                                'to'=> $childEvent->getEndDate(),
                                'desc'=> $childEvent->getTitle(),
                                'label'=> $childEvent->getTitle(),
                                'customClass'=> $usr.' child',
                                'dataObj'=>$eventoChild
                            ]
                        ]
                    ];

                    $childEventsAlreadyChecked[] = $childEvent->getCode();
                }

                $sistemas[$attributes['sistema']] = 1;

            }

            $nTratado = [];
            $legenda = [];
            foreach($tratado as $item){
                $key = array_search($item['color'], array_keys($sistemas));
                $sistema = $item['color'];
//                $item['color'] = $cores[$key];
                $item['className'] = $item['className'].' '.$cores[$key];
                $nTratado[] = $item;

//                $legenda[$sistema] = $item['color'];
                $legenda[$sistema] = $cores[$key];
            }

            $nTratadoGantt = [];
            foreach($tratadoGantt as $item){
                $key = array_search($item['color'], array_keys($sistemas));
                $item['values'][0]['customClass'] = $item['values'][0]['customClass'].' '.$cores[$key];
                $nTratadoGantt[] = $item;
            }

            $this->showlog('Salvando os arquivos JSON.');
            $json = json_encode(['eventos'=>$nTratado,'legenda'=>$legenda]);
            file_put_contents('./data/cache/calendar.json', $json);
            file_put_contents('./data/cache/gantt.json', json_encode($nTratadoGantt));
            $this->showlog('Finalizado!');
            die;
//        }else{
//            echo file_get_contents('./data/cache/calendar.json');
//            die;
//        }
    }

    public function getCalendarAction()
    {
        if($this->params('id')){
            $data = json_decode(file_get_contents('./data/cache/calendar.json'), true);
            $tratado = [];
            foreach($data['eventos'] as $item){
                if($item['color'] != $this->params('id')) continue;
                $tratado[] = $item;
            }
            $data['eventos'] = $tratado;
            echo json_encode($data);
            die;
        }else{
            echo file_get_contents('./data/cache/calendar.json');
            die;
        }
    }

    public function calcUsrAndDeadlineDefineColor($urgency,$relevance,$severity,$deadline,$status,$progress)
    {
        $now = \DateTime::createFromFormat('Y-m-d',date('Y-m-d'));
        $deadline = \DateTime::createFromFormat('Y-m-d',$deadline);

        if($status == 2){
            foreach ($progress as $progres) {
                if($progres['Tipo Ação'] == 'Evento fechado.'){
                    $fechadoEm = \DateTime::createFromFormat('d/m/Y H:i:s',$progres['Data']);
                    if($fechadoEm->diff($deadline)->days > 0 && $fechadoEm->diff($deadline)->invert == 1){
                        return 'evento-fechado severo';
                    }
                }
            }
            return 'evento-fechado';
        }


        $differ = $now->diff($deadline);

        if($status == 1 && $differ->days > 0 && $differ->invert == 1) return 'severo';

        $calc = $urgency*$relevance*$severity;
        if($calc < 7) return 'baixo';
        if($calc > 6 && $calc < 17) return 'baixo';//atencao
        if($calc > 16 && $calc < 31) return 'baixo';//elevado
        if($calc > 30 && $calc < 51) return 'alto';
        if($calc > 50 && $calc < 126) return 'severo';
        return '';
    }

    public function getProgressOfIt($code)
    {
        $progress = new Event();
        $progress->setAnonimo();
        $progress->setCode($code);


        $progressList = $progress->getProgressHistory();

        $progressListTratato = [];
        foreach ($progressList as $itemProgress) {
            $progressListTratatoAux = [];
            if($itemProgress->Comment){
                foreach ($itemProgress as $key=>$value) {
                    if(!isset($this->progressKeyTranslation[$key]) || $value == '') continue;
                    if($key == 'Date'){
                        $progressListTratatoAux[$this->progressKeyTranslation[$key]] = $progress->epocToDate($value,'d/m/Y H:i:s');
                        continue;
                    }
                    if($key == 'UpdateType'){
                        $progressListTratatoAux[$this->progressKeyTranslation[$key]] = $this->progressUpdateType[$value];
                        continue;
                    }
                    if($key == 'Action'){
                        $progressListTratatoAux[$this->progressKeyTranslation[$key]] = $this->progressActionType[$value];
                        continue;
                    }
                    $progressListTratatoAux[$this->progressKeyTranslation[$key]] = $value;
                }
                $progressListTratato[] = $progressListTratatoAux;
            }
        }

        return $progressListTratato;
    }


    public function saveEventProgressAction()
    {
        try{
            $request = $this->getRequest();
            $post = $request->getPost()->toArray();

            if($request->isPost()) {
                if(!isset($post['code']) || !isset($post['newProgress'])) throw new \Exception('Houve um erro ao executar, tente novamente mais tarde.');
                $event = new Event();
                $event->setCode($post['code']);
                $event->setComment($post['newProgress']);
                $event->save();
            }

            return new JsonModel(['error'=>false,'message'=>'Progresso salvo com sucesso!','dados'=>[]]);
        }catch (\Exception $e){
            return new JsonModel(['error'=>true,'message'=>'Houve um erro ao executar, tente novamente mais tarde.','dados'=>$e->getMessage()]);
        }
    }

    public function showlog($msg)
    {
        echo date('Y-d-m H:i:s').' => '.$msg.PHP_EOL;
    }

    public function updateDataAction(){
        try{
            $post = $this->getRequest()->getPost();
            $event = new Event();
            $event->setAnonimo();

            $tratado = [];
            foreach($post as $key => $item){
                $tratado[$key] = $event->dateToEpoc($item);
            }

            $event->exchangeArray($tratado);
            $event->setCode($this->params('id'));
            $event->save();

            $data = json_decode(file_get_contents('./data/cache/calendar.json'), true);

            $start = \DateTime::createFromFormat('d/m/Y', $post['StartDate']);
            $end = \DateTime::createFromFormat('d/m/Y', $post['EndDate']);

            $tratado = [];
            foreach($data['eventos'] as $item){
                if($item['code'] == $this->params('id')){
                    $item['start'] = $start->format('Y-m-d');
                    $item['end'] = $end->format('Y-m-d');
                }

                $tratado[] = $item;
            }

            $data['eventos'] = $tratado;
            file_put_contents('./data/cache/calendar.json',json_encode($data));

            echo json_encode(['error'=>false]);
        }catch (\Exception $e){
            echo json_encode(['error'=>true,'message'=>$e->getMessage()]);
        }
        die;
    }

}