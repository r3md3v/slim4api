<?php

namespace App\Action;

use App\Domain\User\Service\UserDeletor;
use App\Factory\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Logger;

/**
 * Action.
 */
final class UserDeleteAction
{
    /**
     * @var UserDeletor
     */
    private $userDeletor;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * The constructor.
     *
     * @param UserDeletor   $userDeletor The user deletor
     * @param LoggerFactory $lf          The loggerFactory
     */
    public function __construct(UserDeletor $userDeletor, LoggerFactory $lf)
    {
        $this->userDeletor = $userDeletor;
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    /**
     * Invoke.
     *
     * @param ServerRequestInterface $request  The request
     * @param ResponseInterface      $response The response
     * @param array                  $args     The route arguments
     *
     * @return ResponseInterface The response
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface {
        // Collect input from the HTTP request
        $userId = (int) $args['id'];

        // Feed the logger
        $this->logger->debug("UserDeleteAction: id: {$userId}");

        // Invoke the Domain with inputs and retain the result
        $result = $this->userDeletor->deleteUser($userId);

        // Transform the result into the JSON representation
        $result = [
            'user_id' => $result,
        ];

        // Build the HTTP response
        $response->getBody()->write((string) json_encode($result));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}
