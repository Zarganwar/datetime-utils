<?php

/**
 * @author Martin Jirasek <jertin@seznam.cz>
 */
class DateTimeUtilsTest
{
	/**
	 * @return void
	 * @group mjirasek
	 * @group dateTimeUtils
	 */
	public function testGetCorrectDateTime()
	{
		// normal
		$datetime = DateTimeUtils::getCorrectDateTime("9:00", "20:00", true, true, DateTimeUtils::COUNTRY_CZ, new DateTime("2015-12-10 11:20:20"));
		$this->assertTrue($datetime == "2015-12-10 11:20:20");

		// same day morning
		$datetime = DateTimeUtils::getCorrectDateTime("9:00", "20:00", true, true, DateTimeUtils::COUNTRY_CZ, new DateTime("2015-12-10 08:59:59"));
		$this->assertTrue($datetime == "2015-12-10 09:00:00");

		// night
		$datetime = DateTimeUtils::getCorrectDateTime("9:00", "20:00", true, true, DateTimeUtils::COUNTRY_CZ, new DateTime("2015-12-10 20:01:00"));
		$this->assertTrue($datetime == "2015-12-11 09:00:00");

		// holiday
		$datetime = DateTimeUtils::getCorrectDateTime("9:00", "20:00", true, true, DateTimeUtils::COUNTRY_CZ, new DateTime("2014-05-01 11:20:20"));
		$this->assertTrue($datetime == "2014-05-02 09:00:00");

		// easter
		$datetime = DateTimeUtils::getCorrectDateTime("9:00", "20:00", true, true, DateTimeUtils::COUNTRY_CZ, new DateTime("2015-04-03 11:20:20"));
		$this->assertTrue($datetime == "2015-04-07 09:00:00");

		// weekend
		$datetime = DateTimeUtils::getCorrectDateTime("9:00", "20:00", true, true, DateTimeUtils::COUNTRY_CZ, new DateTime("2015-12-05 11:20:20"));
		$this->assertTrue($datetime == "2015-12-07 09:00:00");

		// next year
		$datetime = DateTimeUtils::getCorrectDateTime("9:00", "20:00", true, true, DateTimeUtils::COUNTRY_CZ, new DateTime("2016-12-31 11:20:20"));
		$this->assertTrue($datetime == "2017-01-02 09:00:00");
	}

	/**
	 * @return void
	 * @group mjirasek
	 * @group dateTimeUtils
	 */
	public function testGetEasterMonday()
	{
		$datetime = DateTimeUtils::getEasterMonday(2015);
		$this->assertTrue($datetime->format("Y-m-d") === "2015-04-06");

		$datetime = DateTimeUtils::getEasterMonday(2016);
		$this->assertTrue($datetime->format("Y-m-d") === "2016-03-28");
	}

	/**
	 * @return void
	 * @group mjirasek
	 * @group dateTimeUtils
	 */
	public function testGetEasterDays()
	{
		$days = DateTimeUtils::getEasterDays(2015, 'Y-m-d');
		$this->assertTrue($days[0] === "2015-04-03" && $days[1] === "2015-04-06");
	}
}