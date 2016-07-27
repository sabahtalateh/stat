<?php

namespace App\Components\Statistic;

class RedisStorage implements Storage
{
    /** @var \Predis\Client */
    protected $predis;

    const PAGE_SCOPE = 'page';
    const BROWSER_SCOPE = 'browser';
    const PLATFORM_SCOPE = 'platform';
    const GEO_SCOPE = 'geo';
    const REF_SCOPE = 'ref';

    /**
     * Storage constructor.
     * @param \Predis\Client $predis
     */
    public function __construct(\Predis\Client $predis)
    {
        $this->predis = $predis;
    }

    public function getTotal()
    {
        $browserScope = static::BROWSER_SCOPE;
        $platformScope = static::PLATFORM_SCOPE;
        $geoScope = static::GEO_SCOPE;
        $refScope = static::REF_SCOPE;
        $pageScope = static::PAGE_SCOPE;

        $pipe = $this->predis->pipeline();
        $pipe->smembers($browserScope);
        $pipe->smembers($platformScope);
        $pipe->smembers($geoScope);
        $pipe->smembers($refScope);
        $pipe->smembers($pageScope);
        $pipeResult = $pipe->execute();

        $browsers = $pipeResult[0];
        $platforms = $pipeResult[1];
        $geos = $pipeResult[2];
        $refs = $pipeResult[3];
        $pages = $pipeResult[4];

        return [
            'browser' => $this->findBySingleScopeVals($browserScope, $browsers),
            'platform' => $this->findBySingleScopeVals($platformScope, $platforms),
            'geo' => $this->findBySingleScopeVals($geoScope, $geos),
            'ref' => $this->findBySingleScopeVals($refScope, $refs),
            'page' => $this->findBySingleScopeVals($pageScope, $pages)
        ];
    }

    public function getPerPage()
    {
        $browserScope = static::BROWSER_SCOPE;
        $platformScope = static::PLATFORM_SCOPE;
        $geoScope = static::GEO_SCOPE;
        $refScope = static::REF_SCOPE;
        $pageScope = static::PAGE_SCOPE;

        $pipe = $this->predis->pipeline();
        $pipe->smembers($browserScope);
        $pipe->smembers($platformScope);
        $pipe->smembers($geoScope);
        $pipe->smembers($refScope);
        $pipe->smembers($pageScope);
        $pipeResult = $pipe->execute();

        $browsers = $pipeResult[0];
        $platforms = $pipeResult[1];
        $geos = $pipeResult[2];
        $refs = $pipeResult[3];
        $pages = $pipeResult[4];

        $browserPage = [];
        $platformPage = [];
        $geoPage = [];
        $refsPage = [];

        foreach ($pages as $page) {
            $browserPage[] = $this->findByDoubleScopesVals($browserScope, $browsers, $pageScope, [$page]);

            $platformPage[] = $this->findByDoubleScopesVals($platformScope, $platforms, $pageScope, [$page]);

            $geoPage[] = $this->findByDoubleScopesVals($geoScope, $geos, $pageScope, [$page]);

            $refsPage[] = $this->findByDoubleScopesVals($refScope, $refs, $pageScope, [$page]);
        }

        return [
            'page:browser' => $browserPage,
            'page:platform' => $platformPage,
            'page:geo' => $geoPage,
            'page:ref' => $refsPage
        ];
    }
//    Допилить функцию для выборки по произвольному число скопов
//    private function findByMultipleScopeVals(array $scope)
//    {
//        $queries = [];
//        $queriesNum = 1;
//
//        foreach ($scope as $el) {
//            $queriesNum *= count($el['keyValues']);
//        }
//
//        for ($i = 0; $i < $queriesNum; $i++) {
//            $queries[] = "";
//        }
//
//        foreach ($scope as $el) {
//            $count = 0;
//            for ($i = 0; $i < count($queries); $i++) {
//                if ($count >= count($el['keyValues']))
//                    $count = 0;
//
//                $queries[$i] .= $el['keyName'] . '=' . $el['keyValues'][$count] . ':';
//
//                $count++;
//            }
//        }
//
//        foreach ($queries as &$query) {
//            $query = rtrim($query, ":");
//        }
//
//        dd($queries);
//    }


    private function findByDoubleScopesVals($firstKeyName, $firstKeyValues, $secondKeyName, $secondKeyValues)
    {
        $out = [];

        foreach ($firstKeyValues as $firstKeyValue) {

            foreach ($secondKeyValues as $secondKeyValue) {

                $out["{$firstKeyValue}:{$secondKeyValue}"]['hits'] = $this->predis->get("{$firstKeyName}={$firstKeyValue}:{$secondKeyName}={$secondKeyValue}:hits");

                $out["{$firstKeyValue}:{$secondKeyValue}"]['cookies'] = $this->predis->smembers("{$firstKeyName}={$firstKeyValue}:{$secondKeyName}={$secondKeyValue}:cookies");

                $out["{$firstKeyValue}:{$secondKeyValue}"]['ips'] = $this->predis->smembers("{$firstKeyName}={$firstKeyValue}:{$secondKeyName}={$secondKeyValue}:ips");

            }

        }

        return $out;

    }

    private function findBySingleScopeVals($keyName, $keyValues)
    {
        $out = [];

        foreach ($keyValues as $el) {

            $out[$el]['hits'] = $this->predis->get("{$keyName}={$el}:hits");

            $out[$el]['cookies'] = $this->predis->smembers("{$keyName}={$el}:cookies");

            $out[$el]['ips'] = $this->predis->smembers("{$keyName}={$el}:ips");
        }

        return $out;
    }

    /**
     * @param $page
     * @param $browser
     * @param $platform
     * @param $ip
     * @param $countryCode
     * @param $cookie
     * @param $time
     * @param null $referrer
     */
    public function collect($page, $browser, $platform, $ip, $countryCode, $cookie, $time, $referrer = null)
    {
        $this->predis->pipeline(function (\Predis\Pipeline\Pipeline $pipe) use ($page, $browser, $platform, $ip, $countryCode, $cookie, $time, $referrer) {

            $this->addStatisticsforAllPages($pipe, $browser, $cookie, $ip, $platform, $countryCode, $page, $referrer);

            $this->addStatisticPerPage($pipe, $browser, $cookie, $ip, $platform, $countryCode, $page, $referrer);

            $this->addScopes($pipe, $page, $browser, $platform, $countryCode, $referrer);
        });
    }

    /**
     * @param \Predis\Pipeline\Pipeline $pipe
     * @param $browser
     * @param $cookie
     * @param $ip
     * @param $platform
     * @param $countryCode
     * @param null $referrer
     */
    private function addStatisticsforAllPages($pipe, $browser, $cookie, $ip, $platform, $countryCode, $page, $referrer = null)
    {
        $browserScope = static::BROWSER_SCOPE;
        $platformScope = static::PLATFORM_SCOPE;
        $geoScope = static::GEO_SCOPE;
        $refScope = static::REF_SCOPE;
        $pageScope = static::PAGE_SCOPE;

        $pipe->incr("{$browserScope}={$browser}:hits");
        $pipe->sadd("{$browserScope}={$browser}:cookies", "{$cookie}");
        $pipe->sadd("{$browserScope}={$browser}:ips", "{$ip}");

        $pipe->incr("{$platformScope}={$platform}:hits");
        $pipe->sadd("{$platformScope}={$platform}:cookies", $cookie);
        $pipe->sadd("{$platformScope}={$platform}:ips", $ip);

        $pipe->incr("{$geoScope}={$countryCode}:hits");
        $pipe->sadd("{$geoScope}={$countryCode}:cookies", $cookie);
        $pipe->sadd("{$geoScope}={$countryCode}:ips", $ip);

        if ($referrer != null) {
            $pipe->incr("{$refScope}={$referrer}:hits");
            $pipe->sadd("{$refScope}={$referrer}:cookies", $cookie);
            $pipe->sadd("{$refScope}={$referrer}:ips", $ip);
        }

        $pipe->incr("{$pageScope}={$page}:hits");
        $pipe->sadd("{$pageScope}={$page}:cookies", $cookie);
        $pipe->sadd("{$pageScope}={$page}:ips", $ip);
    }

    /**
     * @param \Predis\Pipeline\Pipeline $pipe
     * @param $browser
     * @param $cookie
     * @param $ip
     * @param $platform
     * @param $countryCode
     * @param null $referrer
     */
    private function addStatisticPerPage($pipe, $browser, $cookie, $ip, $platform, $countryCode, $page, $referrer)
    {
        $browserScope = static::BROWSER_SCOPE;
        $platformScope = static::PLATFORM_SCOPE;
        $geoScope = static::GEO_SCOPE;
        $refScope = static::REF_SCOPE;
        $pageScope = static::PAGE_SCOPE;

        $pipe->incr("{$browserScope}={$browser}:{$pageScope}={$page}:hits");
        $pipe->sadd("{$browserScope}={$browser}:{$pageScope}={$page}:cookies", "{$cookie}");
        $pipe->sadd("{$browserScope}={$browser}:{$pageScope}={$page}:ips", "{$ip}");

        $pipe->incr("{$platformScope}={$platform}:{$pageScope}={$page}:hits");
        $pipe->sadd("{$platformScope}={$platform}:{$pageScope}={$page}:cookies", $cookie);
        $pipe->sadd("{$platformScope}={$platform}:{$pageScope}={$page}:ips", $ip);

        $pipe->incr("{$geoScope}={$countryCode}:{$pageScope}={$page}:hits");
        $pipe->sadd("{$geoScope}={$countryCode}:{$pageScope}={$page}:cookies", $cookie);
        $pipe->sadd("{$geoScope}={$countryCode}:{$pageScope}={$page}:ips", $ip);

        if ($referrer != null) {
            $pipe->incr("{$refScope}={$referrer}:{$pageScope}={$page}:hits");
            $pipe->sadd("{$refScope}={$referrer}:{$pageScope}={$page}:cookies", $cookie);
            $pipe->sadd("{$refScope}={$referrer}:{$pageScope}={$page}:ips", $ip);
        }
    }

    /**
     * @param \Predis\Pipeline\Pipeline $pipe
     * @param $page
     * @param $browser
     * @param $platform
     * @param $countryCode
     * @param $referrer
     */
    private function addScopes($pipe, $page, $browser, $platform, $countryCode, $referrer)
    {
        $pipe->sadd(static::BROWSER_SCOPE, $browser);
        $pipe->sadd(static::PAGE_SCOPE, $page);
        $pipe->sadd(static::PLATFORM_SCOPE, $platform);
        $pipe->sadd(static::GEO_SCOPE, $countryCode);
        $pipe->sadd(static::REF_SCOPE, $referrer);
    }
}