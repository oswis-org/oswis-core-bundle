<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\AddressBook;

use ADCI\FullNameParser\Exception\NameParsingException;
use ADCI\FullNameParser\Name;
use ADCI\FullNameParser\Parser as FullNameParser;
use Exception;
use InvalidArgumentException;
use OswisOrg\OswisCoreBundle\Interfaces\AddressBook\ContactInterface;
use OswisOrg\OswisCoreBundle\Traits\Common\NameableTrait;
use Vokativ\Name as VokativName;

use function trim;

/**
 * Trait adds name field and __toString().
 *
 * Trait adds field *name* that contains name or title of entity.
 * Trait implements __toString() that returns that name.
 */
trait NameablePersonTrait
{
    use NameableTrait {
        getSortableName as traitSortableName;
        setName as traitName;
        updateName as traitUpdateName;
    }

    /**
     * Nickname.
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?string $nickname = null;

    /**
     * First (given) name.
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?string $givenName = null;

    /**
     * Middle (additional) name.
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?string $additionalName = null;

    /**
     * Last (family) name.
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?string $familyName = null;

    /**
     * Prefix (title before name).
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?string $honorificPrefix = null;

    /**
     * Suffix (title after name).
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?string $honorificSuffix = null;

    /**
     * Full name of person.
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?string $name = null;

    public function setName(?string $fullName): ?string
    {
        return $this->setFullName($fullName);
    }

    public function setFullName(?string $name): ?string
    {
        $parser = new FullNameParser();
        try {
            $name = preg_replace('!\s+!', ' ', ''.$name);
            $nameObject = $parser->parse(trim(''.$name));
            if ($nameObject instanceof Name) {
                $this->setHonorificPrefix($nameObject->getAcademicTitle() ?? '');
                $this->setGivenName($nameObject->getFirstName() ?? '');
                $this->setAdditionalName($nameObject->getMiddleName() ?? '');
                $this->setFamilyName($nameObject->getLastName() ?? '');
                $this->setHonorificSuffix($nameObject->getSuffix() ?? '');
                $this->setNickname($nameObject->getNicknames() ?? '');
            }
        } catch (NameParsingException $e) {
            // Name not recognized. TODO: Do some magic. Or maybe throw some exception.
        }

        return $this->updateName();
    }

    public function updateName(): ?string
    {
        $this->setSortableName($this->getSortableName());

        return $this->name = $this->getFullName();
    }

    public function getSortableName(): string
    {
        return $this->getFamilyName().' '.$this->getAdditionalName().' '.$this->getGivenName().' '.$this->getHonorificPrefix().' '.$this->getHonorificSuffix();
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

        return trim(''.$fullName) ?? '';
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): void
    {
        $this->nickname = $nickname;
    }

    public function getSalutationName(): ?string
    {
        if (empty($this->getGivenName())) {
            return $this->getFullName();
        }
        try {
            $vokativName = new VokativName();
            $salutationName = $vokativName->vokativ($this->getGivenName(), null, false);

            return ucfirst($salutationName);
        } catch (Exception $e) {
            return $this->getGivenName();
        }
    }

    public function getCzechSuffixA(): string
    {
        try {
            return (new VokativName())->isMale(''.$this->getGivenName()) ? '' : 'a';
        } catch (Exception $e) {
            return '';
        }
    }

    public function getGender(): string
    {
        if (empty($this->getGivenName())) {
            return ContactInterface::GENDER_UNISEX;
        }
        try {
            return (new VokativName())->isMale($this->getGivenName()) ? ContactInterface::GENDER_MALE : ContactInterface::GENDER_FEMALE;
        } catch (InvalidArgumentException $e) {
            return ContactInterface::GENDER_UNISEX;
        }
    }
}
