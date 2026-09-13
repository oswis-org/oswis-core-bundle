<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Validation;

/** Výsledek kontroly: chyba = mail nejde uložit/odeslat, varování = jde, ale ať to autor ví. */
final class MailValidationResult
{
    /** @var list<MailProblem> */
    private array $problems = [];

    public function error(string $message): void
    {
        $this->problems[] = new MailProblem(MailProblem::ERROR, $message);
    }

    public function warning(string $message): void
    {
        $this->problems[] = new MailProblem(MailProblem::WARNING, $message);
    }

    public function hasErrors(): bool
    {
        return [] !== $this->errors();
    }

    /** @return list<MailProblem> */
    public function errors(): array
    {
        return array_values(array_filter($this->problems, static fn (MailProblem $p): bool => MailProblem::ERROR === $p->severity));
    }

    /** @return list<MailProblem> */
    public function warnings(): array
    {
        return array_values(array_filter($this->problems, static fn (MailProblem $p): bool => MailProblem::WARNING === $p->severity));
    }

    /** @return list<MailProblem> */
    public function all(): array
    {
        return $this->problems;
    }

    /**
     * Otisk varování — potvrzení „odeslat i tak" platí jen pro TATO varování; po úpravě textu
     * s jinými varováními se ptáme znovu.
     */
    public function warningsFingerprint(): string
    {
        return hash('xxh3', implode("\n", array_map(static fn (MailProblem $p): string => $p->message, $this->warnings())));
    }

    /** Smí se zpráva odeslat? Bez chyb a buď bez varování, nebo autor potvrdil právě tato varování. */
    public function isConfirmedBy(?string $fingerprint): bool
    {
        return !$this->hasErrors() && ([] === $this->warnings() || $fingerprint === $this->warningsFingerprint());
    }
}
