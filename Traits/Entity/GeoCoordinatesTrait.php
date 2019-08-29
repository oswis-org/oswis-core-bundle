<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

trait GeoCoordinatesTrait
{

    /**
     * Geo latitude (WGS 84).
     * @var float|null
     * @Doctrine\ORM\Mapping\Column(type="decimal", precision=10, scale=7, nullable=true)
     * @example 37.42242
     */
    protected $geoLatitude;

    /**
     * Geo longitude (WGS 84).
     * @var float|null
     * @Doctrine\ORM\Mapping\Column(type="decimal", precision=10, scale=7, nullable=true)
     * @example -122.08585
     */
    protected $geoLongitude;

    /**
     * Geo elevation (WGS 84, in meters).
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     * @example 1000
     */
    protected $geoElevation;

    /**
     * @return int
     */
    final public function getGeoLongitude(): ?int
    {
        return $this->geoLongitude;
    }

    /**
     * @param int $geoLongitude
     */
    final public function setGeoLongitude(?int $geoLongitude): void
    {
        $this->geoLongitude = $geoLongitude;
    }

    /**
     * @return float|null
     */
    final public function getGeoLatitude(): ?float
    {
        return $this->geoLatitude;
    }

    /**
     * @param float|null $geoLatitude
     */
    final public function setGeoLatitude(?float $geoLatitude): void
    {
        $this->geoLatitude = $geoLatitude;
    }


    /**
     * @return float|null
     */
    final public function getGeoElevation(): ?float
    {
        return $this->geoElevation;
    }

    /**
     * @param float|null $geoElevation
     */
    final public function setGeoElevation(?float $geoElevation): void
    {
        $this->geoElevation = $geoElevation;
    }

    final public function getGeoLon(): ?float {
        return $this->getGeoLongitude();
    }

    final public function getGeoLat(): ?float {
        return $this->getGeoLatitude();
    }

    final public function getGeoEle(): ?int {
        return $this->getGeoElevation();
    }

}
