<?php

namespace App\Action;

use App\Domain\User\Service\UserCreator;
use App\Factory\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Logger;

/**
 * @OA\Post(
 *     path="/api/users",
 *     summary="create user",
 *     description="create user according to posted data ",
 *     @OA\RequestBody(
 *         description="Client side search object",
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Success",
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Could Not Find Resource",
 *  )
 * )
 */

/**
 * Action.
 */
final class UserCreateAction
{
    /**
     * @var UserCreator
     */
    private $userCreator;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * The constructor.
     *
     * @param UserCreator   $userCreator The user creator
     * @param LoggerFactory $lf          The loggerFactory
     */
    public function __construct(UserCreator $userCreator, LoggerFactory $lf)
    {
        $this->userCreator = $userCreator;
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    /**
     * Invoke.
     *
     * @param ServerRequestInterface $request  The request
     * @param ResponseInterface      $response The response
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Collect input from the HTTP request
        $data = (array) $request->getParsedBody();

        // Invoke the Domain with inputs and retain the result
        $userId = $this->userCreator->createUser($data);

        // Feed the logger
        $this->logger->debug("UserCreateAction: id: {$userId}");

        // Transform the result into the JSON representation
        $result = [
            'user_id' => $userId,
        ];

        // Build the HTTP response
        $response->getBody()->write((string) json_encode($result));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}
