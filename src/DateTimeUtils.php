<?php

/**
 * @author Martin Jirasek <jertin@seznam.cz>
 */
class DateTimeUtils
{
	const COUNTRY_CZ = 'cz';
	const COUNTRY_SK = 'sk';

	/**
	 * Returns holidays except Easter
	 * @param $country
	 * @return array
	 * @throw InvalidArgumentException
	 * @todo Add better holiday loading
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
			throw new InvalidArgumentException("Holidays not defined for country $country");
		}

		return $holidays[$country];
	}

	/**
	 * @param int $year
	 * @return DateTime
	 */
	public static function getEasterMonday($year)
	{
		$easterMondays = array(
			'2012' => array(4,9),
			'2013' => array(4,1),
			'2014' => array(4,21),
			'2015' => array(4,6),
			'2016' => array(3,28),
			'2017' => array(4,17),
			'2018' => array(4,2),
			'2019' => array(4,22),
			'2020' => array(4,13),
			'2021' => array(4,5),
			'2022' => array(4,18),
			'2023' => array(4,10),
			'2024' => array(4,1),
			'2025' => array(4,21),
			'2025' => array(4,5),
		);

		$dateTime = new DateTime();
		if (function_exists("easter_date") && is_callable("easter_date")) {
			// function can be undefined if PHP compiled without --enable-calendar
			$dateTime->setTimestamp(easter_date($year));
			$dateTime->modify("+1 day");
		} else {
			if (!isset($easterMondays[$year])) {
				throw new InvalidArgumentException("Undefined easter monday for year ($year)");
			}
			$easterMonday = $easterMondays[$year];
			$dateTime->setDate($year, $easterMonday[0], $easterMonday[1]);
		}

		return $dateTime;
	}

	/**
	 * @param int $year
	 * @param string $format
	 * @return array
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
	 * Method search nearest date from today. Deals with holidays, weekends, night hours
	 * @param $allovedStartTime
	 * @param $allowedEndTime
	 * @param bool|true $skipWeekend
	 * @param bool|true $skipHoliday
	 * @param string $country
	 * @return bool|string
	 */
	public static function getCorrectDateTime($allovedStartTime, $allowedEndTime, $skipWeekend = true, $skipHoliday = true, $country = self::COUNTRY_CZ, DateTime $dateTime = null)
	{
		$morningTimeExploded = self::getTimeExploded($allovedStartTime);
		$eveningTimeExploded = self::getTimeExploded($allowedEndTime);
		if ($dateTime === null) {
			$dateTime = new DateTime();
		}
		$timestamp = $dateTime->getTimestamp();

		$morningDateTime = clone $dateTime;
		$morningDateTime->setTime($morningTimeExploded[0], $morningTimeExploded[1], $morningTimeExploded[2]);
		$eveningDateTime = clone $dateTime;
		$eveningDateTime->setTime($eveningTimeExploded[0], $eveningTimeExploded[1], $eveningTimeExploded[2]);
		$endNightDateTime = clone $dateTime;
		$endNightDateTime->setTime(23, 0, 0);

		$foundTimestamp = $timestamp;
		$year = date('Y', $foundTimestamp);
		$addDays = 0;
		$found = false;
		while (!$found) {
			$searchedDateTime = new DateTime();
			$searchedDateTime->setTimestamp($foundTimestamp);
			// year test
			$newYear = date('Y', $foundTimestamp);
			if ($newYear > $year) {
				$year = $newYear;
				$found = false;
				continue;
			} else {
				$found = true;
			}

			// time range
			if ($foundTimestamp < $morningDateTime->getTimestamp()) {
				$foundTimestamp = $morningDateTime->getTimestamp();
				$found = true;
				continue;
			} elseif ($foundTimestamp > $eveningDateTime->getTimestamp() && $foundTimestamp < $endNightDateTime->getTimestamp()) {
				$addDays++;
				$foundTimestamp = self::getNextDay($dateTime, $addDays, $allovedStartTime);
				$found = false;
				continue;
			} else {
				$found = true;
			}

			// weekend
			if ($skipWeekend) {
				if (self::isWeekend($foundTimestamp)) {
					$addDays++;
					$foundTimestamp = self::getNextDay($dateTime, $addDays, $allovedStartTime);
					$found = false;
					continue;
				}
				$found = true;
			}

			// holidays
			if ($skipHoliday) {
				if (self::isHoliday($foundTimestamp, $country, $year)) {
					$addDays++;
					$foundTimestamp = self::getNextDay($dateTime, $addDays, $allovedStartTime);
					$found = false;
					continue;
				}
				$found = true;
			}
		}

		$dateTime->setTimestamp($foundTimestamp);

		return $dateTime->format('Y-m-d H:i:s');
	}
	
	/**
	 * 
	 * @param DateTime $dateTime
	 * @param int $addDays
	 * @param string $allovedStartTime
	 * @param bool $asDateTime
	 * @return int|\DateTime
	 */
	protected function getNextDay(DateTime $dateTime, $addDays, $allovedStartTime, $asDateTime = false)
	{
		$dt = clone $dateTime;		
		$dt->modify("+$addDays day $allovedStartTime");
		
		if ($asDateTime) {
			return $dt;
		}
		return $dt->getTimestamp();
	}

	/**
	 * 
	 * @param int $timestamp
	 * @return bool
	 */
	protected static function isWeekend($timestamp)
	{
		$dt = new DateTime();
		$dt->setTimestamp($timestamp);
		$weekDay = $dt->format('N');
		
		return ($weekDay == 6) || ($weekDay == 7);
	}
	
	/**
	 * 
	 * @param int $timestamp
	 * @param string $country
	 * @param int $year
	 * @return bool
	 */
	protected static function isHoliday($timestamp, $country, $year)
	{
		$dt = new DateTime();
		$dt->setTimestamp($timestamp);
		$date = $dt->format('j. n.');
		return in_array($date, self::getHolidayDays($country)) || in_array($date, self::getEasterDays($year));
	}
	
	/**
	 * @param $time
	 * @return array
	 */
	protected static function getTimeExploded($time)
	{
		$exploded = explode(':', trim($time));
		$ret = array();
		for ($i = 0; $i < 3; $i++) {
			$value = 0;
			if (isset($exploded[$i])) {
				$value = $exploded[$i];
			}
			$ret[] = $value;
		}
		return $ret;
	}
}