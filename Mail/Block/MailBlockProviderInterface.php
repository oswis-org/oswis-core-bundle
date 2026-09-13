<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Block;

/** Poskytovatel vložených bloků (tag `oswis.mail_block_provider`). */
interface MailBlockProviderInterface
{
    /** @return iterable<MailBlock> */
    public function getBlocks(): iterable;
}
