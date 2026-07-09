<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\AddressBook;

use ADCI\FullNameParser\Exception\NameParsingException;
use ADCI\FullNameParser\Name;
use ADCI\FullNameParser\Parser as FullNameParser;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use Doctrine\ORM\Mapping\Column;
use Exception;
use InvalidArgumentException;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;
use OswisOrg\OswisCoreBundle\Interfaces\AddressBook\ContactInterface;
use Vokativ\Name as VokativName;
use function trim;

/**
 * Adds person-specific name fields and overrides name handling.
 *
 * Designed for entities that already inherit name/sortableName/shortName via
 * {@see \OswisOrg\OswisCoreBundle\Traits\Common\NameableTrait} from a parent
 * class (e.g. AbstractPerson extending AbstractContact). Use this trait
 * instead of {@see NameablePersonTrait} to avoid duplicate Doctrine column
 * declarations under Doctrine ORM 3+ strict validation.
 *
 * If you need both the person-specific overrides AND the underlying name
 * machinery in a single class (with no parent providing it), use
 * {@see NameablePersonTrait} which composes NameableTrait directly.
 */
trait PersonNameOnlyTrait
{
    /** Nickname. */
    #[Column(type: 'string', nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    protected ?string $nickname = null;

    /** First (given) name. */
    #[Column(type: 'string', nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    protected ?string $givenName = null;

    /** Middle (additional) name. */
    #[Column(type: 'string', nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    protected ?string $additionalName = null;

    /** Last (family) name. */
    #[Column(type: 'string', nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    protected ?string $familyName = null;

    /** Prefix (title before name). */
    #[Column(type: 'string', nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    protected ?string $honorificPrefix = null;

    /** Suffix (title after name). */
    #[Column(type: 'string', nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    protected ?string $honorificSuffix = null;

    /**
     * Manual gender override (ContactInterface::GENDER_MALE / GENDER_FEMALE), or null to
     * auto-detect from the given name via vokativ. Set this when the automatic detection is
     * wrong (ambiguous/foreign names — vokativ defaults unknowns to male) or doesn't match
     * the person's gender (e.g. trans participants). Drives getGender(), the Czech salutation
     * and the byl/byla suffix everywhere the contact is used.
     */
    #[Column(type: 'string', length: 8, nullable: true)]
    protected ?string $genderOverride = null;

    public function setName(?string $fullName): ?string
    {
        return $this->setFullName($fullName);
    }

    public function setFullName(?string $name): ?string
    {
        $name = trim(''.preg_replace('!\s+!', ' ', ''.$name));
        if ('' === $name) {
            return $this->updateName();
        }
        try {
            $nameObject = new FullNameParser()->parse($name);
        } catch (NameParsingException) {
            // The parser rejects any input it cannot decompose — most commonly a single token
            // with no surname ("Sonička", "Nguyen", a nickname). Swallowing the exception left
            // every name part null, and the updateName() below then rebuilt `name` from those
            // nulls as '' — silently destroying the name the user typed, while their e-mail and
            // phone were stored. That produced the "half-saved registration" rows (prod 1480,
            // 1548, 1549, 1925, 2742, 2874, 2929, 3320, 3343, 3395 — 2022 through 2026).
            $nameObject = null;
        }
        if ($nameObject instanceof Name) {
            $this->setHonorificPrefix($nameObject->getAcademicTitle());
            $this->setGivenName($nameObject->getFirstName());
            $this->setAdditionalName($nameObject->getMiddleName());
            $this->setFamilyName($nameObject->getLastName());
            $this->setHonorificSuffix($nameObject->getSuffix());
            $this->setNickname($nameObject->getNicknames());
        }
        // Invariant: a non-empty input must never end up as an empty name. Covers both the
        // rejected-input path above and any parse that yields nothing usable.
        if ('' === $this->getFullName()) {
            $this->setRawNameParts($name);
        }

        return $this->updateName();
    }

    /**
     * Fallback decomposition for names the parser cannot handle: first token is the given name
     * (a lone token is overwhelmingly a first name or nickname, and getSalutationName()/getGender()
     * both read givenName), last token — if any — is the family name, the rest is the middle name.
     *
     * @param string $name Whitespace-normalised, non-empty full name.
     */
    private function setRawNameParts(string $name): void
    {
        $parts = explode(' ', $name);
        $this->setHonorificPrefix(null);
        $this->setHonorificSuffix(null);
        $this->setNickname(null);
        $this->setGivenName(array_shift($parts));
        $this->setFamilyName([] === $parts ? null : array_pop($parts));
        $this->setAdditionalName([] === $parts ? null : implode(' ', $parts));
    }

    public function updateName(): ?string
    {
        $this->setSortableName($this->getSortableName());

        return $this->name = $this->getFullName();
    }

    public function getSortableName(): string
    {
        return $this->getFamilyName()
               .' '
               .$this->getAdditionalName()
               .' '
               .$this->getGivenName()
               .' '
               .$this->getHonorificPrefix()
               .' '
               .$this->getHonorificSuffix();
    }

    public function getFamilyName(): ?string
    {
        return $this->familyName ? ucfirst($this->familyName) : null;
    }

    public function setFamilyName(?string $familyName): void
    {
        $this->familyName = $familyName;
    }

    public function getAdditionalName(): ?string
    {
        return $this->additionalName;
    }

    public function setAdditionalName(?string $additionalName): void
    {
        $this->additionalName = $additionalName;
    }

    public function getGivenName(): ?string
    {
        return $this->givenName ? ucfirst($this->givenName) : null;
    }

    public function setGivenName(?string $givenName): void
    {
        $this->givenName = $givenName;
    }

    public function getHonorificPrefix(): ?string
    {
        return $this->honorificPrefix;
    }

    public function setHonorificPrefix(?string $honorificPrefix): void
    {
        $this->honorificPrefix = $honorificPrefix;
    }

    public function getHonorificSuffix(): ?string
    {
        return $this->honorificSuffix;
    }

    public function setHonorificSuffix(?string $honorificSuffix): void
    {
        $this->honorificSuffix = $honorificSuffix;
    }

    public function getFullName(): string
    {
        $fullName = $this->getHonorificPrefix().' ';
        $fullName .= $this->getGivenName().' '.$this->getAdditionalName().' '.$this->getFamilyName();
        $fullName .= ' '.$this->getHonorificSuffix();
        $fullName = preg_replace('!\s+!', ' ', $fullName);

        return trim(''.$fullName);
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): void
    {
        $this->nickname = $nickname;
    }

    public function getGenderOverride(): ?string
    {
        return $this->genderOverride;
    }

    public function setGenderOverride(?string $genderOverride): void
    {
        $this->genderOverride = in_array($genderOverride, [ContactInterface::GENDER_MALE, ContactInterface::GENDER_FEMALE], true)
            ? $genderOverride : null;
    }

    public function getSalutationName(): ?string
    {
        if (empty($this->getGivenName())) {
            return $this->getFullName();
        }
        try {
            // Respect the gender override (if any) so the Czech vocative matches the person's
            // gender; null lets vokativ auto-detect from the name (original behaviour).
            $gender = $this->getGender();
            $isWoman = ContactInterface::GENDER_FEMALE === $gender ? true
                : (ContactInterface::GENDER_MALE === $gender ? false : null);
            $salutationName = new VokativName()->vokativ($this->getGivenName(), $isWoman, false);

            return ucfirst($salutationName);
        } catch (Exception) {
            return $this->getGivenName();
        }
    }

    public function getCzechSuffixA(): string
    {
        // Derived from getGender() → honours the override (byl/byl-a in mails).
        return ContactInterface::GENDER_FEMALE === $this->getGender() ? 'a' : '';
    }

    public function getGender(): string
    {
        if (null !== $this->genderOverride && '' !== $this->genderOverride) {
            return $this->genderOverride;
        }
        if (empty($this->getGivenName())) {
            return ContactInterface::GENDER_UNISEX;
        }
        try {
            return new VokativName()->isMale($this->getGivenName()) ? ContactInterface::GENDER_MALE
                : ContactInterface::GENDER_FEMALE;
        } catch (InvalidArgumentException) {
            return ContactInterface::GENDER_UNISEX;
        }
    }
}
