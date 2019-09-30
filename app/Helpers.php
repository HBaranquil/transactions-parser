<?php

use Carbon\Carbon;

function isValidDate($date){
    try {
        Carbon::parse($date);
        return true;
    } catch (\Exception $e) {
        return false;
    }
}

function isDateWithValidFormat($format, $date){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
