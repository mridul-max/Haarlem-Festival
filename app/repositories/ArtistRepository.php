<?php

require_once("Repository.php");
require_once("../models/Music/Artist.php");
require_once("../models/Music/ArtistKind.php");
require_once("../models/Exceptions/ObjectNotFoundException.php");

/**
 * @author Konrad
 */
class ArtistRepository extends Repository
{
    public function __construct()
    {
        parent::__construct();
    }

    private function buildArtist($arr): array
    {
        $output = array();
        foreach ($arr as $row) {
            $artistId = $row["artistId"];
            $name = htmlspecialchars_decode($row["name"]);
            $description = $this->readIfSet($row, "description");
            $genres = $this->readIfSet($row, "genres");
            $country = $this->readIfSet($row, "country");

            $homepage = $this->readIfSet($row, "homepageUrl");
            $facebook = $this->readIfSet($row, "facebookUrl");
            $twitter = $this->readIfSet($row, "twitterUrl");
            $instagram = $this->readIfSet($row, "instagramUrl");
            $spotify = $this->readIfSet($row, "spotifyUrl");
            $recentAlbums = $this->readIfSet($row, "recentAlbums");

            $artistKind = $this->getArtistKindById($row["artistKindId"]);

            $artist = new Artist(
                $artistId,
                $name,
                $description,
                array(),
                $country,
                $genres,
                $homepage,
                $facebook,
                $twitter,
                $instagram,
                $spotify,
                $recentAlbums,
                $artistKind
            );

            $output[] = $artist;
        }

        return $output;
    }

    private function readIfSet($row, $colName)
    {
        if (isset($row[$colName])) {
            return htmlspecialchars_decode($row[$colName]);
        } else {
            return "";
        }
    }

    /**
     * Returns all artists.
     */
    public function getAll($sort, $filters): array
    {
        $sql = "SELECT artistId, name, description, recentAlbums, genres, country, homepageUrl, facebookUrl, twitterUrl, instagramUrl, spotifyUrl, recentAlbums, artistKindId "
            . "FROM artists";


        // foreach with key and value
        foreach ($filters as $key => $value) {
            switch ($key) {
                case "kind":
                    $sql .= " WHERE artistKindId = :$key ";
                    break;
                default:
                    break;
            }
        }

        switch ($sort) {
            case "name":
                $sql .= " ORDER BY name";
                break;
            case "name_desc":
                $sql .= " ORDER BY name DESC";
                break;
            default:
                $sql .= " ORDER BY artistId";
                break;
        }

        $statement = $this->connection->prepare($sql);

        foreach ($filters as $key => $value) {
            $pdoType = is_numeric($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $statement->bindValue(":" . $key, $value, $pdoType);
        }

        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $this->buildArtist($result);
    }

    /**
     * Returns the artist with the given id.
     */
    public function getById($id): Artist
    {
        $sql = "SELECT artistId, name, description, recentAlbums, genres, country, homepageUrl, facebookUrl, twitterUrl, instagramUrl, spotifyUrl, recentAlbums, artistKindId "
            . "FROM artists WHERE artistId = :id";
        $statement = $this->connection->prepare($sql);
        $statement->bindParam(":id", $id);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $artists = $this->buildArtist($result);
        if (count($artists) == 0) {
            throw new ObjectNotFoundException("Artist with id $id not found");
        }
        return $artists[0];
    }

    public function getDanceLineupByEventId($eventId): array
    {
        $sql = "SELECT
        a.artistId as artistId,
        a.name as artistName,
        a.description as artistDescription,
        a.recentAlbums as artistRecentAlbums,
        a.genres as artistGenres,
        a.country as artistCountry,
        a.homepageUrl as artistHomepage,
        a.facebookUrl as artistFacebook,
        a.twitterUrl as artistTwitter,
        a.instagramUrl as artistInstagram,
        a.spotifyUrl as artistSpotify,
        a.artistKindId as artistKindId
        FROM dancelineups d 
        JOIN artists a on a.artistId = d.artistId
        WHERE d.eventId = :id";
        $statement = $this->connection->prepare($sql);
        $statement->bindValue(":id", htmlspecialchars($eventId));
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $this->buildArtist($result);
    }

    /**
     * Inserts the given artist into the database.
     */
    public function insert(
        $name,
        $description,
        $recentAlbums,
        $genres,
        $country,
        $homepageUrl,
        $facebookUrl,
        $twitterUrl,
        $instagramUrl,
        $spotifyUrl,
        $artistKindId
    ): int {
        $sql = "INSERT INTO artists "
            . "(name, description, recentAlbums, genres, country, homepageUrl, facebookUrl, twitterUrl, instagramUrl, spotifyUrl, artistKindId) "
            . "VALUES (:name, :description, :recentAlbums, :genres, :country, :homepageUrl, :facebookUrl, :twitterUrl, :instagramUrl, :spotifyUrl, :artistKindId)";
        $statement = $this->connection->prepare($sql);
        $statement->bindParam(":name", $name);
        $statement->bindParam(":description", $description);
        $statement->bindParam(":recentAlbums", $recentAlbums);
        $statement->bindParam(":genres", $genres);
        $statement->bindParam(":country", $country);
        $statement->bindParam(":homepageUrl", $homepageUrl);
        $statement->bindParam(":facebookUrl", $facebookUrl);
        $statement->bindParam(":twitterUrl", $twitterUrl);
        $statement->bindParam(":instagramUrl", $instagramUrl);
        $statement->bindParam(":spotifyUrl", $spotifyUrl);
        $statement->bindParam(":artistKindId", $artistKindId);

        $statement->execute();

        return $this->connection->lastInsertId();
    }

    public function deleteById($artistId)
    {
        $sql = "DELETE FROM artists WHERE artistId = :artistId";
        $statement = $this->connection->prepare($sql);
        $statement->bindParam(":artistId", $artistId);
        $statement->execute();
    }

    public function update(
        $artistId,
        $name,
        $description,
        $recentAlbums,
        $genres,
        $country,
        $homepageUrl,
        $facebookUrl,
        $twitterUrl,
        $instagramUrl,
        $spotifyUrl,
        $artistKindId
    ) {
        $sql = "UPDATE artists SET "
            . "name = :name, "
            . "description = :description, "
            . "recentAlbums = :recentAlbums, "
            . "genres = :genres, "
            . "country = :country, "
            . "homepageUrl = :homepageUrl, "
            . "facebookUrl = :facebookUrl, "
            . "twitterUrl = :twitterUrl, "
            . "instagramUrl = :instagramUrl, "
            . "spotifyUrl = :spotifyUrl, "
            . "artistKindId = :artistKindId "
            . "WHERE artistId = :artistId";

        $statement = $this->connection->prepare($sql);
        $statement->bindParam(":artistId", $artistId);
        $statement->bindParam(":name", $name);
        $statement->bindParam(":description", $description);
        $statement->bindParam(":recentAlbums", $recentAlbums);
        $statement->bindParam(":genres", $genres);
        $statement->bindParam(":country", $country);
        $statement->bindParam(":homepageUrl", $homepageUrl);
        $statement->bindParam(":facebookUrl", $facebookUrl);
        $statement->bindParam(":twitterUrl", $twitterUrl);
        $statement->bindParam(":instagramUrl", $instagramUrl);
        $statement->bindParam(":spotifyUrl", $spotifyUrl);
        $statement->bindParam(":artistKindId", $artistKindId);
        $statement->execute();
    }

    private function buildArtistKinds($arr): array
    {
        $output = [];
        foreach ($arr as $row) {
            $id = $row["id"];
            $name = $row["name"];
            $output[] = new ArtistKind($id, $name);
        }

        return $output;
    }

    private function getArtistKindById($id): ArtistKind
    {
        $sql = "SELECT id, name FROM artistkinds WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $this->buildArtistKinds($stmt->fetchAll())[0];
    }

    public function getArtistKinds(): array
    {
        $sql = "SELECT id, name FROM artistkinds";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        return $this->buildArtistKinds($stmt->fetchAll());
    }

    public function getKindOfArtist($id): ArtistKind
    {
        $sql = "SELECT a.artistId, ak.id, ak.name "
            . "FROM artists a "
            . "INNER JOIN artistkinds ak ON a.artistKindId = ak.id "
            . "WHERE a.artistId = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $this->buildArtistKinds($stmt->fetchAll())[0];
    }
}