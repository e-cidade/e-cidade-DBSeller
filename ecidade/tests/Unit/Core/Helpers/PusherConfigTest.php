<?php

namespace Tests\Unit\Core\Helpers;

use Tests\TestCase;

class PusherConfigTest extends TestCase
{
    public function testGetPusherConfig()
    {
        $config = getPusherConfig();
        $this->assertTrue(is_object($config));
        $this->assertObjectHasAttribute('enabled', $config);
        $this->assertObjectHasAttribute('appKey', $config);
        $this->assertObjectHasAttribute('host', $config);
        $this->assertObjectHasAttribute('port', $config);
    }
}
