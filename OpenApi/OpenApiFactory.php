<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\OpenApi;
use ArrayObject;
use Symfony\Component\HttpFoundation\Response;

final class OpenApiFactory implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);
        $schemas = $openApi->getComponents()->getSchemas() ?? new ArrayObject([]);
        $schemas['Token'] = [
            'type'       => 'object',
            'properties' => [
                'token' => [
                    'type'     => 'string',
                    'readOnly' => true,
                ],
            ],
        ];
        $schemas['Credentials'] = [
            'type'       => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                ],
                'password' => [
                    'type' => 'string',
                ],
            ],
        ];
        $openApi = $openApi->withComponents($openApi->getComponents()->withSchemas($schemas));
        $responses = [
            Response::HTTP_OK => [
                'description' => 'Get JWT token',
                'content'     => [
                    'application/json' => [
                        'schema' => [
                            'properties' => [
                                'token'         => ['type' => 'string'],
                                'refresh_token' => ['type' => 'string'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $requestBody = new RequestBody('Create new JWT Token', new ArrayObject([
            'application/json' => [
                'schema' => [
                    '$ref' => '#/components/schemas/Credentials',
                ],
            ],
        ]));
        $operation = new Operation('postCredentialsItem', ['AppUser API Login'], $responses,
            'Get JWT token for API authentication.', 'Get JWT token for API authentication.', null, [], $requestBody,);
        $openApi->getPaths()->addPath('/api/login', new PathItem(null, null, null, null, null, $operation));

        return $openApi;
    }
}
