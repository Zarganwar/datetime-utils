<?php

use Tester\Assert;
use Zarganwar\DateTimeUtils\Common;

require __DIR__ . '/bootstrap.php';

$datetime = Common::getEasterMonday(2015);
Assert::true($datetime->format("Y-m-d") === "2015-04-06");

$datetime = Common::getEasterMonday(2016);
Assert::true($datetime->format("Y-m-d") === "2016-03-28");

$days = Common::getEasterDays(2015, 'Y-m-d');
Assert::true($days[0] === "2015-04-03" && $days[1] === "2015-04-06");
