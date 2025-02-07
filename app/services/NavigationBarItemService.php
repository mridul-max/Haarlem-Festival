<?php
require_once("../repositories/NavigationBarItemRepository.php");
require_once('PageService.php');

class NavigationBarItemService
{
    private $navBarItemRepository;
    private $pageService;

    public function __construct()
    {
        $this->navBarItemRepository = new NavigationBarItemRepository();
        $this->pageService = new PageService();
    }

    /**
     * Returns all the navigation bar items that are not children of another navigation bar item.
     * @return array The navigation bar items that are not children of another navigation bar item.
     */
    public function getAll(): array
    {
        return $this->navBarItemRepository->getAll();
    }

    /**
     * Sets the navigation bar items array from the given input.
     */
    private function mapInputToArrayOfNavbarItems($input): array
    {
        $navbarItems = array();
        $index = 0;
        foreach ($input as $i) {
            $index++;
            // Get the page from the database.
            $page = $this->pageService->getPageById(htmlspecialchars($i["page"]["id"]));

            // Now we must create an array of children of that navigation bar item.
            // There is only ONE level of children possible.
            $children = array();
            // Child's index is : parent's index + 100.
            $childIndex = (int)((string)$index . '00');
            foreach ($i["children"] as $child) {
                $childIndex++;
                $childPage = $this->pageService->getPageById(htmlspecialchars($child["page"]["id"]));
                // Set the children of the navigation bar item.
                $children[] = new NavigationBarItem(0, $childPage, array(), $childIndex);
            }
            // Set the navigation bar item.
            $navbarItems[] = new NavigationBarItem(0, $page, $children, $index);
        }

        return $navbarItems;
    }

    /**
     * Sets the navigation bar layout from the given input.
     * @param array $input The input from the user.
     */
    public function setNavbars($input): array
    {
        // First we create an array of what was inputted by the user.
        // We're doing it before we clear the database,
        // because we want to avoid implications if the user input is invalid.
        $navbarItems = $this->mapInputToArrayOfNavbarItems($input);

        // We must clear the database of all the navigation bar items.
        $this->navBarItemRepository->clear();

        // Then, we must add the new navigation bar items.
        foreach ($navbarItems as $navbar) {
            $id = $this->navBarItemRepository->insert($navbar->getPage()->getId(), $navbar->getOrder());

            // If the navigation bar item has children, we must add them as well.
            foreach ($navbar->getChildren() as $child) {
                $childId = $this->navBarItemRepository->insert($child->getPage()->getId(), $child->getOrder());
                $this->navBarItemRepository->setParent($childId, $id);
            }
        }

        // Cool, we're done! Return an array with the new navigation bar items.
        return $this->getAll();
    }
}
