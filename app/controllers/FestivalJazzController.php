<?php

require_once(__DIR__ . "/../services/JazzTicketLinkService.php");

class FestivalJazzController
{
    const JAZZ_ARTIST_PAGE = "/../views/festival/jazzartist.php";
    const JAZZ_EVENT_PAGE = "/../views/festival/jazzevent.php";

    private $ciService;

    public function __construct()
    {
        $this->ciService = new JazzTicketLinkService();
    }

    public function loadArtistPage($uri)
    {
        require_once(__DIR__ . "/../services/ArtistService.php");

        try {
            $artistService = new ArtistService();
            $artist = $artistService->getById(basename($uri));

            if ($artist === null) {
                // redirect to 404
                header("Location: /404");
                return;
            }

            $events = $this->ciService->getAll("time", ["artist" => $artist->getId()]);

            require(__DIR__ . self::JAZZ_ARTIST_PAGE);
        } catch (Exception $e) {
            header("Location: /404");
            return;
        }
    }

    public function loadEventPage($uri)
    {
        try {
            $cartItem = $this->ciService->getByEventId(basename($uri));
            $event = $cartItem->getEvent();

            if (!($event instanceof JazzEvent)) {
                return;
            }

            $afterThat = $this->ciService->getAll("", [
                "day" => $event->getStartTime()->format('d'),
                "time_from" => $event->getEndTime()->format('H:i'),
                "location" => $event->getLocation()->getLocationId()
            ]);

            require(__DIR__ . self::JAZZ_EVENT_PAGE);
        } catch (Exception $e) {
            header("Location: /404");
            return;
        }
    }
}
