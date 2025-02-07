<?php

require_once(__DIR__ . "/../Image.php");
require_once("ArtistKind.php");

/**
 * @author Konrad
 */
class Artist implements JsonSerializable
{
    private $id;
    private string $name;
    private string $description;
    private string $recentAlbums;
    private array $images;
    private string $country;
    private string $genres;
    private string $homepage;
    private string $facebook;
    private string $twitter;
    private string $instagram;
    private string $spotify;
    private ArtistKind $kind;

    public function __construct($id, $name, $description, $images, $country, $genres, $homepage, $facebook, $twitter, $instagram, $spotify, $recentAlbums, ArtistKind $kind)
    {
        $this->setId($id);
        $this->setName($name);
        $this->setDescription($description);
        $this->setImages($images);
        $this->setCountry($country);
        $this->setGenres($genres);
        $this->setHomepage($homepage);
        $this->setFacebook($facebook);
        $this->setTwitter($twitter);
        $this->setInstagram($instagram);
        $this->setSpotify($spotify);
        $this->setRecentAlbums($recentAlbums);
        $this->setArtistKind($kind);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getImages()
    {
        return $this->images;
    }

    public function setImages($value)
    {
        $this->images = $value;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($value)
    {
        $this->country = $value;
    }

    public function getGenres()
    {
        return $this->genres;
    }

    public function setGenres($value)
    {
        $this->genres = $value;
    }

    public function getHomepage()
    {
        return $this->homepage;
    }

    public function setHomepage($value)
    {
        $this->homepage = $value;
    }

    public function getFacebook()
    {
        return $this->facebook;
    }

    public function setFacebook($value)
    {
        $this->facebook = $value;
    }

    public function getTwitter()
    {
        return $this->twitter;
    }

    public function setTwitter($value)
    {
        $this->twitter = $value;
    }

    public function getInstagram()
    {
        return $this->instagram;
    }

    public function setInstagram($value)
    {
        $this->instagram = $value;
    }

    public function getSpotify()
    {
        return $this->spotify;
    }

    public function setSpotify($value)
    {
        $this->spotify = $value;
    }

    public function getRecentAlbums()
    {
        return $this->recentAlbums;
    }

    public function setRecentAlbums($value)
    {
        $this->recentAlbums = $value;
    }

    public function getArtistKind()
    {
        return $this->kind;
    }

    public function setArtistKind($value)
    {
        $this->kind = $value;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'images' => $this->getImages(),
            'country' => $this->getCountry(),
            'genres' => $this->getGenres(),
            'homepage' => $this->getHomepage(),
            'facebook' => $this->getFacebook(),
            'twitter' => $this->getTwitter(),
            'instagram' => $this->getInstagram(),
            'spotify' => $this->getSpotify(),
            'recentAlbums' => $this->getRecentAlbums(),
            'kind' => $this->getArtistKind()
        ];
    }

    public function noInformation()
    {
        return $this->getDescription() == "" && $this->getImages() == [] && $this->getCountry() == "" && $this->getGenres() == "";
    }
}
