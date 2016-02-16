<?php

use Tester\Assert;
use Zarganwar\DateTimeUtils\Skip;

require __DIR__ . '/bootstrap.php';

// normal
$skip = new Skip(new DateTime("2015-12-10 11:20:20"), "09:00:00", "20:00:00");
$skip->setSkipHoliday(true);
$skip->setSkipWeekend(true);
Assert::equal("2015-12-10 11:20:20", $skip->format('Y-m-d H:i:s'));

// same day morning
$skip = new Skip(new DateTime("2015-12-10 08:59:59"), "09:00:00", "20:00:00");
$skip->setSkipHoliday(true);
$skip->setSkipWeekend(true);
Assert::equal("2015-12-10 09:00:00", $skip->format('Y-m-d H:i:s'));

// night
$skip = new Skip(new DateTime("2015-12-10 20:01:00"), "09:00", "20:00");
$skip->setSkipHoliday(true);
$skip->setSkipWeekend(true);
Assert::equal("2015-12-11 09:00:00", $skip->format('Y-m-d H:i:s'));

// holiday
$skip = new Skip(new DateTime("2014-05-01 11:20:20"), "09:00", "20:00");
$skip->setSkipHoliday(true);
$skip->setSkipWeekend(true);
Assert::equal("2014-05-02 11:20:20", $skip->format('Y-m-d H:i:s'));

// easter
$skip = new Skip(new DateTime("2015-04-03 11:20:20"), "09:00", "20:00");
$skip->setSkipHoliday(true);
$skip->setSkipWeekend(true);
Assert::equal("2015-04-07 11:20:20", $skip->format('Y-m-d H:i:s'));

// weekend
$skip = new Skip(new DateTime("2015-12-05 11:20:20"), "09:00", "20:00");
$skip->setSkipHoliday(true);
$skip->setSkipWeekend(false);
Assert::equal("2015-12-05 11:20:20", $skip->format('Y-m-d H:i:s'));

$skip = new Skip(new DateTime("2015-12-05 11:20:20"), "09:00", "20:00");
$skip->setSkipHoliday(true);
$skip->setSkipWeekend(true);
Assert::equal("2015-12-07 11:20:20", $skip->format('Y-m-d H:i:s'));

// next year
$skip = new Skip(new DateTime("2016-12-31 11:20:20"), "09:00", "20:00");
$skip->setSkipHoliday(true);
$skip->setSkipWeekend(true);
Assert::equal("2017-01-02 11:20:20", $skip->format('Y-m-d H:i:s'));
