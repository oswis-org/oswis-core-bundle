<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Delivery;

use OswisOrg\OswisCoreBundle\Entity\AbstractClass\AbstractMail;

/**
 * Kdy se smí automaticky znovu poslat doručení, které SMTP odmítlo (stav FAILED).
 *
 * PROČ: 26. 9. 2026 hosting zablokoval odesílací účet uprostřed rozesílky; 100 doručení skončilo
 * FAILED a kontrola klíče jedinečnosti je už nikdy nepustila znovu — přitom návrh stavů opakování
 * po odmítnutí výslovně dovoluje ({@see \OswisOrg\OswisCoreBundle\Enum\Mail\MailDeliveryStatus::allowsAutomaticRetry()}).
 * Navíc stála ve frontě první, takže zablokovala i všechny další příjemce.
 *
 * Opakuje se TENTÝŽ záznam (FAILED → SENDING, žádný nový řádek → žádné zdvojení), nejvýš
 * {@see MAX_POKUSU}× a nejdřív {@see PRODLEVA_MINUT} min po posledním selhání — při výpadku SMTP
 * tak cron nebuší do serveru každou minutu (hosting to bere jako útok a účet zablokuje).
 * SENDING (nejisté: nevíme, jestli odešlo) se automaticky NIKDY neopakuje — rozhodne člověk.
 */
final class MailRetryPolicy
{
    public const int MAX_POKUSU = 5;
    public const int PRODLEVA_MINUT = 30;

    public static function smiZnovu(AbstractMail $zaznam, ?\DateTimeInterface $ted = null): bool
    {
        if (!$zaznam->getStatus()->allowsAutomaticRetry() || $zaznam->getAttemptCount() >= self::MAX_POKUSU) {
            return false;
        }
        $posledni = $zaznam->getUpdatedAt();

        return null === $posledni || $posledni <= self::hranice($ted);
    }

    /** Selhání novější než tahle chvíle ještě čeká na další pokus. */
    public static function hranice(?\DateTimeInterface $ted = null): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromInterface($ted ?? new \DateTimeImmutable())->modify(sprintf('-%d minutes', self::PRODLEVA_MINUT));
    }
}
