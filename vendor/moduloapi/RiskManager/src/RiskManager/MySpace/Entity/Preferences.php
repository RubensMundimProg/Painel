<?php

namespace RiskManager\MySpace\Entity;

use Base\Service\AbstractApiService;

/**
 * 
 * Classe Entity que armazena as informações sobre as preferências pessoais do usuário atual.

 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage MySpace\Entity
 */
class Preferences extends AbstractApiService {

    /**
     *
     * @var object ItemsPerPage: traz informações sobre a preferência do usuário quanto ao número de itens que
     * devem ser exibidos por página em todas as listas do sistema. Esse objeto possui dois campos:
     * UseSystemDefault: true ou false. Quando for "true", o campo "Value" terá o conteúdo
     * definido nas preferências do sistema. Quando for "false", o campo "Value" terá o conteúdo
     * definido nas preferências do usuário.
     * Value: valor inteiro que define o número de itens por página.
     * 
     */
    protected $itemsPerPage;

    /**
     *
     * @var object Language: traz informações sobre a preferência do usuário quanto ao idioma utilizado para acessar
     * o Módulo Risk Manager. Esse objeto possui dois campos:
     * UseSystemDefault: true ou false. Quando for "true", o campo "Value" terá o conteúdo
     * definido nas preferências do sistema. Quando for "false", o campo "Value" terá o conteúdo
     * definido nas preferências do usuário.
     * Value: string que identifica o idioma de preferência do usuário. Valores retornados: en-US,
     * es-ES e pt-BR.
     */
    protected $language;

    /**
     *
     * @var object ShowGraphs: traz informações sobre a preferência do usuário quanto à exibição, ou não, de
     * gráficos no sistema. Esse objeto possui dois campos:
     * UseSystemDefault: true ou false. Quando for "true", o campo "Value" terá o conteúdo
     * definido nas preferências do sistema. Quando for "false", o campo "Value" terá o conteúdo
     * definido nas preferências do usuário.
     * Value: valor booleano (true ou false) que define a exibição, ou não, dos gráficos do
     * sistema.
     */
    protected $showGraphs;

    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function getShowGraphs()
    {
        return $this->showGraphs;
    }

    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    public function setLanguage($language)
    {
        $this->language = $language;
    }

    public function setShowGraphs($showGraphs)
    {
        $this->showGraphs = $showGraphs;
    }

}
