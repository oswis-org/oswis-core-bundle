<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Attachment;

use Doctrine\ORM\EntityManagerInterface;
use OswisOrg\OswisCoreBundle\Entity\MailAttachment\MailAttachment;
use Psr\Log\LoggerInterface;

/**
 * Úklid nahraných příloh, které nikdy nikam neodešly (dávka 3.5): autor soubor nahrál a pak ho odebral, zprávu
 * zahodil nebo smazal koncept. Smaže se soubor i záznam, jen když:
 * - je starší než zadaný počet dní (autor může pořád psát),
 * - nikdy neodešel odkazem (zveřejněný soubor může mít někdo v mailu — nemazat nikdy),
 * - žádný poskytovatel ({@see MailAttachmentUsageProviderInterface}) ho nehlásí (koncept, zpráva, odeslaný mail).
 *
 * Když kterýkoli poskytovatel selže, neuklízí se NIC — bez úplného seznamu použitých souborů by se mohlo smazat i
 * něco, co se používá.
 */
final readonly class MailAttachmentCleanup
{
    /** @param iterable<MailAttachmentUsageProviderInterface> $providers */
    public function __construct(
        private EntityManagerInterface $em,
        private MailAttachmentStore $store,
        private iterable $providers,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @return array{starych: int, smazano: int, bajtu: int, chyby: list<string>, soubory: list<string>}
     */
    public function cleanup(int $days, bool $dryRun): array
    {
        if ($days < 1) {
            throw new \InvalidArgumentException('Úklid příloh: nejméně 1 den (mladší soubory může autor právě používat).');
        }
        $pouzite = [];
        foreach ($this->providers as $provider) {
            foreach ($provider->usedAttachmentIds() as $id) {
                $pouzite[$id] = true;
            }
        }
        $hranice = new \DateTimeImmutable(sprintf('-%d days', $days));
        $kandidati = $this->em->createQueryBuilder()
            ->select('a')
            ->from(MailAttachment::class, 'a')
            ->where('a.createdAt < :hranice')
            ->andWhere('a.public = false')
            ->setParameter('hranice', $hranice)
            ->getQuery()
            ->getResult();
        $vysledek = ['starych' => 0, 'smazano' => 0, 'bajtu' => 0, 'chyby' => [], 'soubory' => []];
        foreach (is_array($kandidati) ? $kandidati : [] as $priloha) {
            if (!$priloha instanceof MailAttachment || isset($pouzite[$priloha->getId() ?? 0])) {
                continue;
            }
            ++$vysledek['starych'];
            $vysledek['soubory'][] = sprintf('#%d %s (%s, %s)', $priloha->getId() ?? 0, $priloha->getOriginalName(), $priloha->getSizeLabel(), $priloha->getCreatedAt()->format('j. n. Y'));
            if ($dryRun) {
                continue;
            }
            $cesta = $this->store->absolutePath($priloha);
            if (is_file($cesta) && !@unlink($cesta)) {
                $vysledek['chyby'][] = sprintf('#%d: soubor nejde smazat (%s)', $priloha->getId() ?? 0, $cesta);
                continue;
            }
            $vysledek['bajtu'] += $priloha->getSize();
            $this->em->remove($priloha);
            ++$vysledek['smazano'];
        }
        if (!$dryRun) {
            $this->em->flush();
            $this->logger->info(sprintf('Úklid příloh: smazáno %d nepoužitých souborů (%s), chyb %d.', $vysledek['smazano'], MailAttachment::velikost($vysledek['bajtu']), count($vysledek['chyby'])));
        }

        return $vysledek;
    }
}
