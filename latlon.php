<?php
namespace PMVC\PlugIn\latlon;

\PMVC\l(__DIR__.'/src/MercatorProjection.php');
\PMVC\l(__DIR__.'/src/GeoPoint.php');

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\latlon';

class latlon extends \PMVC\PlugIn
{
    private $_mercator;
    public function init()
    {
        $mp = new MercatorProjection(); 
        $this->setDefaultAlias($mp);
    }

    public function getLatLon($lat, $lon)
    {
        return new GeoPoint($lat, $lon);
    }
}
