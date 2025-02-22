<?php
require_once(__DIR__ . "/APIController.php");
require_once("../services/NavigationBarItemService.php");

class NavBarAPIController extends APIController
{
    private $navService;

    public function __construct()
    {
        $this->navService = new NavigationBarItemService();
    }

    public function handleGetRequest($uri)
    {
        try {
            $output = $this->navService->getAll();
            echo json_encode($output);
        } catch (Exception $e) {
            Logger::write($e);
            $this->sendErrorMessage("Unable to retrive pages.");
        }
    }
}
