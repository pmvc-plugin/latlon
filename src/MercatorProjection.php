<?php
namespace PMVC\PlugIn\latlon;
use PMVC\PlugIn\image\Coord2D;
use PMVC\PlugIn\image\ImageSize;

/**
 * mercator projection
 */
class MercatorProjection{

    const MERCATOR_RANGE = 256;
    private $_pixelOrigin; 
    private $_pixelsPerLonDegree;
    private $_pixelsPerLonRadian;


    function __construct()
    {
        $this->_pixelOrigin = new Coord2D(self::MERCATOR_RANGE / 2, self::MERCATOR_RANGE / 2);
        $this->_pixelsPerLonDegree = self::MERCATOR_RANGE / 360;
        $this->_pixelsPerLonRadian = self::MERCATOR_RANGE / (2 * pi());
    }        

    /**
     * Lat and Lon to pixel xy
     */
    function fromLatLonToPoint(GeoPoint $latLon, $opt_point=null)
    {
        $point = $opt_point ? $opt_point : new Coord2D(0,0);
        $origin = $this->_pixelOrigin;
        $point->x = $origin->x + $latLon->lon * $this->_pixelsPerLonDegree;
        $siny = $this->bound(sin($this->degreesToRadians($latLon->lat)), -0.9999, 0.9999);
        $point->y = $origin->y + 0.5 * log((1 + $siny) / (1 - $siny)) * -$this->_pixelsPerLonRadian;
        return $point;
    }

    function bound($value, $opt_min, $opt_max) {
      if ($opt_min != null) $value = max($value, $opt_min);
      if ($opt_max != null) $value = min($value, $opt_max);
      return $value;
    }

    function degreesToRadians($deg) {
      return $deg * (pi() / 180);
    }

    function radiansToDegrees($rad) {
      return $rad / (pi() / 180);
    }

    public function fromPointToLatLon(Coord2D $point) {
      $origin = $this->_pixelOrigin;
      $lng = ($point->x - $origin->x) / $this->_pixelsPerLonDegree;
      $latRadians = ($point->y - $origin->y) / -$this->_pixelsPerLonRadian;
      $lat = $this->radiansToDegrees(2 * atan(exp($latRadians)) - pi() / 2);
      return new GeoPoint($lat, $lng);
    }

    /**
     * get corners for one static map
     * NE 135, NW 225, SE 45, SW 315  
     * @see http://miguel-miguel-matemticas.blogspot.tw/2012/03/sin-cos-y-tan-30-60-45-y-90.html
     */
    function getCorners (GeoPoint $center, $zoom, ImageSize $mapsize)
    {
        $scale = pow(2, $zoom);
        $centerXY = $this->fromLatLonToPoint($center);
        $SW_XY = new Coord2D (
               $centerXY->x - ($mapsize->w / 2) / $scale,
               $centerXY->y + ($mapsize->h / 2) / $scale
        );
        $NE_XY = new Coord2D (
               $centerXY->x + ($mapsize->w / 2) / $scale,
               $centerXY->y - ($mapsize->h / 2) / $scale
        );
        //get SE and NW from 2d
        $img = \PMVC\plug('image');

        $distance = $img->getDistance(
            $centerXY,
            $NE_XY //from any point, each point should equal
        );
        $NW_XY = $img->getPointByDistance(
            $centerXY,
            225,
            $distance
        );
        $SE_XY = $img->getPointByDistance(
            $centerXY,
            45,
            $distance
        );
        $xy = array(
            'NE'=>$NE_XY,
            'NW'=>$NW_XY,
            'SE'=>$SE_XY,
            'SW'=>$SW_XY,
            'center'=>$centerXY
        );
        $latlon = array();
        $static = \PMVC\plug('static_map');
        $map = array();
        foreach ($xy as $k=>$v) {
            $latlon[$k] = $this->fromPointToLatLon($v);
            $static['center'] = $latlon[$k]->toString();
            $map[$k] = $static->toUrl();
        }
        return (object)array(
            'latlon'=>$latlon,
            'xy'=>$xy,
            'map'=>$map
        );
    }


}

