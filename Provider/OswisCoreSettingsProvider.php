<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Provider;

use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Exception\LogicException;
use Symfony\Component\Mime\Exception\RfcComplianceException;

/**
 * Provider of settings for OSWIS core module.
 */
class OswisCoreSettingsProvider
{
    protected array $app = [];

    /** @var array{name?: string, email?: string, web?: string, phone?: string} $admin */
    protected array $admin = [];

    /** @var array{address?: string, name?: string, reply_path?: string, return_path?: string, archive_address?: string, archive_name?: string, default_subject?: string, logo?: string} $email */
    protected array $email = [];

    protected array $web = [];

    protected array $adminIPs = [];

    protected array $angularAdmin = [];

    /** @var array<string, string|null> barvy podle role (uzel `colors`), viz {@see getColors()} */
    protected array $colors = [];

    /**
     * @param array                                                                                                                                                                       $app
     * @param array{name?: string, email?: string, web?: string, phone?: string}                                                                                                          $admin
     * @param array{address?: string, name?: string, reply_path?: string, return_path?: string, archive_address?: string, archive_name?: string, default_subject?: string, logo?: string} $email
     * @param array                                                                                                                                                                       $web
     * @param array                                                                                                                                                                       $adminIPs
     * @param array                                                                                                                                                                       $angularAdmin
     */
    public function __construct(
        array $app,
        array $admin,
        array $email,
        array $web,
        array $adminIPs,
        array $angularAdmin,
        array $colors = [],
    ) {
        $this->app = $app;
        $this->admin = $admin;
        $this->email = $email;
        $this->web = $web;
        $this->adminIPs = $adminIPs;
        $this->angularAdmin = $angularAdmin;
        /** @var array<string, string|null> $colors */
        $this->colors = $colors;
    }

    final public function getArray(): array
    {
        return [
            'app'           => $this->getApp(),
            'admin'         => $this->getAdmin(),
            'email'         => $this->getEmail(),
            'web'           => $this->getWeb(),
            'admin_ips'     => $this->getAdminIPs(),
            'angular_admin' => $this->getAngularAdmin(),
            'colors'        => $this->getColors(),
        ];
    }

    final public function getApp(): array
    {
        return $this->app;
    }

    /**
     * @return array{name?: string, email?: string, web?: string, phone?: string}
     */
    final public function getAdmin(): array
    {
        return $this->admin;
    }

    /**
     * @return array{address?: string, name?: string, reply_path?: string, return_path?: string, archive_address?: string, archive_name?: string, default_subject?: string, logo?: string}
     */
    final public function getEmail(): array
    {
        return $this->email;
    }

    final public function getWeb(): array
    {
        return $this->web;
    }

    /**
     * Barvy podle role pro šablony (`{{ oswis.colors.primary }}`) — jediné místo, kde barvy mailů žijí.
     *
     * `primary` bez vlastní hodnoty = `web.color`; `primary_tint` je z ní dopočítaná desetina
     * (podklad bloku s tokenem) — ve tvaru, který šablony měly natvrdo.
     *
     * @return array<string, string>
     */
    final public function getColors(): array
    {
        $barvy = array_filter($this->colors, static fn (mixed $barva): bool => is_string($barva) && '' !== $barva);
        $web = $this->web['color'] ?? null;
        $barvy['primary'] ??= is_string($web) && '' !== $web ? $web : '#006FAD';
        $barvy['primary_tint'] = self::tint($barvy['primary'], '0.1');

        return $barvy;
    }

    /** `#006FAD` + `0.1` → `rgba(0, 111, 173, 0.1)`; jiný zápis než #rrggbb se vrátí beze změny. */
    private static function tint(string $hex, string $alpha): string
    {
        if (1 !== preg_match('/^#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i', $hex, $m)) {
            return $hex;
        }

        return sprintf('rgba(%d, %d, %d, %s)', hexdec($m[1]), hexdec($m[2]), hexdec($m[3]), $alpha);
    }

    final public function getAdminIPs(): array
    {
        return $this->adminIPs;
    }

    final public function getAngularAdmin(): array
    {
        return $this->angularAdmin;
    }

    final public function getCoreAppNameShort(): string
    {
        return $this->getCoreAppName();
    }

    final public function getCoreAppName(): string
    {
        return 'OSWIS';
    }

    final public function getCoreAppNameLong(): string
    {
        return 'One Simple Web IS';
    }

    /**
     * @return Address
     * @throws LogicException
     * @throws RfcComplianceException
     */
    public function getArchiveMailerAddress(): ?Address
    {
        $email = $this->getEmail();
        if ($email && !empty($email['archive_address']) && !empty($email['archive_name'])) {
            return new Address($email['archive_address'], $email['archive_name']);
        }

        return null;
    }

    /**
     * @throws AccessDeniedHttpException
     */
    public function checkAdminIP(?string $ip): void
    {
        if (!$this->isAdminIP($ip)) {
            throw new AccessDeniedHttpException("Nedostatečná oprávnění.");
        }
    }

    public function isAdminIP(?string $ip): bool
    {
        return IpUtils::checkIp(''.$ip, $this->getAdminIPs());
    }
}
