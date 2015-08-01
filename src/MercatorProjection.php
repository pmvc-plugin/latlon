<?php
namespace PMVC\PlugIn\mercator_projection;
use namespace PMVC\PlugIn\image\CoordPoint;

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
        $this->_pixelOrigin = new CoordPoint(self::MERCATOR_RANGE / 2, self::MERCATOR_RANGE / 2);;
        $this->_pixelsPerLonDegree = self::MERCATOR_RANGE / 360;
        $this->_pixelsPerLonRadian = self::MERCATOR_RANGE / (2 * pi());
    }        

    /**
     * Lat and Lon to pixel xy
     */
    function fromLatLonToPoint(GeoPoint $latLon, $opt_point=null)
    {
        $point = $opt_point ? $opt_point : new CoordPoint(0,0);
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

    public function fromPointToLatLon(CoordPoint $point) {
      $origin = $this->_pixelOrigin;
      $lng = ($point->x - $origin->x) / $this->_pixelsPerLonDegree;
      $latRadians = ($point->y - $origin->y) / -$this->_pixelsPerLonRadian;
      $lat = $this->radiansToDegrees(2 * atan(exp($latRadians)) - pi() / 2);
      return new GeoPoint($lat, $lng);
    }

    /**
     * get geo point "a", angle "angle", and dis "m"
     * return geo point "b"
     */
    function getNewPointByDistance(CoordPoint $pa, $angle, $len){
      $angle = deg2rad($angle);
      $pb = new CoordPoint($pa->x+$len*sin($angle), $pa->y+$len*cos($angle));
      return $pb;
    }

    /**
     * get corners for one static map
     */
    function getCorners (GeoPoint $center, $zoom, $mapWidth, $mapHeight){
        $scale = pow(2, $zoom);
        $centerXY = $this->fromLatLonToPoint($center);
        $SW_XY = new CoordPoint (
               $centerXY->x - ($mapWidth/2) / $scale,
               $centerXY->y + ($mapHeight/2) / $scale
        );
        $SW_LatLon = $this->fromPointToLatLon($SW_XY);
        $NE_XY = new CoordPoint (
               $centerXY->x + ($mapWidth/2) / $scale,
               $centerXY->y - ($mapHeight/2) / $scale
        ); 
        $NW_XY = new CoordPoint (
               -$centerXY->x + ($mapWidth/2) / $scale,
               -$centerXY->y - ($mapHeight/2) / $scale
        ); 
        $NE_LatLon = $this->fromPointToLatLon($NE_XY);
        var_dump($NE_XY,$NW_XY,$SW_XY);
        return (object)array(
            'NE'=>$NE_LatLon,
            'SW'=>$SW_LatLon
        );

    }


}

