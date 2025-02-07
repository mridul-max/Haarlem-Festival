<?php
require_once("Page.php");

class EventPage extends Page implements JsonSerializable
{
    protected Event $event;

    public function __construct($id, $title, $href, Event $event)
    {
        $this->id = $id;
        $this->title = $title;
        $this->href = $href;
        $this->event = $event;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function setEvent(Event $value)
    {
        $this->event = $value;
    }

    public function jsonSerialize(): mixed
    {
        return [
            "id" => $this->getId(),
            "title" => $this->getTitle(),
            "href" => $this->getHref(),
            "event" => $this->getEvent()
        ];
    }
}
