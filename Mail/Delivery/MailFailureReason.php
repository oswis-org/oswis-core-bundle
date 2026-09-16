<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Delivery;

/**
 * Přeloží hlášku poštovního serveru do věty, se kterou tým něco udělá.
 *
 * Proč: v historii se dosud ukazovalo to, co vrátil SMTP („Expected response code 250 but got
 * code 550, with message 5.1.1 User unknown"). Nejčastější důvod je přitom překlep v adrese —
 * 200 z 218 selhání v letech 2020–2026 — a to je věc, kterou tým umí opravit, když se to dozví.
 *
 * Původní hlášku nezahazujeme; zůstává v záznamu a v historii jde ukázat vedle.
 */
final class MailFailureReason
{
    /** @var array<string, list<string>> věta → části hlášky, podle kterých se pozná */
    private const array PODLE_HLASKY = [
        'Adresa neexistuje — zkontroluj, jestli v ní není překlep.' => [
            '5.1.1', 'user unknown', 'does not exist', 'recipient address rejected', 'no such user',
            'unrouteable address', 'mailbox unavailable',
        ],
        'Schránka příjemce je plná.' => ['5.2.2', 'mailbox full', 'over quota', 'quota exceeded'],
        'Adresa je zapsaná v nesprávném tvaru.' => ['rfc 2822', 'rfc 5322', 'does not comply', 'invalid address'],
        'Server příjemce zprávu odmítl jako nevyžádanou.' => ['spam', 'blocked', 'blacklist', 'reputation'],
        'Poštovní server je nedostupný — zkusí se to znovu.' => [
            'connection could not be established', 'connection refused', 'timed out', 'timeout',
            'could not connect', 'network is unreachable',
        ],
        'Chyba v šabloně e-mailu — dej vědět správci systému.' => ['twig', 'template', 'unexpected token'],
        'Zprávu se nepodařilo uložit do databáze — dej vědět správci systému.' => ['sqlstate', 'entitymanager'],
    ];

    /** Věta pro tým, nebo null, když hlášce nerozumíme (pak se ukáže původní text). */
    public static function forMessage(?string $statusMessage): ?string
    {
        if (null === $statusMessage || '' === trim($statusMessage)) {
            return null;
        }
        $hlaska = mb_strtolower($statusMessage);
        foreach (self::PODLE_HLASKY as $veta => $casti) {
            foreach ($casti as $cast) {
                if (str_contains($hlaska, $cast)) {
                    return $veta;
                }
            }
        }

        return null;
    }
}
