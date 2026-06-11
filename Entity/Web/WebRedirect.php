<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Entity\Web;

use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Nameable;
use OswisOrg\OswisCoreBundle\Interfaces\Common\NameableInterface;
use OswisOrg\OswisCoreBundle\Repository\Web\WebRedirectRepository;
use OswisOrg\OswisCoreBundle\Traits\Common\DeletedTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\NameableTrait;

/**
 * Admin-managed short link: public /redirect/{slug} (alias /r/{slug}) 302-redirects to $targetUrl.
 *
 * Replaces yearly hardcoded redirect controller methods (feedback forms, FB events, QR posters):
 * a new year/campaign is a DATA row added in the web admin, not a code change + deploy. The target
 * may be swapped at any time (typo in a form link, moved event) without touching already-sent
 * e-mails or printed QR codes. Soft-deleted/unknown slugs render the standard 404.
 *
 * Lives in CORE (not the optional web bundle) so that ANY bundle's content — calendar e-mail
 * templates above all — may safely link the route in every deployment.
 *
 * Targets are entered exclusively by ROLE_ADMIN (curated list), so the public endpoint is not an
 * open-redirect vector; the http(s)-only validation in the form is defense-in-depth.
 */
#[Entity(repositoryClass: WebRedirectRepository::class)]
#[Table(name: 'core_web_redirect')]
#[UniqueConstraint(name: 'UNIQ_CORE_WEB_REDIRECT_SLUG', columns: ['slug'])]
class WebRedirect implements NameableInterface
{
    use NameableTrait;
    use DeletedTrait;

    /**
     * Target the public route redirects to: absolute http(s) URL, or a site-relative path
     * starting with a single "/" (internal pages; "//host" is rejected by the form as it would
     * escape the site). Validation lives in {@see \OswisOrg\OswisCoreBundle\Form\WebAdmin\WebRedirectType}.
     */
    #[Column(type: 'string', length: 2048, nullable: false)]
    protected string $targetUrl = '';

    /** Lightweight usage counter (QR posters, campaigns); detailed logs live in the web server. */
    #[Column(type: 'integer', nullable: false)]
    protected int $hitCount = 0;

    #[Column(type: 'datetime', nullable: true)]
    protected ?DateTime $lastHitAt = null;

    public function __construct(?Nameable $nameable = null, string $targetUrl = '')
    {
        $this->setFieldsFromNameable($nameable);
        $this->setTargetUrl($targetUrl);
    }

    public function getTargetUrl(): string
    {
        return $this->targetUrl;
    }

    public function setTargetUrl(string $targetUrl): void
    {
        $this->targetUrl = trim($targetUrl);
    }

    public function getHitCount(): int
    {
        return $this->hitCount;
    }

    public function getLastHitAt(): ?DateTime
    {
        return $this->lastHitAt;
    }

    /** Record one public-route usage (caller flushes). */
    public function registerHit(): void
    {
        ++$this->hitCount;
        $this->lastHitAt = new DateTime();
    }
}
