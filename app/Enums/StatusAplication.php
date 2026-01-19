<?php

namespace App\Enums;

class StatusAplication
{
	public const PENDING = 'pending';
	public const APPROVED = 'approved';
	public const REJECTED = 'rejected';
	public const EDITED = 'edited';

	/**
	 * Zwraca listę wszystkich statusów (klucze)
	 *
	 * @return array
	 */
	public static function all(): array
	{
		return [
			self::PENDING,
			self::APPROVED,
			self::REJECTED,
			self::EDITED,
		];
	}

	/**
	 * Polskie etykiety statusów
	 *
	 * @return array<string,string>
	 */
	public static function labels(): array
	{
		return [
			self::PENDING => 'Oczekujący',
			self::APPROVED => 'Zatwierdzony',
			self::REJECTED => 'Odrzucony',
			self::EDITED => 'Edytowany',
		];
	}

	/**
	 * Zwraca polską etykietę dla statusu
	 *
	 * @param string $status
	 * @return string
	 */
	public static function label(string $status): string
	{
		$labels = self::labels();

		return $labels[$status] ?? $status;
	}

	/**
	 * Mapowanie kolorów (hex) dla statusów
	 *
	 * @return array<string,string>
	 */
	public static function colors(): array
	{
		return [
			self::PENDING => '#FFD54F', // żółty
			self::APPROVED => '#10B981', // zielony
			self::REJECTED => '#EF4444', // czerwony
			self::EDITED => '#3B82F6', // niebieski
		];
	}

	/**
	 * Zwraca hex koloru dla statusu
	 *
	 * @param string $status
	 * @return string|null
	 */
	public static function color(string $status): ?string
	{
		$colors = self::colors();

		return $colors[$status] ?? null;
	}

	/**
	 * Mapowanie klas Tailwind dla odznak/badge
	 *
	 * @return array<string,string>
	 */
	public static function badgeClasses(): array
	{
		return [
			self::PENDING => 'bg-yellow-100 text-yellow-800',
			self::APPROVED => 'bg-green-100 text-green-800',
			self::REJECTED => 'bg-red-100 text-red-800',
			self::EDITED => 'bg-blue-100 text-blue-800',
		];
	}

	/**
	 * Zwraca klasę Tailwind dla statusu (przydatne w widokach)
	 *
	 * @param string $status
	 * @return string|null
	 */
	public static function badgeClass(string $status): ?string
	{
		$map = self::badgeClasses();

		return $map[$status] ?? null;
	}
}

