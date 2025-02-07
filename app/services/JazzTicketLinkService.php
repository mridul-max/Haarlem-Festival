<?php

require_once("TicketLinkService.php");
require_once(__DIR__ . "/../repositories/JazzTicketLinkRepository.php");
require_once(__DIR__ . "/../repositories/ImageRepository.php");

class JazzTicketLinkService extends TicketLinkService
{
    private $imageRepository;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new JazzTicketLinkRepository();
        $this->imageRepository = new ImageRepository();
    }

    public function getAll($sort = null, $filters = []): array
    {
        $ticketLinks = $this->repo->getAll($sort, $filters);
        foreach ($ticketLinks as $ticketLink) {
            $ticketLink
                ->getEvent()
                ->getArtist()
                ->setImages(
                    $this
                        ->imageRepository
                        ->getImagesForArtistId(
                            $ticketLink
                                ->getEvent()
                                ->getArtist()
                                ->getId()
                        )
                );
        }

        return $ticketLinks;
    }

    public function getById(int $id): ?TicketLink
    {
        $ticketLink = $this->repo->getById($id);
        $ticketLink
            ->getEvent()
            ->getArtist()
            ->setImages(
                $this
                    ->imageRepository
                    ->getImagesForArtistId(
                        $ticketLink
                            ->getEvent()
                            ->getArtist()
                            ->getId()
                    )
            );
        return $ticketLink;
    }

    public function getByEventId(int $id): ?TicketLink
    {
        $item = $this->repo->getByEventId($id);
        if ($item == null) {
            throw new ObjectNotFoundException("Ticket Link not found");
        }
        $item->getEvent()->getArtist()->setImages($this->imageRepository->getImagesForArtistId($item->getEvent()->getArtist()->getId()));
        return $item;
    }
}
