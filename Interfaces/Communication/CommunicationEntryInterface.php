<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Interfaces\Communication;

use DateTimeInterface;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Enum\Communication\CommunicationChannel;
use OswisOrg\OswisCoreBundle\Enum\Communication\CommunicationDirection;

/**
 * Polymorphic contract for items appearing in a participant's communication timeline.
 *
 * Implementations live in calendar-bundle (ParticipantMail today;
 * ParticipantIncomingMail, ParticipantPhoneCall, ParticipantChatMessage in
 * later phases). The aggregator service builds a chronological feed by
 * pulling from each implementation's repository and merging by getOccurredAt().
 */
interface CommunicationEntryInterface
{
    public function getId(): ?int;

    /**
     * @return object|null  Participant (avoiding circular dep — core-bundle doesn't know calendar-bundle types).
     */
    public function getParticipant(): ?object;

    public function getOccurredAt(): ?DateTimeInterface;

    public function getDirection(): CommunicationDirection;

    public function getChannel(): CommunicationChannel;

    public function getSubject(): ?string;

    public function getSummary(): ?string;

    public function getBody(): ?string;

    public function getBodyHtml(): ?string;

    public function isPublicForParticipant(): bool;

    public function getMessageId(): ?string;

    public function getInReplyTo(): ?string;

    public function getThreadKey(): ?string;

    public function getAuthorAppUser(): ?AppUser;
}
