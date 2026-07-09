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
        $nameObject = null;
        // A single token carries no surname, so the parser can only damage it: it either throws
        // NameParsingException ("Sonička", "Nguyen", a nickname) or splits on a hyphen and inserts
        // a space ("Anna-Líza" → "Anna -Líza", which grows another space on every re-save).
        // Multi-token names with hyphens ("Marie Nováková-Svobodová") parse correctly.
        if (str_contains($name, ' ')) {
            try {
                $nameObject = new FullNameParser()->parse($name);
            } catch (NameParsingException) {
                $nameObject = null;
            }
        }
        if ($nameObject instanceof Name) {
            $this->setHonorificPrefix($nameObject->getAcademicTitle());
            $this->setGivenName($nameObject->getFirstName());
            $this->setAdditionalName($nameObject->getMiddleName());
            $this->setFamilyName($nameObject->getLastName());
            $this->setHonorificSuffix($nameObject->getSuffix());
            $this->setNickname($nameObject->getNicknames());
            if ('' !== $this->getFullName() && !self::onlyWhitespaceDiffers($name, $this->getFullName())) {
                return $this->updateName();
            }
        }
        // Parser skipped, refused the input, or produced nothing usable. Keeping the raw name is
        // what stops a non-empty input from being silently stored as ''. Swallowing the exception
        // and letting updateName() rebuild `name` from the resulting nulls is what produced the
        // "half-saved registration" rows on production (2022 through 2026): e-mail and phone
        // stored, name gone.
        $this->setRawNameParts($name);

        return $this->updateName();
    }

    /**
     * The parser may reorder tokens ("Novák, Jan" → "Jan Novák"), but it must never invent or drop a
     * space. When the only difference is whitespace, it split something it shouldn't have — typically
     * on a hyphen — and re-saving would keep adding spaces ("Anna -Líza" → "Anna - Líza" → …). Keeping
     * the raw input in that case makes setName(getName()) idempotent, so an already-damaged legacy row
     * cannot decay further before it gets repaired.
     */
    private static function onlyWhitespaceDiffers(string $rawName, string $parsedName): bool
    {
        return $rawName !== $parsedName
               && str_replace(' ', '', $rawName) === str_replace(' ', '', $parsedName);
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
