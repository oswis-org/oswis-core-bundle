<?php /** @noinspection MethodShouldBeFinalInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds e-mail field.
 */
trait EmailTrait
{
    /**
     * E-mail address.
     *
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(name="email",type="string", unique=false, length=60, nullable=true)
     * @Symfony\Component\Validator\Constraints\NotBlank()
     * @Symfony\Component\Validator\Constraints\Email(
     *     message = "Zadaná adresa ({{ value }}) není platná.",
     *     mode = "strict"
     * )
     */
    protected ?string $email = null;

    /**
     * Get e-mail.
     */
    public function getEmail(): string
    {
        return $this->email ?? '';
    }

    /**
     * Set e-mail.
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
}
