<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

trait GeoCoordinatesTrait
{
    /**
     * Geo latitude (WGS 84).
     *
     * @var float|null
     * @Doctrine\ORM\Mapping\Column(type="decimal", precision=10, scale=7, nullable=true)
     *
     * @example 37.42242
     */
    protected ?float $geoLatitude = null;

    /**
     * Geo longitude (WGS 84).
     *
     * @var float|null
     * @Doctrine\ORM\Mapping\Column(type="decimal", precision=10, scale=7, nullable=true)
     *
     * @example -122.08585
     */
    protected ?float $geoLongitude = null;

    /**
     * Geo elevation (WGS 84, in meters).
     *
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     *
     * @example 1000
     */
    protected ?int $geoElevation = null;

    final public function getGeoLon(): ?float
    {
        return $this->getGeoLongitude();
    }

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

    final public function getGeoLat(): ?float
    {
        return $this->getGeoLatitude();
    }

    final public function getGeoLatitude(): ?float
    {
        return $this->geoLatitude;
    }

    final public function setGeoLatitude(?float $geoLatitude): void
    {
        $this->geoLatitude = $geoLatitude;
    }

    final public function getGeoEle(): ?int
    {
        return $this->getGeoElevation();
    }

    final public function getGeoElevation(): ?float
    {
        return $this->geoElevation;
    }

    final public function setGeoElevation(?float $geoElevation): void
    {
        $this->geoElevation = $geoElevation;
    }
}
