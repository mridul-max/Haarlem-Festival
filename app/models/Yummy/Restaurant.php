<?php

/**
 * Summary of Restaurant
 */
class Restaurant implements JsonSerializable
{

	protected int $restaurantId;
	protected String $restaurantName;
	protected int $addressId;
	public string $location;
	protected string $description;
	protected float $price;
	protected RestaurantType $type;
	protected int $rating;

	/**
	 * Summary of jsonSerialize
	 * @return mixed
	 */
	public function jsonSerialize(): mixed
	{
		return [
			'restaurantId' => $this->restaurantId,
			'restaurantName' => $this->restaurantName,
			'addressId' => $this->addressId,
			'description' => $this->description,
			'price' => $this->price,
			'type' => $this->type,
			'rating' => $this->rating
		];
	}
	/**
	 * @return int
	 */
	public function getRestaurantId(): int
	{
		return $this->restaurantId;
	}

	/**
	 * @param int $restaurantId
	 * @return self
	 */
	public function setRestaurantId(int $restaurantId): self
	{
		$this->restaurantId = $restaurantId;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRestaurantName(): string
	{
		return $this->restaurantName;
	}

	/**
	 * @param string $restaurantName
	 * @return self
	 */
	public function setRestaurantName(string $restaurantName): self
	{
		$this->restaurantName = $restaurantName;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getAddressId(): int
	{
		return $this->addressId;
	}

	/**
	 * @param int $addressId
	 * @return self
	 */
	public function setAddressId(int $addressId): self
	{
		$this->addressId = $addressId;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getNumOfSessions(): int
	{
		return $this->numOfSessions;
	}

	/**
	 * @param int $numOfSessions
	 * @return self
	 */
	public function setNumOfSessions(int $numOfSessions): self
	{
		$this->numOfSessions = $numOfSessions;
		return $this;
	}

	/**
	 * @return time
	 */
	public function getDurationOfSessions(): string
	{
		return $this->durationOfSessions;
	}

	/**
	 * @param time $durationOfSessions
	 * @return self
	 */
	public function setDurationOfSessions(time $durationOfSessions): self
	{
		$this->durationOfSessions = $durationOfSessions;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 * @return self
	 */
	public function setDescription(string $description): self
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPrice(): string
	{
		return $this->price;
	}

	/**
	 * @param string $price
	 * @return self
	 */
	public function setPrice(string $price): self
	{
		$this->price = $price;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getAvailableSeats(): int
	{
		return $this->availableSeats;
	}

	/**
	 * @param int $availableSeats
	 * @return self
	 */
	public function setAvailableSeats(int $availableSeats): self
	{
		$this->availableSeats = $availableSeats;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getRating(): int
	{
		return $this->rating;
	}

	/**
	 * @param int $rating
	 * @return self
	 */
	public function setRating(int $rating): self
	{
		$this->rating = $rating;
		return $this;
	}

	/**
	 * @return RestaurantType
	 */
	public function getTypeId(): RestaurantType
	{
		return $this->type;
	}

	/**
	 * @param RestaurantType $typeId
	 * @return self
	 */
	public function setTypeId(RestaurantType $type): RestaurantType
	{
		$this->type = $type;
		return $this->type;
	}
}
