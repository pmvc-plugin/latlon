<?php
PMVC\Load::plug();
PMVC\addPlugInFolder('../');
class LatLonTest extends PHPUnit_Framework_TestCase
{
    private $_plug='latlon';
    function testPlugin()
    {
        PMVC\plug($this->_plug);
        ob_start();
        print_r(PMVC\plug($this->_plug));
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertContains($this->_plug,$output);
    }

}
