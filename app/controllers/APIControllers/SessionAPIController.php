<?php
require_once("APIController.php");
require_once __DIR__ . "/../../services/FestivalFoodService.php";
class SessionApiController{

    private $service;

    public function __construct()
    {
        $this->service = new FestivalFoodService();
    }

    public function handleDeleteRequest()
    {
        try {
            $this->service->deleteSession($_GET['id']);
            echo "Restaurant deleted successfully.";
        } catch (Exception $e) {
            Logger::write($e);
            echo "Unable to delete restaurant.", 500;
        }
    }
}
?>