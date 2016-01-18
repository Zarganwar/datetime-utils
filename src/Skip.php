<?php

namespace Zarganwar\DateTimeUtils;

/**
 * @author Martin Jirasek <jertin@seznam.cz>
 */
class Skip
{	
	const FORMAT_DATE = "Y-m-d";
	const FORMAT_TIME = "H:i:s";	
	
	const START_TIME = '00:00:00';
	const END_TIME = '23:59:59';

	const DEFAULT_MORNING = '08:00:00';
	const DEFAULT_EVENING = '18:00:00';
	
	/**
	 *
	 * @var string
	 */
	private $country = Common::COUNTRY_CZ;

	/**
	 *
	 * @var bool
	 */
	private $skipWeekend = false;
	
	/**
	 *
	 * @var bool
	 */
	private $skipHoliday = false;
	
	/**
	 *
	 * @var string 
	 */
	private $morningTime;
	
	/**
	 *
	 * @var string 
	 */
	private $eveningTime;	
	
	/**
	 *
	 * @var \DateTime
	 */
	private $dateTime;
	
	/**
	 * 
	 * @param \DateTime $dateTime
	 * @param string $morningTime
	 * @param string $eveningTime
	 */
	public function __construct(\DateTime $dateTime, $morningTime = self::DEFAULT_MORNING, $eveningTime = self::DEFAULT_EVENING)
	{
		$this->dateTime = $dateTime;
		$this->morningTime = $morningTime;
		$this->eveningTime = $eveningTime;
	}
	
	/**
	 * 
	 * @param string $morningTime
	 */
	public function setMorningTime($morningTime)
	{
		$this->morningTime = $morningTime;
	}
	
	/**
	 * 
	 * @param string $eveningTime
	 */
	public function setEveningTime($eveningTime)
	{
		$this->eveningTime = $eveningTime;
	}
	
	/**
	 * 
	 * @param string $country
	 */
	public function setCountry($country)
	{	
		if (!in_array($country, array(Common::COUNTRY_CZ, Common::COUNTRY_SK))) {
			throw new \InvalidArgumentException("Unsupported country '$country'");
		}
		
		$this->country = $country;
	}
	
	/**
	 * 
	 * @param bool $skipWeekend
	 */
	public function setSkipWeekend($skipWeekend)
	{
		$this->skipWeekend = $skipWeekend;
	}

		/**
	 * 
	 * @param bool $skipHoliday
	 */
	public function setSkipHoliday($skipHoliday)
	{
		$this->skipHoliday = $skipHoliday;
	}

	/**
	 * 
	 * @param \DateTime $current
	 * @return \DateTime
	 */
	private function doCorrectTime(\DateTime $current)
	{
		if ($this->eveningTime < $this->morningTime) {
			throw new \OutOfRangeException("Evening time is less than morning time");
		}
		
		$currentTime = $current->format(self::FORMAT_TIME);
		$timeExploded = explode(':', $this->morningTime);
		$hour = isset($timeExploded[0]) ? $timeExploded[0] : 0;
		$minute = isset($timeExploded[1]) ? $timeExploded[1] : 0;
		$second = isset($timeExploded[2]) ? $timeExploded[2] : 0;
		
		if ($this->eveningTime <= $currentTime) {			
			$current->modify('+1 day');
			$current->setTime($hour, $minute, $second);
		}
		
		if ($currentTime < $this->morningTime) {			
			$current->setTime($hour, $minute, $second);
		}
		
		return $current;
	}
	
	/**
	 * 
	 * @param \DateTime $current
	 * @return \DateTime
	 */
	private function doCorrectDay(\DateTime $current)
	{
		$weekend = $this->skipWeekend && Common::isWeekend($current);
		$holiday = $this->skipHoliday && Common::isHoliday($current, $this->country);
		if ($weekend || $holiday) {
			$current->modify('+1 day');
			$current = $this->doCorrectDay($current);
		}
		
		return $current;
	}

	/**
	 * 
	 * @param string|null $format
	 * @return \DateTime|null
	 */
	public function format($format = null)
	{
		$current = clone $this->dateTime;
		
		$current = $this->doCorrectTime($current);
		$current = $this->doCorrectDay($current);
		
		if ($format === null) {
			return $current;
		}
		
		return $current->format($format);
	}

}