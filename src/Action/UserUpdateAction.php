<?php

namespace App\Action;

use App\Domain\User\Service\UserUpdator;
use App\Factory\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Logger;

/**
 * @OA\Post(
 *     path="/api/users",
 *     summary="update user",
 *     description="update user according to posted data ",
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
final class UserUpdateAction
{
    /**
     * @var UserUpdator
     */
    private $userUpdator;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * The constructor.
     *
     * @param UserUpdator   $userUpdator The user updator
     * @param LoggerFactory $lf          The loggerFactory
     */
    public function __construct(UserUpdator $userUpdator, LoggerFactory $lf)
    {
        $this->userUpdator = $userUpdator;
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
        $data = (array) $request->getParsedBody();

        // Invoke the Domain with inputs and retain the result
        $this->userUpdator->updateUser($userId, $data);

        // Feed the logger
        $this->logger->debug("UserUpdateAction: id: {$userId}");

        // Transform the result into the JSON representation
        $result = [
            'user_id' => $userId,
        ];

        // Build the HTTP response
        $response->getBody()->write((string) json_encode($result));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}
