<?php

class RestaurantType implements JsonSerializable {

 private int $typeId;
 private string $typeName;

 public function jsonSerialize(): mixed
 {
     return [
            'typeId' => $this->typeId,
            'typeName' => $this->typeName
     ];
 }


	/**
	 * @return int
	 */
	public function getTypeId(): int {
		return $this->typeId;
	}
	
	/**
	 * @param int $typeId 
	 * @return self
	 */
	public function setTypeId(int $typeId): self {
		$this->typeId = $typeId;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getTypeName(): string {
		return $this->typeName;
	}
	
	/**
	 * @param string $typeName 
	 * @return self
	 */
	public function setTypeName(string $typeName): self {
		$this->typeName = $typeName;
		return $this;
	}
}

?>