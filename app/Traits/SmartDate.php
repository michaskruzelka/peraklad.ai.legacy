<?php

namespace App\Traits;

use DateTime;
use Lang;

trait SmartDate
{
    /**
     * @param DateTime $date
     * @return string
     */
    protected function smartRepresent(DateTime $date)
    {
        $currentDate = clone $date;
        $currentDate->setTimestamp(time());
        $dateDiff = $currentDate->diff($date);
        $daysDiff =  (int) $currentDate->format('j') -  (int) $date->format('j');
        $hoursDiff = (int) $dateDiff->format('%h');

        if ($daysDiff > 0) {
            if ($daysDiff > 2) {
                return Lang::has('date.daysDiff')
                    ? Lang::get('date.daysDiff', ['days' => $daysDiff])
                    : $daysDiff . ' days ago'
                ;
            } elseif ($daysDiff == 2) {
                return Lang::has('date.aDayBeforeYesterday')
                    ? Lang::get('date.aDayBeforeYesterday')
                    : 'a day before yesterday'
                ;
            } elseif ($daysDiff == 1) {
                return Lang::has('date.yesterday')
                    ? Lang::get('date.yesterday')
                    : 'yesterday'
                ;
            }
        } elseif ($hoursDiff > 4) {
            return Lang::has('date.today')
                ? Lang::get('date.today')
                : 'today'
            ;
        } elseif ($hoursDiff > 1) {
            return Lang::has('date.hoursDiff')
                ? Lang::get('date.hoursDiff', ['hours' => $hoursDiff])
                : $hoursDiff . ' hours ago'
            ;
        } elseif ($hoursDiff == 1) {
            return Lang::has('date.hourAgo')
                ? Lang::get('date.hourAgo')
                : '1 hour ago'
            ;
        } else {
            if ( ! ($dateDiff->format('%i') > 20)) {
                return Lang::has('date.now') ? Lang::get('date.now') : 'now';
            } elseif ( ! ($dateDiff->format('%i') > 60) &&  ! ($dateDiff->format('%I') < 20)) {
                return Lang::has('date.halfHour') ? Lang::get('date.halfHour') : 'a half an hour ago';
            }
        }
        return $date->format('j/n/Y');
    }
}