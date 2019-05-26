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
     * @return int
     */
    final public function getGeoLatitude(): ?int
    {
        return $this->geoLatitude;
    }

    /**
     * @param int $geoLatitude
     */
    final public function setGeoLatitude(?int $geoLatitude): void
    {
        $this->geoLatitude = $geoLatitude;
    }


    /**
     * @return int
     */
    final public function getGeoElevation(): ?int
    {
        return $this->geoElevation;
    }

    /**
     * @param int $geoElevation
     */
    final public function setGeoElevation(?int $geoElevation): void
    {
        $this->geoElevation = $geoElevation;
    }

}
