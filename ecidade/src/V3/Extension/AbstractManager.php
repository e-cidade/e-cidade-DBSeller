<?php

namespace ECidade\V3\Extension;

use \ECidade\V3\Extension\Container;
use \ECidade\V3\Extension\Logger;

/**
 * @package extension
 */
abstract class AbstractManager
{
    /**
     * @var \ECidade\Extension\Container
     */
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container = null)
    {
        if ($container !== null) {
            return $this->container = $container;
        }

        $this->container = new Container();

        $this->container->register('logger', function () {
            return new Logger();
        });

        return $this;
    }

    public function setup()
    {
    }

    public function install($args)
    {
    }

    public function update()
    {
    }

    public function uninstall($args)
    {
    }

    /**
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->container->register('logger', $logger);
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->container->get('logger');
    }
}
