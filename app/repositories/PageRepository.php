<?php

require_once("Repository.php");
require_once("../models/Page.php");
require_once("../models/TextPage.php");

/**
 * @author Konrad
 */
class PageRepository extends Repository
{
    /**
     * Handles building of the page objects.
     * @param mixed $arr An array from the 'stmt->fetchAll()'.
     * @return array An array of the pages
     * (note: it can be empty, if criteria set by the previous matching was not met).
     */
    private function pageBuilder($arr): array
    {
        $output = array();
        foreach ($arr as $row) {
            $id = $row["id"];
            $title = htmlspecialchars_decode($row["title"]);
            $href = $row["href"];
            $location = $row["location"];
            $page = new Page($id, $title, $href, $location, array());

            array_push($output, $page);
        }
        return $output;
    }

    private function textPageBuilder($arr): array
    {
        $output = array();
        foreach ($arr as $row) {
            $id = $row["textPageId"];
            $title = htmlspecialchars_decode($row["title"]);
            $href = $row["href"];
            $text = htmlspecialchars_decode($row["content"]);
            $page = new TextPage($id, $title, $href, $text, array());

            array_push($output, $page);
        }
        return $output;
    }

    /**
     * Returns matching page, or null if nothing was found.
     * @param mixed $id Page's ID.
     * @return ?Page A requested page (or null).
     */
    public function getPageById($id): ?Page
    {
        $sql = "SELECT id, title, href, location FROM pages WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $pageArray = $this->pageBuilder($stmt->fetchAll());
        return empty($pageArray) ? null : $pageArray[0];
    }

    /**
     * Returns all pages in the database.
     * @return array Array of pages.
     */
    public function getAll(): array
    {
        $sql = "SELECT id, title, href, location FROM pages";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $this->pageBuilder($stmt->fetchAll());
    }

    /**
     * Returns a page with matching href, or null if one was not found.
     * @param mixed $href Href of the webpage.
     * @return ?Page A requested page (or null).
     */
    public function getPageByHref($href): ?Page
    {
        $sql = "SELECT id, title, href, location FROM pages WHERE href = :href";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(":href", $href, PDO::PARAM_STR);
        $stmt->execute();
        $pageArray = $this->pageBuilder($stmt->fetchAll());
        return empty($pageArray) ? null : $pageArray[0];
    }

    public function getTextPageByHref($href): ?TextPage
    {
        $sql = "SELECT tp.textPageId, tp.content, p.title, p.href, p.location "
            . "FROM textpages tp JOIN pages p ON p.id = tp.textPageId "
            . "WHERE p.href = :href";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(":href", $href, PDO::PARAM_STR);
        $stmt->execute();
        $pageArray = $this->textPageBuilder($stmt->fetchAll());
        return empty($pageArray) ? null : $pageArray[0];
    }

    public function getTextPageById($id): ?TextPage
    {
        $sql = "SELECT tp.textPageId, tp.content, p.title, p.href, p.location "
            . "FROM textpages tp JOIN pages p ON p.id = tp.textPageId "
            . "WHERE tp.textPageId = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $pageArray = $this->textPageBuilder($stmt->fetchAll());
        return empty($pageArray) ? null : $pageArray[0];
    }

    public function countTextPages(string $href): int
    {
        $sql = "SELECT p.id, p.href FROM pages p JOIN textpages tp ON p.id = tp.textPageId WHERE p.href = :href";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(":href", $href, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function countTextPagesById(int $id): int
    {
        $sql = "SELECT p.id, p.href FROM pages p JOIN textpages tp ON p.id = tp.textPageId WHERE p.id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function getAllTextPages(): array
    {
        $sql = "SELECT tp.textPageId, tp.content, p.title, p.href, p.location "
            . "FROM textpages tp JOIN pages p ON p.id = tp.textPageId";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $this->textPageBuilder($stmt->fetchAll());
    }

    public function updateTextPage($id, $title, $content, $href)
    {
        $sql = "UPDATE textpages SET content = :content WHERE textPageId = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(":content", $content, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $sql = "UPDATE pages SET title = :title, href = :href WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(":title", $title, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":href", $href, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function createTextPage($title, $content, $href): int
    {
        $sql = "INSERT INTO pages (title, href, location) VALUES (:title, :href, :location)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(":title", $title, PDO::PARAM_STR);
        $stmt->bindParam(":href", $href, PDO::PARAM_STR);
        $stmt->bindValue(":location", "", PDO::PARAM_STR);
        $stmt->execute();

        $lastId = $this->connection->lastInsertId();

        $sql = "INSERT INTO textpages (textPageId, content) VALUES (:id, :content)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(":content", $content, PDO::PARAM_STR);
        $stmt->bindParam(":id", $lastId, PDO::PARAM_INT);
        $stmt->execute();

        return $lastId;
    }

    public function delete($id)
    {
        $sql = "DELETE FROM pages WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
