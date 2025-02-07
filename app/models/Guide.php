<?php

class Guide implements JsonSerializable
{
    private int $guideId;
    private string $firstName;
    private string $lastName;
    private string $language;

    private $description;

    public function jsonSerialize(): mixed
    {
        return [
            'guideId' => $this->guideId,
            'guideName' => $this->firstName,
            'lastName' => $this->lastName,
            'language' => $this->language
        ];
    }
    

    public function getGuideId(): int
    {
        return $this->guideId;
    }

    public function setGuideId(int $guideId): void
    {
        $this->guideId = $guideId;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    public function getDescription(): mixed
    {
        return $this->description;
    }

    public function setDescription(mixed $description): void
    {
        $this->description = $description;
    }
}