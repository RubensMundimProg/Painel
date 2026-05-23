<?php

namespace RiskManager\Workflow\Entity;

use Base\Service\AbstractApiService;
/**
 *
 * Classe Entity que armazena informações dos eventos
 *
 * @author Bruno Silva <bruno.silva@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage Entity\Entity
 */
class Event extends AbstractApiService {
    protected $id;
    protected $title;
    protected $status;
    protected $description;
    protected $progress;
    protected $urgency;
    protected $relevance;
    protected $severity;
    protected $latitude;
    protected $longitude;
    protected $geolocationDescription;
    protected $expectedStartDate;
    protected $expectedEndDate;
    protected $startDate;
    protected $endDate;
    protected $deadline;
    protected $value;
    protected $notify;
    protected $parentEvent;
    protected $coordinator;
    protected $responsible;
    protected $involved;
    protected $firstReviewer;
    protected $secondReviewer;
    protected $thirdReviewer;
    protected $data;
    protected $fileName;
    protected $comment;
    protected $eventType;
    protected $code;
    protected $created;
    protected $updatedOn;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $id = str_replace('"','',$id);
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $code = str_replace('"','',$code);
        $this->code = $code;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }



    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getCoordinator()
    {
        return $this->coordinator;
    }

    /**
     * @param mixed $coordinator
     */
    public function setCoordinator($coordinator)
    {
        $this->coordinator = $coordinator;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * @param mixed $deadline
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return mixed
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * @param mixed $eventType
     */
    public function setEventType($eventType)
    {
        $this->eventType = $eventType;
    }

    /**
     * @return mixed
     */
    public function getExpectedEndDate()
    {
        return $this->expectedEndDate;
    }

    /**
     * @param mixed $expectedEndDate
     */
    public function setExpectedEndDate($expectedEndDate)
    {
        $this->expectedEndDate = $expectedEndDate;
    }

    /**
     * @return mixed
     */
    public function getExpectedStartDate()
    {
        return $this->expectedStartDate;
    }

    /**
     * @param mixed $expectedStartDate
     */
    public function setExpectedStartDate($expectedStartDate)
    {
        $this->expectedStartDate = $expectedStartDate;
    }

    /**
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param mixed $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return mixed
     */
    public function getFirstReviewer()
    {
        return $this->firstReviewer;
    }

    /**
     * @param mixed $firstReviewer
     */
    public function setFirstReviewer($firstReviewer)
    {
        $this->firstReviewer = $firstReviewer;
    }

    /**
     * @return mixed
     */
    public function getGeolocationDescription()
    {
        return $this->geolocationDescription;
    }

    /**
     * @param mixed $geolocationDescription
     */
    public function setGeolocationDescription($geolocationDescription)
    {
        $this->geolocationDescription = $geolocationDescription;
    }

    /**
     * @return mixed
     */
    public function getInvolved()
    {
        return $this->involved;
    }

    /**
     * @param mixed $involved
     */
    public function setInvolved($involved)
    {
        $this->involved = $involved;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return mixed
     */
    public function getNotify()
    {
        return $this->notify;
    }

    /**
     * @param mixed $notify
     */
    public function setNotify($notify)
    {
        $this->notify = $notify;
    }

    /**
     * @return mixed
     */
    public function getParentEvent()
    {
        return $this->parentEvent;
    }

    /**
     * @param mixed $parentEvent
     */
    public function setParentEvent($parentEvent)
    {
        $this->parentEvent = $parentEvent;
    }

    /**
     * @return mixed
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * @param mixed $progress
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;
    }

    /**
     * @return mixed
     */
    public function getRelevance()
    {
        return $this->relevance;
    }

    /**
     * @param mixed $relevance
     */
    public function setRelevance($relevance)
    {
        $this->relevance = $relevance;
    }

    /**
     * @return mixed
     */
    public function getResponsible()
    {
        return $this->responsible;
    }

    /**
     * @param mixed $responsible
     */
    public function setResponsible($responsible)
    {
        $this->responsible = $responsible;
    }

    /**
     * @return mixed
     */
    public function getSecondReviewer()
    {
        return $this->secondReviewer;
    }

    /**
     * @param mixed $secondReviewer
     */
    public function setSecondReviewer($secondReviewer)
    {
        $this->secondReviewer = $secondReviewer;
    }

    /**
     * @return mixed
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * @param mixed $severity
     */
    public function setSeverity($severity)
    {
        $this->severity = $severity;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getThirdReviewer()
    {
        return $this->thirdReviewer;
    }

    /**
     * @param mixed $thirdReviewer
     */
    public function setThirdReviewer($thirdReviewer)
    {
        $this->thirdReviewer = $thirdReviewer;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getUrgency()
    {
        return $this->urgency;
    }

    /**
     * @param mixed $urgency
     */
    public function setUrgency($urgency)
    {
        $this->urgency = $urgency;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    public function setCreated($created){
        $this->created = $created;
    }

    public function getCreated(){
        return $this->created;
    }

    public function setUpdatedOn($update){
        $this->updatedOn = $update;
    }

    public function getUpdatedOn(){
        return $this->updatedOn;
    }

}
