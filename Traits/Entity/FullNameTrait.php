<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use ADCI\FullNameParser\Exception\NameParsingException;
use ADCI\FullNameParser\Parser as FullNameParser;
use Exception;
use Vokativ\Name as VokativName;
use function implode;
use function trim;

/**
 * Trait adds name field and __toString()
 *
 * Trait adds field *name* that contains name or title of entity.
 * Trait implements __toString() that returns that name.
 */
trait FullNameTrait
{

    /**
     * Nickname.
     * @var string|null $nickname
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $nickname;

    /**
     * First (given) name.
     * @var string|null $givenName
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $givenName;

    /**
     * Middle (additional) name.
     * @var string|null $additionalName
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $additionalName;

    /**
     * Last (family) name.
     * @var string|null $familyName
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $familyName;

    /**
     * Prefix (title before name).
     * @var string|null $honorificPrefix
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $honorificPrefix;

    /**
     * Suffix (title after name).
     * @var string|null $honorificSuffix
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $honorificSuffix;

    final public function setFullName(?string $name): void
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

    /**
     * @return null|string
     */
    final public function getFullName(): string
    {
        $fullName = $this->getHonorificPrefix().' '.$this->getGivenName().' '.$this->getAdditionalName().' '.$this->getFamilyName().' '.$this->getHonorificSuffix();
        $fullName = trim($fullName);
        $fullName = preg_replace('!\s+!', ' ', $fullName);

        return $fullName ?? '';
    }

    /**
     * @return string|null
     */
    final public function getHonorificPrefix(): ?string
    {
        return $this->honorificPrefix;
    }

    /**
     * @param string|null $honorificPrefix
     */
    final public function setHonorificPrefix(?string $honorificPrefix): void
    {
        $this->honorificPrefix = $honorificPrefix;
    }

    /**
     * @return string|null
     */
    final public function getGivenName(): ?string
    {
        return $this->givenName;
    }

    /**
     * @param string|null $givenName
     */
    final public function setGivenName(?string $givenName): void
    {
        $this->givenName = $givenName;
    }

    /**
     * @return string|null
     */
    final public function getAdditionalName(): ?string
    {
        return $this->additionalName;
    }

    /**
     * @param string|null $additionalName
     */
    final public function setAdditionalName(?string $additionalName): void
    {
        $this->additionalName = $additionalName;
    }

    /**
     * @return string|null
     */
    final public function getFamilyName(): ?string
    {
        return $this->familyName;
    }

    /**
     * @param string|null $familyName
     */
    final public function setFamilyName(?string $familyName): void
    {
        $this->familyName = $familyName;
    }

    /**
     * @return string|null
     */
    final public function getHonorificSuffix(): ?string
    {
        return $this->honorificSuffix;
    }

    /**
     * @param string|null $honorificSuffix
     */
    final public function setHonorificSuffix(?string $honorificSuffix): void
    {
        $this->honorificSuffix = $honorificSuffix;
    }

    /**
     * @return string|null
     */
    final public function getNickname(): ?string
    {
        return $this->nickname;
    }

    /**
     * @param string|null $nickname
     */
    final public function setNickname(?string $nickname): void
    {
        $this->nickname = $nickname;
    }

    /**
     * @return string|null
     */
    final public function getSalutationName(): ?string
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

    /**
     * @return string
     */
    final public function getCzechSuffixA(): string
    {
        try {
            $vokativName = new VokativName();

            return $vokativName->isMale($this->getGivenName()) ? '' : 'a';
        } catch (Exception $e) {
            return '';
        }
    }
}
