<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnusedAliasInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OswisOrg\OswisCoreBundle\Interfaces\Common\BasicInterface;

/**
 * Trait adds **common basic fields** for entities (id, created/updatedDateTime...).
 *
 * Trait adds **common basic fields**:
 * * id
 * * createdDateTime, updatedDateTime
 * * createdAuthor, updatedAuthor
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
trait BasicTrait
{
    use IdTrait;
    use TimestampableTrait;
    use BlameableTrait;

    public static function sortCollection(?Collection $items, bool $reversed = false): Collection
    {
        $itemsArray = ($items ?? new ArrayCollection())->toArray();
        self::sortArray($itemsArray);
        if ($reversed) {
            $itemsArray = array_reverse($itemsArray);
        }

        return new ArrayCollection($itemsArray);
    }

    public static function sortArray(array &$items): void
    {
        usort($items, fn(BasicInterface $item1, BasicInterface $item2) => self::compare($item1, $item2));
    }

    public static function compare(BasicInterface $item1, BasicInterface $item2): int
    {
        return 0 === ($result = $item1->getCreatedAt() <=> $item2->getCreatedAt()) ? $item1->getId() <=> $item2->getId() : $result;
    }
}
