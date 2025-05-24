<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\AddressBook;

use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use Doctrine\ORM\Mapping\Column;
use Symfony\Component\Validator\Constraints as Assert;
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
    #[Assert\NotBlank()]
    #[Email(message: "Zadaná adresa {{ value }} není platná.", mode: 'strict')]
    protected ?string $email = null;

    /**
     * @return non-empty-string
     */
    public function getEmail(): string
    {
        /** @phpstan-ignore-next-line */
        return $this->email ?? '-';
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
}
