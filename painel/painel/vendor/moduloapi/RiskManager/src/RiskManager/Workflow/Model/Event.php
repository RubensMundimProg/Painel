<?php

namespace RiskManager\Workflow\Model;

/**
 *
 * Classe Model que gerencia os campos dos eventos
 *
 * @author Bruno Silva <bruno.silva@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage Workflow\Model
 */
class Event {

    public $campos = [
        'Title'=>'title',
        'Description'=>'description',
        'Progress'=>'progress',
        'Urgency'=>'urgency',
        'Relevance'=>'relevance',
        'Severity'=>'severity',
        'Latitude'=>'latitude',
        'Longitude'=>'longitude',
        'GeolocationDescription'=>'geolocationDescription',
        'ExpectedStartDate'=>'expectedStartDate',
        'ExpectedEndDate'=>'expectedEndDate',
        'StartDate'=>'startDate',
        'EndDate'=>'endDate',
        'Deadline'=>'deadline',
        'Value'=>'value',
        'Notify'=>'notify',
        'ParentEvent'=>'parentEvent',
        'Coordinator'=>'coordinator',
        'Responsible'=>'responsible',
        'Involved'=>'involved',
        'FirstReviewer'=>'firstReviewer',
        'SecondReviewer'=>'secondReviewer',
        'ThirdReviewer'=>'thirdReviewer',
        'Data'=>'data',
        'FileName'=>'fileName',
        'Comment'=>'comment',
        'EventType'=>'eventType',
        'Code'=>'code',
        'Status'=>'status',
        'Created'=>'created',
        'UpdatedOn'=>'updatedOn',
    ];

}
