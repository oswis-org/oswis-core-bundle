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
}
