<?php

namespace ECidade\V3\Modification\Data;

class DataTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider provideDefaultData
     */
    public function testPaths($file, $prefix)
    {
        $data = new File($file);
        $path = ECIDADE_MODIFICATION_CACHE_PATH . $data->getPrefix() . $data->getOriginalPath();
        $this->assertEquals($data->getOriginalPath(), $file);
        $this->assertEquals($data->getPrefix(), $prefix);
        $this->assertEquals($data->getPath(), $path);
    }

    /**
     * @dataProvider provideDefaultData
     */
    public function testLoadContent($file, $prefix)
    {
        $data = new File($file);
        $this->assertEquals($data->getContent(), null);
        $data->loadContent();
        $this->assertEquals($data->getContent(), file_get_contents(ECIDADE_PATH . $file));
    }

    public function provideDefaultData()
    {
        return array(
            array('login.php', 'global/'),
        );
    }
}
