<?php
require_once(__DIR__ . "/../repositories/FestivalHistoryRepository.php");

class FestivalHistoryService
{
    private $festivalHistoryRep;

    public function __construct()
    {
        $this->festivalHistoryRep = new FestivalHistoryRepository();
    }

    public function getAllGuides()
    {
        return $this->festivalHistoryRep->getAllGuides();
    }

    public function getAllHistoryEvents()
    {
        return $this->festivalHistoryRep->getAllHistoryEvents();
    }

    public function getById($id){
        return $this->festivalHistoryRep->getById($id);
    }

    public function getGuideById($id)
    {
        return $this->festivalHistoryRep->getGuideById($id);
    }

}