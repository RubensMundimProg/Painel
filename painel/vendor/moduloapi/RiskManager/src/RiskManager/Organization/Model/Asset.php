<?php

namespace RiskManager\Organization\Model;

/**
 *
 * Classe Model que gerencia os campos dos ativos
 *
 * @author Bruno Silva <bruno.silva@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage Organization\Model
 */
class Asset {

    public $campos = [
        'AssetType' => 'assetType',
        'Name' => 'name',
        'Path' => 'path',
        'Responsible'=>'responsible',
        'Relevance'=>'relevance',
        'Criticality'=>'criticality',
        'AnalysisFrequency'=>'analysisFrequency',
        'Description'=>'description',
        'Latitude'=>'latitude',
        'Longitude'=>'longitude',
        'GeolocationDescription'=>'geolocationDescription',
        'ZoomLevel'=>'zoomLevel',
        'CustomAttributes'=>'customAttributes',
        'HostAddress'=>'hostAddress',
        'Credentials'=>'credentials',
        'CollectorServer'=>'collectorServer',
        'NetbiosName'=>'netbiosName',
        'IPAddress'=>'ipAddress',
        'DNSName'=>'dnsName',
        'ParentPerimeter'=>'ParentPerimeter'
    ];

}
