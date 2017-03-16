<?php

/**
 * Created by PhpStorm.
 * User: alex
 * Date: 16.03.17
 * Time: 2:02
 */

interface iDifferentCount
{
    public function validator($start, $end, $days);

    public function february($end, $days, $checkYear);

    public function totalDays($start, $end, $days);
}