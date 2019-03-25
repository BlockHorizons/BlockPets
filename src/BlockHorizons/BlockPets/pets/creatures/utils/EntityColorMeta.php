<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures\utils;

interface EntityColorMeta {

	public const COLOR_WHITE = 0;
	public const COLOR_ORANGE = 1;
	public const COLOR_MAGENTA = 2;
	public const COLOR_LIGHT_BLUE = 3;
	public const COLOR_YELLOW = 4;
	public const COLOR_LIME = 5;
	public const COLOR_PINK = 6;
	public const COLOR_GRAY = 7;
	public const COLOR_LIGHT_GRAY = 8;
	public const COLOR_CYAN = 9;
	public const COLOR_PURPLE = 10;
	public const COLOR_BLUE = 11;
	public const COLOR_BROWN = 12;
	public const COLOR_GREEN = 13;
	public const COLOR_RED = 14;
	public const COLOR_BLACK = 15;

	public const ALL_COLORS = [
		self::COLOR_WHITE,
		self::COLOR_ORANGE,
		self::COLOR_MAGENTA,
		self::COLOR_LIGHT_BLUE,
		self::COLOR_YELLOW,
		self::COLOR_LIME,
		self::COLOR_PINK,
		self::COLOR_GRAY,
		self::COLOR_LIGHT_GRAY,
		self::COLOR_CYAN,
		self::COLOR_PURPLE,
		self::COLOR_BLUE,
		self::COLOR_BROWN,
		self::COLOR_GREEN,
		self::COLOR_RED,
		self::COLOR_BLACK
	];
}