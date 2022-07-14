<?php

namespace App\Service;

class DateService
{
    public function getDate(): \DateTime
    {
        $date = new \DateTime();
        $dateString = $date->format('Y-m-d H:00:00');

        return new \DateTime($dateString);
    }

    public function getDeleteDate(): \DateTime
    {
        $date = $this->getDate();
        $deleteDate = $date->modify('-1 day');

        return $deleteDate;
    }

    /*
     *    @param mixed $hour
     */
    public function hourNormalizer($hour): int
    {
        $hour = intval($hour);

        if($hour > 24 || $hour < 1) return 1;

        return $hour;
    }

    public function calcPastDate(int $hour): \DateTime
    {
        $date = $this->getDate();
        $past_date = $date->modify("-${hour} hour");

        return $past_date;
    }
}