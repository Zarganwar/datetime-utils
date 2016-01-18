<?php

namespace Zarganwar\DateTimeUtils;

/**
 *
 * @author Martin Jirasek <jertin@seznam.cz>
 */
class Common
{
	
	const COUNTRY_CZ = 'cz';
	const COUNTRY_SK = 'sk';
	
	/**
	 * 
	 * @param \DateTime $dateTime
	 * @return type
	 */
	public static function isWeekend(\DateTime $dateTime)
	{
		$weekDay = $dateTime->format('N');
		return ($weekDay == 6) || ($weekDay == 7);
	}

	/**
	 * 
	 * @param \DateTime $dateTime
	 * @param string $country
	 * @return bool
	 */
	public static function isHoliday(\DateTime $dateTime, $country = null)
	{
		$date = $dateTime->format('j. n.');
		$year = $dateTime->format('Y');

		if ($country === null) {
			$country = $this->country;
		}

		return in_array($date, self::getHolidayDays($country)) || in_array($date, self::getEasterDays($year));
	}

/**
 * 
 * @param string $year
 * @param string $format
 * @return array \DateTime[]
 */
	public static function getEasterDays($year, $format = 'j. n.')		
	{
		$easterMonday = self::getEasterMonday($year);
		$easterFriday = clone $easterMonday;
		$easterFriday->modify("-3 day");

		return array(
			$easterFriday->format($format),
			$easterMonday->format($format),
		);
	}

	/**
	 * 
	 * @param int $year
	 * @return \DateTime
	 * @throws \InvalidArgumentException
	 */
	public static function getEasterMonday($year)
	{
		$easterMondays = array(
			'2012' => array(4, 9),
			'2013' => array(4, 1),
			'2014' => array(4, 21),
			'2015' => array(4, 6),
			'2016' => array(3, 28),
			'2017' => array(4, 17),
			'2018' => array(4, 2),
			'2019' => array(4, 22),
			'2020' => array(4, 13),
			'2021' => array(4, 5),
			'2022' => array(4, 18),
			'2023' => array(4, 10),
			'2024' => array(4, 1),
			'2025' => array(4, 21),
			'2025' => array(4, 5),
		);

		$dateTime = new \DateTime;
		if (function_exists("easter_date") && is_callable("easter_date")) {
			// function can be undefined if PHP compiled without --enable-calendar
			$dateTime->setTimestamp(easter_date($year));
			$dateTime->modify("+1 day");
		} else {
			if (!isset($easterMondays[$year])) {
				throw new \InvalidArgumentException("Undefined easter monday for year ($year)");
			}
			$easterMonday = $easterMondays[$year];
			$dateTime->setDate($year, $easterMonday[0], $easterMonday[1]);
		}

		return $dateTime;
	}

	/**
	 * 
	 * @param string $country
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public static function getHolidayDays($country)
	{
		$holidays = array(
			self::COUNTRY_CZ => array(
				'1. 1.',
				'1. 5.',
				'8. 5.',
				'5. 7.',
				'6. 7.',
				'28. 9.',
				'28. 10.',
				'17. 11.',
				'24. 12.',
				'25. 12.',
				'26. 12.',
			),
			self::COUNTRY_SK => array(
				'1. 1.',
				'6. 1.',
				'1. 5.',
				'8. 5.',
				'5. 7.',
				'29. 8.',
				'1. 9.',
				'15. 9.',
				'1. 11.',
				'17. 11.',
				'24. 12.',
				'25. 12.',
				'26. 12.',
			),
		);

		if (!isset($holidays[$country])) {
			throw new \InvalidArgumentException("Holidays not defined for country $country");
		}

		return $holidays[$country];
	}

}
