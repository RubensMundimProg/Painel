<?php

namespace Application\Service;
use Classes\Service\Alertas;

/**
 * Class Categoria
 * @package Application\Service
 */
class Categoria
{
    protected $dados;
    public function __construct()
    {
        $alerta = new Alertas();
        $lista = $alerta->categoriaSubCategoria();
        $dados = [];
        foreach($lista['subcategorias'] as $chave => $valor){

            if(is_array($valor)){
                foreach($valor as $sub){
                    $dados[] = $chave.' - '.$sub;
                }
            }else{
                $dados[] = $chave;
            }
        }

        $this->dados = $dados;

/*
        $this->dados = [];
        $this->dados[] = '';
        $this->dados[1] = 'Abastecimento de Água';
        $this->dados[2] = 'Aplicação - Abertura e fechamento dos portões';
        $this->dados[3] = 'Aplicação - Atendimento especializado';
        $this->dados[4] = 'Aplicação - Atendimento específico';
        $this->dados[5] = 'Aplicação - Boletim de ocorrência';
        $this->dados[6] = 'Aplicação - Identificação especial';
        $this->dados[7] = 'Aplicação - Infraestrutura';
        $this->dados[8] = 'Aplicação - Nome Social (travestis e transexuais)';
        $this->dados[9] = 'Aplicação - Participação condicional';
        $this->dados[10] = 'Aplicação - Porte de arma';
        $this->dados[11] = 'Aplicação - Quantidade de provas';
        $this->dados[12] = 'Aplicação - Tentativa de roubo de caderno de prova';
        $this->dados[13] = 'Aplicação - Tumulto em local de prova';
        $this->dados[14] = 'Aplicação - Demanda Judicial';
        $this->dados[15] = 'Clima e Tempo - Inundação no local de prova';
        $this->dados[16] = 'Clima e Tempo - Tempestade torrencial';
        $this->dados[17] = 'Emergências Médicas - Acidentes no local de prova';
        $this->dados[18] = 'Emergências Médicas - Doença Infectocontagiosa';
        $this->dados[19] = 'Emergências Médicas - Morte (participante ou equipe da aplicadora)';
        $this->dados[20] = 'Emergências Médicas - Nascimentos';
        $this->dados[21] = 'Energia Elétrica';
        $this->dados[22] = 'Incêndio';
        $this->dados[23] = 'Malote - Agente dos correios sem lacre reserva';
        $this->dados[24] = 'Malote - Atraso na coleta de malotes';
        $this->dados[25] = 'Malote - Atraso na entrega de malotes';
        $this->dados[26] = 'Malote - Imprevistos em carro da ECT';
        $this->dados[27] = 'Malote - Abertura indevida';
        $this->dados[28] = 'Malote - Danificado';
        $this->dados[29] = 'Malote - Integridade do cadeado eletrônico';
        $this->dados[30] = 'Malote - Integridade do lacre metálico';
        $this->dados[31] = 'Malote - Não recebido';
        $this->dados[32] = 'Malote - Perda ou extravio';
        $this->dados[33] = 'Malote - Operação reversa';
        $this->dados[34] = 'Malote - Vunerabilidade';
        $this->dados[35] = 'Eliminação de Participantes - Comportamento inadequado (participante ou acompanhante de lactante)';
        $this->dados[36] = 'Eliminação de Participantes - Comunicação indevida (participante X equipe de aplicação)';
        $this->dados[37] = 'Eliminação de Participantes - Consulta de material';
        $this->dados[38] = 'Eliminação de Participantes - Documentação indevida';
        $this->dados[39] = 'Eliminação de Participantes - Portar armas de qualquer espécie, ainda que detenha autorização para o respectivo porte';
        $this->dados[40] = 'Eliminação de Participantes - Porte de equipamento eletrônico e/ou de comunicação';
        $this->dados[41] = 'Eliminação de Participantes - Porte de material proibido em edital';
        $this->dados[42] = 'Eliminação de Participantes - Procedimento indevido relacionado ao Caderno de Questões';
        $this->dados[43] = 'Eliminação de Participantes - Procedimento indevido relacionado ao Cartão-Resposta';
        $this->dados[44] = 'Eliminação de Participantes - Recusa à coleta de dado biométrico';
        $this->dados[45] = 'Eliminação de Participantes - Recusa à revista eletrônica (detector de metais)';
        $this->dados[46] = 'Eliminação de Participantes - Saída antes do horário permitido';
        $this->dados[47] = 'Eliminação de Participantes - Tentativa ou utilização de meio fraudulento';
        $this->dados[48] = 'Segurança Pública - Ameaça bomba / localização artefato explosivo';
        $this->dados[49] = 'Segurança Pública - Atentados contra locais de Exame (invasão / incêndio / depredação)';
        $this->dados[50] = 'Segurança Pública - CVLI (Crimes Violentos Letais Intencionais)';
        $this->dados[51] = 'Segurança Pública - CVP (Crimes violentos contra o patrimônio)';
        $this->dados[52] = 'Segurança Pública - Estabelecimento prisional (fuga / rebelião / salve)';
        $this->dados[53] = 'Segurança Pública - Greve (segurança pública / transporte público)';
        $this->dados[54] = 'Segurança Pública - Instituição financeira (explosão / maçarico / arrombamento / novo cangaço)';
        $this->dados[55] = 'Segurança Pública - Interdição via (Rodovia federal / Estadual)';
        $this->dados[56] = 'Segurança Pública - Manifestações (Previsão / Monitoramento)';
        $this->dados[57] = 'Segurança Pública - Outras ocorrências relevantes';
        $this->dados[58] = 'Segurança Pública - Perturbação sossego público';
        $this->dados[59] = 'Segurança Pública - Transporte público (queima/depredação)';
        $this->dados[60] = 'Inscrição';*/
    }

    public function getIndice($categoria, $subcategoria=false){
        $string = $categoria;
        if($subcategoria) $string .= ' - '.$subcategoria;
        $inverso = [];
        foreach($this->dados as $indice => $valor){
            $inverso[$valor] = $indice;
        }
        if(!isset($inverso[$string])){
            throw  new \Exception('Categoria e Subcateria não encontrada');
        }

        return $inverso[$string];
    }

    public function getCategoria($codigo)
    {
        if(!isset($this->dados[$codigo])){
            throw  new \Exception('Código de categoria não localizado');
        }

        $dados = explode(' - ', $this->dados[$codigo]);
        $return = [];
        $return['categoria'] = $dados[0];
        $return['subcategoria'] = (isset($dados[1])) ? $dados[1] : '';

        return $return;
    }

    public function getChaves()
    {
        $return = [];
        foreach($this->dados as $key => $item){
            if(preg_match('/__/',$item)) continue;
            if(!$item) continue;
            $return[] = ['codigo'=>$key,'valor'=>($item)];
        }
        return $return;
    }

    public function getTable(){
        $table = '<table><tr><td>Código</td><td>Categoria</td></tr>';
        foreach($this->dados as $key => $item){
            if(!$item) continue;
            $table.='<tr><td>'.$key.'</td><td>'.$item.'</td></tr>';
        }
        $table.'</table>';
        echo $table;
        die;
    }
}