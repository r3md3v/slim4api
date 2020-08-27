<?php

namespace App\Action;

use App\Domain\User\Service\UserSearcher;
use App\Factory\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Logger;

/**
 * Action
 */
final class UserSearchAction
{

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var UserSearcher
     */
    private $userSearcher;

    /**
     * The constructor.
     *
     * @param UserSearcher $userSearcher The user searcher
     * @param Logger $logger the logger
     */
    public function __construct(UserSearcher $userSearcher, LoggerFactory $lf)
    {
        $this->userSearcher = $userSearcher;
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    /**
     * Invoke.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     * @param array $args The route arguments
     *
     * @return ResponseInterface The response
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface
    {

        // Collect input from the HTTP request
        $keyword = (string)$args['keyword'];
        $in = isset($_GET['in']) ? $_GET['in'] : -1;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $size = isset($_GET['size']) ? $_GET['size'] : 0;

        // Feed the logger
        $this->logger->debug("UserSearchAction:page: $page, size: $size, in: $in, keyword: $keyword");

        // Invoke the Domain with inputs and retain the result
        $userSearch = $this->userSearcher->getUserSearch($keyword, $in, $page, $size);

        // Transform the result into the JSON representation
        $result = [];
        foreach ($userSearch as $userData) {
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
        $response->getBody()->write((string)json_encode($result));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}