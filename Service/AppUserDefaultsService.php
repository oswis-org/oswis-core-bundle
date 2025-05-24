<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Service;

use InvalidArgumentException;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserRole;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserType;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Nameable;
use OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException;
use OswisOrg\OswisCoreBundle\Exceptions\NotFoundException;
use OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
use OswisOrg\OswisCoreBundle\Exceptions\UserNotFoundException;
use OswisOrg\OswisCoreBundle\Exceptions\UserNotUniqueException;
use OswisOrg\OswisCoreBundle\Provider\OswisCoreSettingsProvider;

class AppUserDefaultsService
{
    public function __construct(
        protected OswisCoreSettingsProvider $oswisCoreSettings,
        protected AppUserTypeService $appUserTypeService,
        protected AppUserRoleService $appUserRoleService,
        protected AppUserService $appUserService
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws InvalidTypeException
     * @throws NotFoundException
     * @throws NotImplementedException
     * @throws OswisException
     * @throws UserNotFoundException
     * @throws UserNotUniqueException
     */
    public function registerRoot(): void
    {
        $role = $this->appUserRoleService->create(new AppUserRole(new Nameable('Superuživatel', 'Root', null, null,
            'root'), 'ROOT'));
        $type = $this->appUserTypeService->create(new AppUserType(new Nameable('Root', null, null, null, 'root'), $role,
            true));
        $fullName = $this->oswisCoreSettings->getAdmin()['name'] ?? $this->oswisCoreSettings->getEmail()['name'] ?? null;
        $email = $this->oswisCoreSettings->getAdmin()['email'] ?? $this->oswisCoreSettings->getEmail()['address'] ?? null;
        $adminUser = new AppUser($fullName, 'admin', $email, null, $type);
        $this->appUserService->create($adminUser, false, true, false);
    }
}
