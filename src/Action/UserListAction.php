<?php

namespace App\Action;

use App\Domain\User\Service\UserLister;
use App\Factory\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Logger;

/**
 * Action
 */
final class UserListAction
{

    /**
     * @var UserLister
     */
    private $userLister;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * The constructor.
     *
     * @param UserLister    $userLister The user lister
     * @param LoggerFactory $lf         The loggerFactory
     */
    public function __construct(UserLister $userLister, LoggerFactory $lf)
    {
        $this->userLister = $userLister;
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    /**
     * Invoke.
     *
     * @param ServerRequestInterface $request  The request
     * @param ResponseInterface      $response The response
     * @param array                  $args     The route arguments // unused for List
     *
     * @return ResponseInterface The response
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface {

        // Collect input from the HTTP request
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $size = isset($_GET['size']) ? $_GET['size'] : 0;

        // Feed the logger
        $this->logger->debug("UserListAction: page: {$page}, size: {$size}");

        // Invoke the Domain with inputs and retain the result
        $userList = $this->userLister->getUserList($page, $size);

        // Transform the result into the JSON representation
        $result = [];
        foreach ($userList as $userData) {
            array_push($result, [
                'user_id' => $userData->id,
                'username' => $userData->username,
                'first_name' => $userData->firstName,
                'last_name' => $userData->lastName,
                'email' => $userData->email,
                'profile' => $userData->profile,
            ]);
        }

        // Build the HTTP response
        $response->getBody()->write((string) json_encode($result));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
