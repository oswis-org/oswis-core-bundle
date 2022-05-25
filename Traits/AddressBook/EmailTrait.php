<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\AddressBook;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping\Column;
use Symfony\Component\Validator\Constraints\Email;

/**
 * Trait adds e-mail field.
 */
trait EmailTrait
{
    /**
     * E-mail address.
     */
    #[Column(name: 'email', type: 'string', unique: false, nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(ExistsFilter::class)]
    #[ApiFilter(OrderFilter::class)]
    #[Email(message: "Zadaná adresa {{ value }} není platná.", mode: 'strict')]
    protected ?string $email = null;

    public function getEmail(): string
    {
        return $this->email ?? '';
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
}
