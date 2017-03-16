<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 16.03.17
 * Time: 2:08
 */

class MyDate implements iDifferentCount
{

    public static function diff($start, $end)
    {
        $result = [
            'years' => null,
            'months' => null,
            'days' => null,
            'total_days' => null,
            'invert' => false,
            'is_valid' => true
        ];

        $days = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 32];

        $start = array_map('intval', explode('/', $start));
        $end = array_map('intval', explode('/', $end));

//        validation of date
        if (self::validator($start, $end, $days)) {

            $result['total_days'] = self::totalDays($start, $end, $days); //count total days
            $checkStart = $start;
            $checkEnd = $end;

            $monthStart = ($start[0] *= 12) + $start[1];
            $monthEnd = ($end[0] *= 12) + $end[1]; //turn years and month into months

            $difference = $monthEnd - $monthStart;
            $result['years'] = 0;

            if ($end[1] == 1) { //avoid errors with January (setting December before)
                $correctMonth = 11;
            } else {
                $correctMonth = $end[1];
            }

            /**
             * all of logic was building by difference of monthes (year+monthes)
             */
            if ($difference > 12) {
                if ($end[2] >= $start[2]) {
                    $result['years'] = (int)floor($difference / 12);
                    $result['months'] = $difference % 12;
                    $result['days'] = $end[2] - $start[2];
                } else {
                    $result['years'] = (int)floor($difference / 12);
                    $result['months'] = ($difference % 12 == 0) ? 11 : ($difference % 12) - 1;
                    $result['days'] = ($days[$end[1]] - $start[2]) - 1 + $end[2];
                }

            } else if ($difference == 12) {
                if ($end[2] >= $start[2]) {
                    $result['years'] = 1;
                    $result['months'] = 0;
                    $result['days'] = $end[2] - $start[2];
                } else {
                    $result['months'] = 11;
                    $result['days'] = ($days[$correctMonth - 2] - $start[2]) + $end[2];
                }
                $result['total_days'] = $result['days'];

            } else if ($difference > 0 && $difference < 12) {
                if ($end[0] > $start[0]) {
                    if ($end[2] >= $start[2]) {
                        $result['months'] = 12 - $start[1] + $end[1];
                        $result['days'] = $end[2] - $start[2];
                    } else {
                        $result['months'] = 11 - $start[1] + $end[1];
                        $result['days'] = ($days[$correctMonth - 2] - $start[2]) + $end[2];
                    }
                } else {
                    if ($end[2] >= $start[2]) {
                        $result['months'] = $end[1] - $start[1];
                        $result['days'] = $end[2] - $start[2];
                    } else {
                        $result['months'] = $end[1] - $start[1] - 1;
                        $result['days'] = ($days[$correctMonth - 2] - $start[2]) + $end[2];
                    }
                }

            } else if ($difference == 0) {
                if ($end[2] >= $start[2]) {
                    $result['months'] = 0;
                    $result['days'] = $end[2] - $start[2];
                } else {
                    $result['years'] = null;
                    $result['months'] = null;
                    $result['invert'] = true;
                }

            } else if ($difference < 0) {
                $result['invert'] = true;
            }

            //adding day if February of leap year
            $result['days'] = self::february($checkEnd, $result['days'], $checkStart);

            return $result;

        } else {
            $result['is_valid'] = false;
            return $result;
        }
    }

    /**
     * Validate for avoid of wrong data
     * @param $start
     * @param $end
     * @param $days
     * @return bool
     */
    public function validator($start, $end, $days)
    {
        $result = false;
        if (($start[1] > 0 && $start[1] <= 12) && ($end[1] > 0 && $end[1] <= 12)) {
            if ($start[1] != 2 && $end[1] != 2) {
                if (($start[2] > 0 && $start[2] <= $days[$start[1] - 1]) &&
                    ($end[2] > 0 && $end[2] <= $days[$end[1] - 1])
                ) {
                    $result =  true;
                }
            } else {
               if(self::validationFebruary($start) && self::validationFebruary($end)) {
                   $result =  true;
               }
            }
        }
        return $result;
    }

    /**
     * adding day if leap-year
     * @param $end
     * @param $days
     * @param $checkYear
     * @return mixed
     */
    public function february($end, $days, $checkYear)
    {
        if ($end[1] == 3 && $checkYear[0] % 4 == 0) {
            $days++;
        }
        return $days;
    }

    /**
     * count all days between dates
     * @param $start
     * @param $end
     * @param $days
     * @return int
     */
    public function totalDays($start, $end, $days)
    {
        $countDays = $days[$start[1] - 1] - $start[2]; //days of first non-full month
        $countDays += $end[2] - 1; // passed days of last month

        //counting months between
        if ($end[0] > $start[0]) {

            for ($i = $start[1]; $i < 12; $i++) {
                $countDays += $days[$i];
            }
            for ($i = 0; $i < $end[1] - 1; $i++) {
                $countDays += $days[$i];
            }

            for ($i = $start[0] + 1; $i < $end[0]; $i++) {
                if ($i % 4 == 0) {
                    $countDays += 366; //don't forget about leap-year February!
                } else {
                    $countDays += 365;
                }
            }
        } else if ($end[0] == $start[0]) {
            for ($i = $start[1]; $i < $end[1] - 1; $i++) {
                $countDays += $days[$i];
            }
            if ($start[1] <= 2 && $end[0] % 4 == 0) {
                $countDays++;
            }
        } else if ($end[0] < $start[0]) {
            $countDays = null;
        }
        return $countDays;
    }

    public function validationFebruary($checkArray) {
        $result = false;
        if ($checkArray[0] % 4  == 0 ) {
            if($checkArray[2] < 30) {
                $result =  true;
            }
        } else {
            if($checkArray[2] < 29) {
                $result =  true;
            }
        }

        return $result;
    }

}