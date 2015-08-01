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
    /*
        $mp = new MercatorProjection();
        $center = new GeoPoint(49.141404,-121.960988);
        $corners = $mp->getCorners($center,10,320,320);
        $url = \PMVC\plug('static_map');
        $url['center'] = $center->toString();
        $url->addMarkers($center->tostring());
        $url->addMarkers($corners->NE->tostring());
        $url->addMarkers($corners->SW->tostring());
        */
    }
}
