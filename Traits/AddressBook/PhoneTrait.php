<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\AddressBook;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use Doctrine\ORM\Mapping\Column;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Trait adds phone number field.
 */
trait PhoneTrait
{
    /** Phone number. */
    #[Column(type: 'string', unique: false, nullable: true)]
    #[Regex(pattern: "/^(\+420|\+421)? ?[1-9][0-9]{2} ?[0-9]{3} ?[0-9]{3}$/", message: "Zadané číslo ({{ value }}) není platným českým nebo slovenským telefonním číslem.",)]
    #[Length(min: 9, max: 15)]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    protected ?string $phone = null;

    public function getPhone(): string
    {
        return $this->phone ?? '';
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = preg_replace('/\s+/', '', $phone);
    }
}
