<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\AddressBook;

/**
 * Trait adds e-mail field.
 */
trait EmailTrait
{
    /**
     * E-mail address.
     * @Doctrine\ORM\Mapping\Column(name="email",type="string", unique=false, length=60, nullable=true)
     * @Symfony\Component\Validator\Constraints\NotBlank()
     * @Symfony\Component\Validator\Constraints\Email(
     *     message = "Zadaná adresa ({{ value }}) není platná.",
     *     mode = "strict"
     * )
     */
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
