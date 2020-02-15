<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Interfaces;

use Knp\DoctrineBehaviors\Contract\Entity\BlameableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;

interface BasicEntityInterface extends IdInterface, TimestampableInterface, BlameableInterface
{
}
