<?php
/**
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Interfaces;

use Knp\DoctrineBehaviors\Contract\Entity\BlameableInterface;

interface BasicEntityInterface extends IdInterface, TimestampableInterface, BlameableInterface
{
}
