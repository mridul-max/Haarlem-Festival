<?php

/**
 * @author Konrad
 */
class Page implements JsonSerializable
{
    protected $id;
    protected $title;
    protected $href;
    protected $location;
    protected $images;

    public function __construct($id, $title, $href, $location, $images = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->href = $href;
        $this->location = $location;
        $this->images = $images;
    }

    /**
     * Returns the ID in database.
     * @return mixed Page's ID.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the page's title, which is displayed in the page's tab bar.
     * @return mixed Page's title.
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the page's title (displayed in the page's tabs bar).
     * @param mixed $value New page's title.
     */
    public function setTitle($value)
    {
        $this->title = $value;
    }

    /**
     * Returns the href of the page.
     * @return mixed Page's href.
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * Sets the href used to access the page.
     * @param mixed $value Href to be set.
     * @throws InvalidArgumentException Thrown if the $value does NOT start with "/", or if the $value is empty.
     */
    public function setHref($value)
    {
        // Page URLs should always start with the '/'.
        if (!str_starts_with($value, "/")) {
            throw new InvalidArgumentException("Location must start with the '/' character.");
        }

        // Href ends with '/'? Trim it out.
        if (str_ends_with($value, "/")) {
            $value = rtrim($value, "/");
        }

        // The empty href is reserved for the statically located Index only.
        if (empty($value) || $value == "") {
            throw new InvalidArgumentException("Cannot assign empty href, as it's reserved for Index.");
        }

        $this->href = $value;
    }

    /**
     * @return mixed Returns the website file location (usulaly located in the "/views" folder).
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Allows setting the file location of the page.
     * @param mixed $value Location to the file (starting with '/').
     * @return mixed A location of the page file.
     * @throws InvalidArgumentException Thrown when the $value does not start with '/', or does not end with '.php'.
     */
    public function setLocation($value)
    {
        if (!str_starts_with($value, "/")) {
            throw new InvalidArgumentException("Location must start with the '/' character.");
        }

        if (!str_ends_with($value, ".php")) {
            throw new InvalidArgumentException("Location must end with the '.php' suffix.");
        }

        $this->location = $value;
    }

    public function getImages()
    {
        return $this->images;
    }

    public function setImages($images)
    {
        $this->images = $images;
    }

    public function jsonSerialize(): mixed
    {
        return [
            "id" => $this->getId(),
            "title" => $this->getTitle(),
            "href" => $this->getHref(),
            "images" => $this->getImages()
        ];
    }
}
