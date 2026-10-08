<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Attachment;

use Doctrine\DBAL\Connection;

/** ID příloh ze sloupců JSON `[{id, …}]` — sdílené čtení pro poskytovatele {@see MailAttachmentUsageProviderInterface}. */
final class MailAttachmentIds
{
    /**
     * @param list<string> $tables tabulky se sloupcem `attachments`
     *
     * @return list<int>
     */
    public static function fromTables(Connection $connection, array $tables, string $column = 'attachments'): array
    {
        $ids = [];
        foreach ($tables as $table) {
            foreach ($connection->fetchFirstColumn(sprintf('SELECT %1$s FROM %2$s WHERE %1$s IS NOT NULL', $column, $table)) as $json) {
                $data = is_string($json) ? json_decode($json, true) : null;
                foreach (is_array($data) ? $data : [] as $polozka) {
                    if (is_array($polozka) && is_numeric($polozka['id'] ?? null)) {
                        $ids[(int) $polozka['id']] = true;
                    }
                }
            }
        }

        return array_keys($ids);
    }
}
