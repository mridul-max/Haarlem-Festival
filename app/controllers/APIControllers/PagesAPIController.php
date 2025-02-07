<?php
require_once("APIController.php");
require_once(__DIR__ . '/../../services/PageService.php');

/**
 * @author Konrad
 */
class PagesAPIController extends APIController
{
    private $pageService;

    public function __construct()
    {
        $this->pageService = new PageService();
    }


    public function handleGetRequest($uri)
    {
        try {
            $output = $this->pageService->getAll();
            echo json_encode($output);
        } catch (Exception $e) {
            Logger::write($e);
            $this->sendErrorMessage("Unable to retrive pages.", 500);
        }
    }
}
