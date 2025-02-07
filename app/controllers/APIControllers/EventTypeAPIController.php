<?php

require_once('APIController.php');
require_once(__DIR__ . '/../../services/EventTypeService.php');

/**
 * @author Konrad
 */
class EventTypeAPIController extends APIController
{
    private $eventTypeService;

    public function __construct()
    {
        $this->eventTypeService = new EventTypeService();
    }

    public function handleGetRequest($uri)
    {
        try {
            $id = basename($uri);
            if (is_numeric($id)) {
                $eventType = $this->eventTypeService->getById($id);
                if ($eventType) {
                    echo json_encode($eventType);
                } else {
                    $this->sendErrorMessage("Not found", 404);
                }
            } else {
                $eventTypes = $this->eventTypeService->getAll();
                echo json_encode($eventTypes);
            }
        } catch (Exception $e) {
            Logger::write($e);
            $this->sendErrorMessage("Unable to retrive event types.", 500);
        }
    }
}
