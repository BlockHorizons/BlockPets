<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\datastorage;

class SQLiteDataStorer extends SQLDataStorer {

	protected static function readBinaryString(string $string): string {
		return base64_decode($string);
	}

	protected static function writeBinaryString(string $string): string {
		return base64_encode($string);
	}
}