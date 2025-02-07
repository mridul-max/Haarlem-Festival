<?php

require_once("Page.php");

/**
 * A representation of navigation bar link.
 * @author Konrad Figura
 */
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

    /**
     * Returns this NavBarItem's ID inside of the database.
     * @return mixed NavBarItem's ID in database.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the page that is opened, when user clicks on this NavBarItem.
     * @return Page Page to be opened, when this NavBarItem is clicked.
     */
    public function getPage(): Page
    {
        return $this->page;
    }

    /**
     * Sets the page which this navbar item leads to, when clicking on it.
     * @param Page $value Page that will be opened, when this element was clicked.
     */
    public function setPage(Page $value)
    {
        $this->page = $value;
    }

    /**
     * Returns the list of all children NavigationBarItems.
     * @return array A list of all NavigationBarItems that are the children of this NavBarItem.
     * This array can be empty, meaning this particular NavigationBarItem has no children.
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Sets the children elements of the navbar item.
     * @param array $value Sets the children elements (also NavigationBarItem objects).
     */
    public function setChildren(array $value)
    {
        $this->children = $value;
    }

    /**
     * Order in which the navbar item should be displayed (pref. the order should be set every 10).
     * @return int Position where the navbar item should be displayed.
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * Sets the new order in the navbar of an item.
     * @param int $value New position (pref. the order should be set every 10th digit, like so: 10, 20, 30,40...).
     */
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
