<?php
PMVC\Load::plug();
PMVC\addPlugInFolder('../');
use PMVC\PlugIn\image\ImageSize;


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

    function testGetCorners()
    {
        $zoom = 12;
        $mp = PMVC\plug($this->_plug);
        $center = $mp->getLatLon(49.141404,-121.960988);
        $corners = $mp->getCorners($center,$zoom,new ImageSize(320,320));
        $url = \PMVC\plug('static_map');
        $url['center'] = $center->toString();
        $url->addMarkers($center->tostring());
        $url->addMarkers($corners->latlon['NE']->tostring());
        $url->addMarkers($corners->latlon['NW']->tostring());
        $url->addMarkers($corners->latlon['SE']->tostring());
        $url->addMarkers($corners->latlon['SW']->tostring());
        $url['zoom'] = $zoom;
        $expected = 'https://maps.googleapis.com/maps/api/staticmap?center=49.141404%2C-121.960988&size=640x640&zoom=12&markers=color:blue|label:|49.141404,-121.960988&markers=color:blue|label:|49.177326945514,-121.90605635937&markers=color:blue|label:|49.177326956626,-122.01591965762&markers=color:blue|label:|49.105454985508,-121.90605634238&markers=color:blue|label:|49.105454996637,-122.01591964062';
        $actual = $url->toUrl();
        $this->assertEquals($expected, $actual);
    }

}
