<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Export;

use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;

final class ExportResponseFactory
{
    public function toResponse(ExportResult $result): Response
    {
        $response = new Response($result->content);
        $response->headers->set('Content-Type', $result->mimeType);
        $disposition = HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $result->filename);
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
