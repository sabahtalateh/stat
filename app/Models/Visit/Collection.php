<?php

class Collection
{
    /** @var Entity */
    protected $collection;

    public function add(Entity $e)
    {
        $this->collection[] = $e;
    }

    public function all()
    {
        return $this->collection;
    }
}