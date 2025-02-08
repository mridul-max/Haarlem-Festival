<?php

require_once("../models/TextPage.php");

class TextPageController
{
    const TEXT_PAGE_PATH = "/../views/textpage.php";

    public function loadPage($page)
    {
        try {
            $title = $page->getTitle();
            $content = $page->getContent();
            $images = $page->getImages();
            require(__DIR__ . self::TEXT_PAGE_PATH);
        } catch (Throwable $t) {
            require(__DIR__ . "../views/404.php");
        }
    }
}
