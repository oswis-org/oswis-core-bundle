<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Delivery;

use InvalidArgumentException;
use Stringable;

/**
 * Identifies WHAT an automatic send is for, so the database can refuse a second one.
 *
 * Code-level guards have failed here twice: 17 duplicate payment confirmations on
 * 2026-08-21 (the cursor write failed after sending) and the duplicated payment import
 * on 2026-08-16, whose analysis ended with "only a unique index protects against a race".
 * Every automatic delivery therefore carries this key in a unique column; a manual send
 * or a resend leaves it NULL, and NULL may repeat.
 *
 * Shape: `purpose:part[:part…]`, e.g. `automail:infomail-2026:3421:1890`,
 * `payment:5590:1890`, `bulk:12:3421:1890`.
 */
final readonly class DeliveryKey implements Stringable
{
    /** Column length in {@see \OswisOrg\OswisCoreBundle\Entity\AbstractClass\AbstractMail}. */
    public const int MAX_LENGTH = 191;

    private function __construct(public string $value)
    {
    }

    /**
     * @param string          $purpose what kind of automatic send this is (`automail`, `payment`, `bulk`)
     * @param int|string|null ...$parts what makes it unique (ids of the subject, message, recipient)
     */
    public static function of(string $purpose, int|string|null ...$parts): self
    {
        $segments = [self::segment($purpose)];
        foreach ($parts as $part) {
            if (null === $part) {
                throw new InvalidArgumentException('Klíč jedinečnosti nesmí mít prázdnou část.');
            }
            $segments[] = self::segment((string) $part);
        }
        if (1 === count($segments)) {
            throw new InvalidArgumentException('Klíč jedinečnosti potřebuje kromě účelu aspoň jednu část.');
        }
        $value = implode(':', $segments);
        if (self::MAX_LENGTH < strlen($value)) {
            throw new InvalidArgumentException('Klíč jedinečnosti je delší než '.self::MAX_LENGTH.' znaků.');
        }

        return new self($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /** Segments stay printable and searchable: letters, digits and `. _ -`. */
    private static function segment(string $raw): string
    {
        $trimmed = trim($raw);
        if ('' === $trimmed) {
            throw new InvalidArgumentException('Klíč jedinečnosti nesmí mít prázdnou část.');
        }
        if (1 !== preg_match('~^[A-Za-z0-9._-]+$~', $trimmed)) {
            throw new InvalidArgumentException(
                sprintf('Část klíče „%s" smí mít jen písmena, číslice a . _ -', $trimmed),
            );
        }

        return $trimmed;
    }
}
