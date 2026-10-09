<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use OswisOrg\OswisCoreBundle\Entity\TwigTemplate\TwigTemplate;
use OswisOrg\OswisCoreBundle\Entity\TwigTemplate\TwigTemplateVersion;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Historie verzí šablon (dávka 4, spec §6.3): při KAŽDÉM uložení šablony, které změnilo obsah (název, předmět, text,
 * rodič, druh, slug), vznikne verze — ať šablonu uložila administrace, API, nebo import z konzole. Verze se zakládá
 * ve fázi `onFlush`, tedy ve stejném uložení jako šablona (v `postFlush` se podle dokumentace Doctrine `flush()`
 * volat bezpečně nedá). Uložení beze změny obsahu verzi nezaloží.
 */
final readonly class TwigTemplateVersionRecorder
{
    public function __construct(private ?TokenStorageInterface $tokenStorage = null)
    {
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();
        $sablony = [];
        foreach ([...$uow->getScheduledEntityInsertions(), ...$uow->getScheduledEntityUpdates()] as $entita) {
            if ($entita instanceof TwigTemplate) {
                $sablony[spl_object_id($entita)] = $entita;
            }
        }
        if ([] === $sablony) {
            return;
        }
        $meta = $em->getClassMetadata(TwigTemplateVersion::class);
        $connection = $em->getConnection();
        foreach ($sablony as $sablona) {
            $posledni = null === $sablona->getId() ? false : $connection->fetchAssociative(
                'SELECT number, name, subject, text_value, parent, kind, slug FROM core_twig_template_version WHERE template_id = ? ORDER BY number DESC LIMIT 1',
                [$sablona->getId()],
            );
            $otisk = TwigTemplateVersion::otisk($sablona->getName(), $sablona->getSubject(), $sablona->getTextValue(), $sablona->getRegularTemplateName(), $sablona->getKind(), $sablona->getSlug());
            if (is_array($posledni) && $otisk === TwigTemplateVersion::otisk(
                self::text($posledni['name']), self::text($posledni['subject']), self::text($posledni['text_value']),
                self::text($posledni['parent']), self::text($posledni['kind']), self::text($posledni['slug']),
            )) {
                continue;
            }
            $cislo = is_array($posledni) && is_numeric($posledni['number']) ? (int) $posledni['number'] + 1 : 1;
            $verze = new TwigTemplateVersion($sablona, $cislo, $this->autor(), $sablona->getPoznamkaKVerzi());
            $em->persist($verze);
            $uow->computeChangeSet($meta, $verze);
            $sablona->setPoznamkaKVerzi(null);
        }
    }

    private function autor(): string
    {
        $uzivatel = $this->tokenStorage?->getToken()?->getUser();

        return null !== $uzivatel ? $uzivatel->getUserIdentifier() : 'systém (konzole)';
    }

    private static function text(mixed $hodnota): ?string
    {
        return is_string($hodnota) ? $hodnota : null;
    }
}
