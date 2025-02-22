<?php

require_once("Page.php");

class NavigationBarItem implements JsonSerializable
{
    private $id;
    private Page $page;
    private array $children;
    private int $order;

    public function __construct($id, Page $page, array $children, $order)
    {
        $this->id = $id;
        $this->page = $page;
        $this->children = $children;
        $this->order = $order;
    }

    public function getId()
    {
        return $this->id;
    }

 
    public function getPage(): Page
    {
        return $this->page;
    }

  
    public function setPage(Page $value)
    {
        $this->page = $value;
    }


    public function getChildren(): array
    {
        return $this->children;
    }

    public function setChildren(array $value)
    {
        $this->children = $value;
    }


    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder(int $value)
    {
        $this->order = $value;
    }

    public function jsonSerialize(): mixed
    {
        return [
            "id" => $this->getId(),
            "page" => $this->getPage(),
            "children" => $this->getChildren(),
            "order" => $this->getOrder()
        ];
    }
}
