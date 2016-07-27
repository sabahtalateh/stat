<?php

namespace App\Components\Statistic;


interface Storage
{
    public function collect($page, $browser, $platform, $ip, $countryCode, $cookie, $time, $referrer = null);

    public function getTotal();

    public function getPerPage();
}