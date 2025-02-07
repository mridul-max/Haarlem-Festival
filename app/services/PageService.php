<?php
require_once("../repositories/PageRepository.php");
require_once("../repositories/ImageRepository.php");
require_once("../models/Exceptions/PageNotFoundException.php");
require_once("../models/Exceptions/FileDoesNotExistException.php");
require_once("ImageService.php");

class PageService
{
    private $repo;
    private $imageService;

    public function __construct()
    {
        $this->repo = new PageRepository();
        $this->imageService = new ImageService();
    }

    public function getAll(): array
    {
        $pages = $this->repo->getAll();
        foreach ($pages as $page) {
            $page->setImages($this->imageService->getImagesForPageId($page->getId()));
        }

        return $pages;
    }

    /**
     * Returns the page by its href.
     * @param mixed $href Href to the page.
     * @return Page A page with matching href.
     * @throws PageNotFoundException If matching page is not found, throws an exception.
     * @throws FileDoesNotExistException If the file does not exist, throws an exception.
     */
    public function getPageByHref($href): Page
    {
        $href = htmlspecialchars($href);
        // Check if last char is '/'.
        if (strlen($href) > 1 && substr($href, -1) == '/') {
            // If so, remove it from the string.
            // Strings should be saved without the '/' at the end, as it screws with GET requests.
            $href = rtrim($href, "/");
        }

        $page = $this->repo->countTextPages($href) > 0
            ? $this->repo->getTextPageByHref($href) : $this->repo->getPageByHref($href);

        // Page not found? Throw an exception.
        if ($page == null) {
            throw new PageNotFoundException("Page with href '$href' was not found.");
        }

        if (!($page instanceof TextPage)) {
            // Check if file exists
            $location = "../" .  $page->getLocation();
            if (!file_exists($location)) {
                throw new FileDoesNotExistException("File at '$location' was not found.");
            }
        }

        // Load images used on that page.
        $page->setImages($this->imageService->getImagesForPageId($page->getId()));

        return $page;
    }

    /**
     * Returns a page by its ID.
     * @param int $id ID of requested page.
     * @return Page A page with matching ID.
     * @throws PageNotFoundException If matching page was not found, throws the PageNotFoundException.
     * @throws FileDoesNotExistException If the file does not exist, throws an exception.
     */
    public function getPageById(int $id): Page
    {
        $id = htmlspecialchars($id);
        $page = $this->repo->getPageById($id);

        if ($page == null) {
            throw new PageNotFoundException("Page with ID '$id' was not found.");
        }


        // Check if file exists
        if (!$this->isInTextPage($id)) {
            // Check if file exists
            $href =  $page->getHref();
            $location = "../" .  $page->getLocation();
            if (!file_exists($location)) {
                throw new FileDoesNotExistException("File for href '$href' at '$location' was not found.");
            }
        }

        $page->setImages($this->imageService->getImagesForPageId($page->getId()));
        return $page;
    }

    /**
     * Returns all text pages.
     * @return array All text pages in the database.
     */
    public function getAllTextPages(): array
    {
        $pages = $this->repo->getAllTextPages();
        foreach ($pages as $page) {
            $page->setImages($this->imageService->getImagesForPageId($page->getId()));
        }
        return $pages;
    }

    /**
     * Updates the page with given ID.
     * @param int $id ID of the page to update.
     * @param string $title New title of the page.
     * @param string $content New content of the page.
     * @param array $images New images of the page.
     * @param string $href New href of the page.
     * @throws PageNotFoundException If page with given ID was not found, throws an exception.
     */
    public function updateTextPage($id, $title, $content, $images, $href)
    {
        $id = htmlspecialchars($id);
        $content = htmlspecialchars($content);
        $title = htmlspecialchars($title);
        $href = htmlspecialchars($href);

        // Check if href starts with '/', unless it's a link to another website.
        if ($href[0] != '/' && !str_starts_with($href, 'http')) {
            $href = '/' . $href;
        }

        // Check if it even exists in table.
        if ($this->repo->countTextPagesById($id) == 0) {
            throw new PageNotFoundException("Page with ID '$id' was not found.");
        }

        $this->imageService->setImagesForPage($id, $images);

        $this->repo->updateTextPage($id, $title, $content, $href);
    }

    /**
     * Gets page by its ID in database.
     * @param int $id ID of the page.
     * @return TextPage A text page with matching ID.
     * @throws PageNotFoundException If matching page was not found, throws the PageNotFoundException.
     */
    public function getTextPageById($id): TextPage
    {
        $id = htmlspecialchars($id);
        $page = $this->repo->getTextPageById($id);

        if ($page == null) {
            throw new PageNotFoundException("Page with ID '$id' was not found.");
        }

        $page->setImages($this->imageService->getImagesForPageId($page->getId()));

        return $page;
    }

    /**
     * @param string $title Title of the page.
     * @param string $content Content of the page.
     * @param array $images Images of the page.
     * @param string $href Href of the page.
     * @return TextPage The newly created text page.
     */
    public function createTextPage($title, $content, $images, $href): TextPage
    {
        $content = htmlspecialchars($content);
        $title = htmlspecialchars($title);
        $href = htmlspecialchars($href);

        // Check if href starts with '/', unless it's a link to another website.
        if ($href[0] != '/' && !str_starts_with($href, 'http')) {
            $href = '/' . $href;
        }

        // Check if page with this href already exists.
        if ($this->repo->countTextPages($href) > 0) {
            throw new PageAlreadyExistsException("Page with href '$href' already exists.");
        }

        $pageId = $this->repo->createTextPage($title, $content, $href);
        $this->imageService->setImagesForPage($pageId, $images);

        return $this->getTextPageById($pageId);
    }

    /**
     * Deletes a text page with given ID.
     * @param int $id ID of the page to delete.
     */
    public function delete($id)
    {
        $id = htmlspecialchars($id);
        $this->repo->delete($id);
    }

    /**
     * Checks if page with given ID is a text page.
     * @param int $id ID of the page.
     * @return bool True if page is a text page, false otherwise.
     */
    public function isInTextPage($id): bool
    {
        $id = htmlspecialchars($id);
        return $this->repo->countTextPagesById($id) > 0;
    }
}
