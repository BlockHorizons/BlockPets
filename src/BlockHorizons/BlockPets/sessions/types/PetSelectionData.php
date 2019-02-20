<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\sessions\types;

use BlockHorizons\BlockPets\pets\datastorage\types\PetData;

class PetSelectionData {

	/** @var string */
	public $type;
	/** @var float */
	public $scale;
	/** @var bool */
	public $is_baby;

	public function __construct(string $type, float $scale = 1.0, bool $is_baby = false) {
		$this->type = $type;
		$this->scale = $scale;
		$this->is_baby = $is_baby;
	}

	public function toPetData(string $name, string $owner): PetData {
		$data = new PetData($name, $this->type, $owner);
		$data->scale = $this->scale;
		$data->is_baby = $this->is_baby;
		return $data;
	}
}