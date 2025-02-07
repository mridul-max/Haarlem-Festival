<?php

require_once("TicketLinkRepository.php");
require_once(__DIR__ . '/../models/Address.php');
require_once(__DIR__ . "/../models/Location.php");
require_once(__DIR__ . "/../models/Types/TicketType.php");
require_once(__DIR__ . "/../models/Types/EventType.php");
require_once(__DIR__ . "/../models/Music/Artist.php");
require_once(__DIR__ . "/../models/Music/ArtistKind.php");
require_once(__DIR__ . "/../models/Music/JazzEvent.php");
require_once(__DIR__ . "/../models/TicketLink.php");

/**
 * @author Konrad
 */
class JazzTicketLinkRepository extends TicketLinkRepository
{
    protected function build($arr): array
    {
        $output = array();
        foreach ($arr as $item) {
            $address = new Address();
            $address->setAddressId($item['addressId']);
            $address->setStreetName($item['addressStreetName']);
            $address->setHouseNumber($item['addressHouseNumber']);
            $address->setPostalCode($item['addressPostalCode']);
            $address->setCity($item['addressCity']);
            $address->setCountry($item['addressCountry']);

            $location = new Location();
            $location->setLocationId($item['locationId']);
            $location->setName($item['locationName']);
            $location->setAddress($address);
            $location->setLocationType($item['locationType']);
            $location->setLon($item['locationLon']);
            $location->setLat($item['locationLat']);
            $location->setCapacity($item['locationCapacity']);
            $location->setDescription($item['locationDescription']);

            $ticketType = new TicketType(
                $item['ticketTypeId'],
                $item['ticketTypeName'],
                $item['ticketTypePrice'],
                $item['ticketTypeNrOfPeople']
            );
            $eventType = new EventType(
                $item['eventTypeId'],
                $item['eventTypeName'],
                $item['evenTypeVat']
            );
            $artistKind = new ArtistKind(
                $item['artistKindId'],
                $item['artistKindName']
            );
            $artist = new Artist(
                $item['artistId'],
                htmlspecialchars_decode($item['artistName']),
                htmlspecialchars_decode($this->readIfSet($item['artistDescription'])),
                array(),
                $this->readIfSet($item['artistCountry']),
                $this->readIfSet($item['artistGenres']),
                $this->readIfSet($item['artistHomepage']),
                $this->readIfSet($item['artistFacebok']),
                $this->readIfSet($item['artistTwitter']),
                $this->readIfSet($item['artistInstagram']),
                $this->readIfSet($item['artistSpotify']),
                $this->readIfSet($item['artistRecentAlbums']),
                $artistKind
            );

            $event = new JazzEvent(
                $item['eventId'],
                htmlspecialchars_decode($item['eventName']),
                new DateTime($item['startTime']),
                new DateTime($item['endTime']),
                $artist,
                $location,
                $eventType,
                $item['availableTickets']
            );

            $ticketLink = new TicketLink(
                $item['ticketLinkId'],
                $event,
                $ticketType
            );

            array_push($output, $ticketLink);
        }

        return $output;
    }

    // Why such long query? Because we need to get all the data for the event, and we need to get the ticket type
    // If we get everything one by one, we will have a lot of queries, and that will be slow.
    // This way we get all the data in one query, and we can build the objects from that.
    // We are saving good 30 seconds of loading time.
    public function getAll($sort = null, $filters = [])
    {
        $sql = "SELECT e.eventId,
		e.name as eventName,
		e.startTime,
		e.endTime,
		e.availableTickets - (select count(t2.eventId) from tickets t2 where t2.eventid = e.eventId) as availableTickets,
		a.artistId,
		a.name  as artistName,
		a.description as artistDescription,
		a.recentAlbums as artistRecentAlbums,
		a.genres as artistGenres,
		a.country as artistCountry,
		a.homepageUrl as artistHomepage,
		a.facebookUrl as artistFacebok,
		a.twitterUrl as artistTwitter,
		a.instagramUrl as artistInstagram,
		a.spotifyUrl as artistSpotify,
		a.homepageUrl as artistHomepage,
		f.eventTypeId as eventTypeId,
		f.name as eventTypeName,
		f.VAT as evenTypeVat,
		l.locationId as locationId,
		l.name as locationName,
		l.locationType as locationType,
		l.capacity as locationCapacity,
		l.lon as locationLon,
		l.lat as locationLat,
		l.description as locationDescription,
		ad.addressId as addressId,
		ad.streetName as addressStreetName,
		ad.houseNumber as addressHouseNumber,
		ad.postalCode as addressPostalCode,
		ad.city as addressCity,
		ad.country as addressCountry,
		t.ticketTypeId as ticketTypeId,
		t.ticketTypeName as ticketTypeName,
		t.ticketTypePrice as ticketTypePrice,
		t.nrOfPeople as ticketTypeNrOfPeople,
		a2.id as artistKindId,
		a2.name as artistKindName,
        c.ticketLinkId as ticketLinkId
        FROM jazzevents je
        JOIN events e ON e.eventId = je.eventId
        JOIN ticketlinks c on e.eventId = c.eventId
        join tickettypes t on c.ticketTypeId = t.ticketTypeId
        join artists a on a.artistId = je.artistId
        join locations l on l.locationId = je.locationId
        join festivaleventtypes f on f.eventTypeId  = e.festivalEventType
        join addresses ad on ad.addressId =l.addressId
        join artistkinds a2 on a2.id = a.artistKindId ";

        if (!empty($filters)) {
            $sql .= " WHERE ";

            foreach ($filters as $key => $filter) {
                switch ($key) {
                    case 'price_from':
                        $sql .= " t.ticketTypePrice >= :$key ";
                        break;
                    case 'price_to':
                        $sql .= " t.ticketTypePrice <= :$key ";
                        break;
                    case 'time_from':
                        $sql .= " HOUR(e.startTime) >= :$key ";
                        break;
                    case 'time_to':
                        $sql .= " HOUR(e.endTime) <= :$key ";
                        break;
                    case 'hide_without_seats':
                        $sql .= " availableTickets > 0 ";
                        break;
                    case 'day':
                        $sql .= " DAY(e.startTime) = :$key ";
                        break;
                    case 'date':
                        $sql .= " DATE(e.startTime) = :$key ";
                        break;
                    case 'location':
                        $sql .= " je.locationId = :$key ";
                        break;
                    case 'artist':
                        $sql .= " je.artistId = :$key ";
                        break;
                    case "artist_kind":
                        $sql .= " a.artistKindId = :$key ";
                        break;
                    default:
                        continue 2;
                }

                // Add "AND" if there are more filters
                if (next($filters)) {
                    $sql .= " AND ";
                }
            }
        }

        if (isset($sort)) {
            switch ($sort) {
                case "time_desc":
                    $sql .= " ORDER BY e.startTime DESC";
                    break;
                case "price":
                    $sql .= " ORDER BY t.ticketTypePrice";
                    break;
                case "price_desc":
                    $sql .= " ORDER BY t.ticketTypePrice DESC";
                    break;
                default:
                    $sql .= " ORDER BY e.startTime";
                    break;
            }
        }

        $stmt = $this->connection->prepare($sql);

        foreach ($filters as $key => $filter) {
            // If the filter is empty, skip. DO NOT SKIP IF THAT IS A NUMBER!
            if (empty($filter) && !is_numeric($filter)) {
                continue;
            }

            $pdoType = is_numeric($filter) ? PDO::PARAM_INT : PDO::PARAM_STR;
            if (str_starts_with($key, 'price')) {
                $pdoType = PDO::PARAM_STR;
            }
            $stmt->bindValue(':' . $key, $filter, $pdoType);
        }

        $stmt->execute();
        $result = $stmt->fetchAll();
        return $this->build($result);
    }


    public function getById($id)
    {
        $sql = "SELECT e.eventId,
		e.name as eventName,
		e.startTime,
		e.endTime,
		e.availableTickets - (select count(t2.eventId) from tickets t2 where t2.eventid = e.eventId) as availableTickets,
		a.artistId,
		a.name  as artistName,
		a.description as artistDescription,
		a.recentAlbums as artistRecentAlbums,
		a.genres as artistGenres,
		a.country as artistCountry,
		a.homepageUrl as artistHomepage,
		a.facebookUrl as artistFacebok,
		a.twitterUrl as artistTwitter,
		a.instagramUrl as artistInstagram,
		a.spotifyUrl as artistSpotify,
		a.homepageUrl as artistHomepage,
		f.eventTypeId as eventTypeId,
		f.name as eventTypeName,
		f.VAT as evenTypeVat,
		l.locationId as locationId,
		l.name as locationName,
		l.locationType as locationType,
		l.capacity as locationCapacity,
		l.lon as locationLon,
		l.lat as locationLat,
		l.description as locationDescription,
		ad.addressId as addressId,
		ad.streetName as addressStreetName,
		ad.houseNumber as addressHouseNumber,
		ad.postalCode as addressPostalCode,
		ad.city as addressCity,
		ad.country as addressCountry,
		t.ticketTypeId as ticketTypeId,
		t.ticketTypeName as ticketTypeName,
		t.ticketTypePrice as ticketTypePrice,
		t.nrOfPeople as ticketTypeNrOfPeople,
		a2.id as artistKindId,
		a2.name as artistKindName,
        c.ticketLinkId as ticketLinkId
        FROM jazzevents je
        JOIN events e ON e.eventId = je.eventId
        JOIN ticketlinks c on e.eventId = c.eventId
        join tickettypes t on c.ticketTypeId = t.ticketTypeId
        join artists a on a.artistId = je.artistId
        join locations l on l.locationId = je.locationId
        join festivaleventtypes f on f.eventTypeId  = e.festivalEventType
        join addresses ad on ad.addressId =l.addressId
        join artistkinds a2 on a2.id = a.artistKindId
        WHERE ticketLinkId = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        $output = $this->build($result);
        if (empty($output)) {
            return null;
        }
        return $output[0];
    }

    public function getByEventId($id): ?TicketLink
    {
        $sql = "SELECT e.eventId,
		e.name as eventName,
		e.startTime,
		e.endTime,
		e.availableTickets - (select count(t2.eventId) from tickets t2 where t2.eventid = e.eventId) as availableTickets,
		a.artistId,
		a.name  as artistName,
		a.description as artistDescription,
		a.recentAlbums as artistRecentAlbums,
		a.genres as artistGenres,
		a.country as artistCountry,
		a.homepageUrl as artistHomepage,
		a.facebookUrl as artistFacebok,
		a.twitterUrl as artistTwitter,
		a.instagramUrl as artistInstagram,
		a.spotifyUrl as artistSpotify,
		a.homepageUrl as artistHomepage,
		f.eventTypeId as eventTypeId,
		f.name as eventTypeName,
		f.VAT as evenTypeVat,
		l.locationId as locationId,
		l.name as locationName,
		l.locationType as locationType,
		l.capacity as locationCapacity,
		l.lon as locationLon,
		l.lat as locationLat,
		l.description as locationDescription,
		ad.addressId as addressId,
		ad.streetName as addressStreetName,
		ad.houseNumber as addressHouseNumber,
		ad.postalCode as addressPostalCode,
		ad.city as addressCity,
		ad.country as addressCountry,
		t.ticketTypeId as ticketTypeId,
		t.ticketTypeName as ticketTypeName,
		t.ticketTypePrice as ticketTypePrice,
		t.nrOfPeople as ticketTypeNrOfPeople,
		a2.id as artistKindId,
		a2.name as artistKindName,
        c.ticketLinkId as ticketLinkId
        FROM jazzevents je
        JOIN events e ON e.eventId = je.eventId
        JOIN ticketlinks c on e.eventId = c.eventId
        join tickettypes t on c.ticketTypeId = t.ticketTypeId
        join artists a on a.artistId = je.artistId
        join locations l on l.locationId = je.locationId
        join festivaleventtypes f on f.eventTypeId  = e.festivalEventType
        join addresses ad on ad.addressId =l.addressId
        join artistkinds a2 on a2.id = a.artistKindId
        WHERE e.eventId = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $output = $this->build($result);
        if (empty($output)) {
            return null;
        }
        return $output[0];
    }
}
