<?php

class Entity
{
    /** @var \Carbon\Carbon */
    protected $dateTime;

    /** @var string */
    protected $page;

    /** @var string */
    protected $browser;

    /** @var string */
    protected $os;

    /** @var string */
    protected $geo;

    /** @var string */
    protected $ref;

    /** @var string */
    protected $ip;

    /** @var string */
    protected $cookie;

    /**
     * @param mixed $dateTime
     * @return Entity
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @param mixed $page
     * @return Entity
     */
    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param string $browser
     * @return Entity
     */
    public function setBrowser($browser)
    {
        $this->browser = $browser;
        return $this;
    }

    /**
     * @return string
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * @param string $geo
     * @return Entity
     */
    public function setGeo($geo)
    {
        $this->geo = $geo;
        return $this;
    }

    /**
     * @return string
     */
    public function getGeo()
    {
        return $this->geo;
    }

    /**
     * @param string $ref
     * @return Entity
     */
    public function setRef($ref)
    {
        $this->ref = $ref;
        return $this;
    }

    /**
     * @return string
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * @param string $ip
     * @return Entity
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $os
     * @return Entity
     */
    public function setOs($os)
    {
        $this->os = $os;
        return $this;
    }

    /**
     * @return string
     */
    public function getOs()
    {
        return $this->os;
    }
}