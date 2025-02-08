<?php
require_once("Page.php");

class TextPage extends Page
{
    public $content;

    public function __construct($id, $title, $href, $content, $images = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->href = $href;
        $this->content = $content;
        $this->images = $images;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($value)
    {
        $this->content = $value;
    }

    public function jsonSerialize(): mixed
    {
        // return base
        return parent::jsonSerialize() + [
            'content' => $this->content
        ];
    }
}
