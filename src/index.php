<?php

/**
 * Created by PhpStorm.
 * User: alex
 * Date: 15.03.17
 * Time: 19:49
 */

spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

$start = '1979/12/28'; //change by hands, in future it will be like, for example, $_POST['start'];
$end = '2012/02/29'; //absolutely the same
$difference = MyDate::diff($start, $end);

if(!$difference['is_valid']) {
    echo "Sorry, it's no valid date. Month is not more than 12, days in month no more than by Gregorian Calendar";
} else {
    echo "<b>Years</b>: ".$difference['years'].'<br>';
    echo "<b>Months</b>: ".$difference['months'].'<br>';
    echo "<b>Days</b>: ".$difference['days'].'<br>';
    echo "<b>Total Days</b>: ".$difference['total_days'].'<br><br>';
    echo "Thank you for using our products";
}

