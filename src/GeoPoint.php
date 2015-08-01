<?php
namespace PMVC\PlugIn\mercator_projection;

class GeoPoint
{
    public $lat;
    public $lon;
    public function __construct($lat,$lon)
    {
        $this->lat = $lat;
        $this->lon = $lon;
    }
    public function toString()
    {
        return $this->lat.','.$this->lon;
    }
}
