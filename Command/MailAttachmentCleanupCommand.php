<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Command;

use OswisOrg\OswisCoreBundle\Entity\MailAttachment\MailAttachment;
use OswisOrg\OswisCoreBundle\Mail\Attachment\MailAttachmentCleanup;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/** Úklid nahraných a nikdy neodeslaných příloh ({@see MailAttachmentCleanup}). `--dry-run` jen vypíše, co by smazal. */
#[AsCommand(name: 'oswis:mail:attachments:cleanup', description: 'Smaže nahrané přílohy, které nikam neodešly a nikdo je nepoužívá.')]
final class MailAttachmentCleanupCommand extends Command
{
    public function __construct(private readonly MailAttachmentCleanup $cleanup)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('days', null, InputOption::VALUE_REQUIRED, 'Smazat jen soubory starší než tolik dní', '30')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Nic nemazat, jen vypsat');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dny = $input->getOption('days');
        $dryRun = (bool) $input->getOption('dry-run');
        $vysledek = $this->cleanup->cleanup(is_numeric($dny) ? (int) $dny : 30, $dryRun);
        if ($io->isVerbose() || $dryRun) {
            $io->listing($vysledek['soubory']);
        }
        foreach ($vysledek['chyby'] as $chyba) {
            $io->error($chyba);
        }
        $io->writeln($dryRun
            ? sprintf('DRY-RUN: nepoužitých souborů ke smazání %d.', $vysledek['starych'])
            : sprintf('Smazáno %d nepoužitých souborů (%s).', $vysledek['smazano'], MailAttachment::velikost($vysledek['bajtu'])));

        return [] === $vysledek['chyby'] ? Command::SUCCESS : Command::FAILURE;
    }
}
