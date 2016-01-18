<?php

use Tester\Assert;
use Zarganwar\DateTimeUtils\Skip;

require __DIR__ . '/bootstrap.php';

// normal
$skip = new Skip(new DateTime("2015-12-10 11:20:20"), "09:00:00", "20:00:00");
$skip->setSkipHoliday(true);
$skip->setSkipWeekend(true);
Assert::true($skip->format('Y-m-d H:i:s') == "2015-12-10 11:20:20");

// same day morning
$skip = new Skip(new DateTime("2015-12-10 08:59:59"), "09:00:00", "20:00:00");
$skip->setSkipHoliday(true);
$skip->setSkipWeekend(true);
Assert::true($skip->format('Y-m-d H:i:s') == "2015-12-10 09:00:00");

// night
$skip = new Skip(new DateTime("2015-12-10 20:01:00"), "09:00", "20:00");
$skip->setSkipHoliday(true);
$skip->setSkipWeekend(true);
Assert::true($skip->format('Y-m-d H:i:s') == "2015-12-11 09:00:00");

// holiday
$skip = new Skip(new DateTime("2014-05-01 11:20:20"), "09:00", "20:00");
$skip->setSkipHoliday(true);
$skip->setSkipWeekend(true);
Assert::true($skip->format('Y-m-d H:i:s') == "2014-05-02 11:20:20");

// easter
$skip = new Skip(new DateTime("2015-04-03 11:20:20"), "09:00", "20:00");
$skip->setSkipHoliday(true);
$skip->setSkipWeekend(true);
Assert::true($skip->format('Y-m-d H:i:s') == "2015-04-07 11:20:20");

// weekend
$skip = new Skip(new DateTime("2015-12-05 11:20:20"), "09:00", "20:00");
$skip->setSkipHoliday(true);
$skip->setSkipWeekend(false);
Assert::true($skip->format('Y-m-d H:i:s') == "2015-12-05 11:20:20");

$skip = new Skip(new DateTime("2015-12-05 11:20:20"), "09:00", "20:00");
$skip->setSkipHoliday(true);
$skip->setSkipWeekend(true);
Assert::true($skip->format('Y-m-d H:i:s') == "2015-12-07 11:20:20");

// next year
$skip = new Skip(new DateTime("2016-12-31 11:20:20"), "09:00", "20:00");
$skip->setSkipHoliday(true);
$skip->setSkipWeekend(true);
Assert::true($skip->format('Y-m-d H:i:s') == "2017-01-02 11:20:20");
