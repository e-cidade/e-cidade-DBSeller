<?php

namespace ECidade\Tributario\Library;

use ECidade\Tributario\Library\Collection;

abstract class ModelCollection extends Collection
{
    protected $resource;

    public function __construct($resource)
    {
        parent::__construct();

        $this->size = pg_num_rows($resource);
        $this->resource = $resource;
    }

    protected function fetchRow($resource, $index)
    {
        if (pg_num_rows($resource) == 0) {
            return false;
        }

        return pg_fetch_object($resource, $index);
    }
}
