<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use ADCI\FullNameParser\Exception\NameParsingException;
use ADCI\FullNameParser\Parser as FullNameParser;
use Exception;
use Vokativ\Name as VokativName;
use function implode;
use function trim;

/**
 * Trait adds name field and __toString().
 *
 * Trait adds field *name* that contains name or title of entity.
 * Trait implements __toString() that returns that name.
 */
trait FullNameTrait
{
    /**
     * Nickname.
     *
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $nickname = null;

    /**
     * First (given) name.
     *
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $givenName = null;

    /**
     * Middle (additional) name.
     *
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $additionalName = null;

    /**
     * Last (family) name.
     *
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $familyName = null;

    /**
     * Prefix (title before name).
     *
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $honorificPrefix = null;

    /**
     * Suffix (title after name).
     *
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $honorificSuffix = null;

    public function setFullName(?string $name): void
    {
        if ($name) {
            $parser = new FullNameParser();
            try {
                $name = preg_replace('!\s+!', ' ', $name);
                $nameObject = $parser->parse($name);
                $this->setHonorificPrefix($nameObject->getAcademicTitle() ?? '');
                $this->setGivenName($nameObject->getFirstName() ?? '');
                $this->setAdditionalName($nameObject->getMiddleName() ?? '');
                $this->setFamilyName($nameObject->getLastName() ?? '');
                $this->setHonorificSuffix($nameObject->getSuffix() ?? '');
                $this->setNickname(implode($nameObject->getNicknames(), [', ']) ?? '');
            } catch (NameParsingException $e) {
                // Name not recognized.
            } finally {
                return;
            }
        }
    }

    public function getFullName(): string
    {
        $fullName = $this->getHonorificPrefix().' '.$this->getGivenName().' '.$this->getAdditionalName().' '.$this->getFamilyName().' '.$this->getHonorificSuffix();
        $fullName = trim($fullName);
        $fullName = preg_replace('!\s+!', ' ', $fullName);

        return $fullName ?? '';
    }

    public function getHonorificPrefix(): ?string
    {
        return $this->honorificPrefix;
    }

    public function setHonorificPrefix(?string $honorificPrefix): void
    {
        $this->honorificPrefix = $honorificPrefix;
    }

    public function getGivenName(): ?string
    {
        return $this->givenName;
    }

    public function setGivenName(?string $givenName): void
    {
        $this->givenName = $givenName;
    }

    public function getAdditionalName(): ?string
    {
        return $this->additionalName;
    }

    public function setAdditionalName(?string $additionalName): void
    {
        $this->additionalName = $additionalName;
    }

    public function getFamilyName(): ?string
    {
        return $this->familyName;
    }

    public function setFamilyName(?string $familyName): void
    {
        $this->familyName = $familyName;
    }

    public function getHonorificSuffix(): ?string
    {
        return $this->honorificSuffix;
    }

    public function setHonorificSuffix(?string $honorificSuffix): void
    {
        $this->honorificSuffix = $honorificSuffix;
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
        if (!$this->getGivenName()) {
            return null;
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
            $vokativName = new VokativName();

            return $vokativName->isMale($this->getGivenName()) ? '' : 'a';
        } catch (Exception $e) {
            return '';
        }
    }
}
