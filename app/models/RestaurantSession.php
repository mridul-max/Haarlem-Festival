<?php

Class RestaurantSession implements JsonSerializable{

protected int $id;
protected date $date;
protected int $AvailableSeats;
protected time $startTime;
protected time $endTime;

public function jsonSerialize() : mixed{
    return [
        'Id' => $this->id,
        'date' => $this->date,
        'AvailableSeats' => $this->AvailableSeats,
        'startTime' => $this->startTime,
        'endTime' => $this->endTime
    ];
}

public function getId() : int{
    return $this->id;

}
public function setId(int $id) : void{
    $this->id = $id;
}

public function getDate() : date{
    return $this->date;

}
public function setDate(date $date) : void{
    $this->date = $date;
}
public function getAvailableSeats() : int{
    return $this->AvailableSeats;
}
public function setAvailableSeats(int $AvailableSeats) : void{
    $this->AvailableSeats = $AvailableSeats;
}
public function getStartTime() : time{
    return $this->startTime;
}
public function setStartTime(time $startTime) : void{
    $this->startTime = $startTime;
}
public function getEndTime() : time{
    return $this->endTime;
}
public function setEndTime(time $endTime) : void{
    $this->endTime = $endTime;
}
}
?>