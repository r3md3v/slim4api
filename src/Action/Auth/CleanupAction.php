<?php

namespace App\Action\Auth;

use App\Domain\Login\Service\LoginReader;
use App\Domain\Login\Service\TokenManager;
use App\Factory\LoggerFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

final class CleanupAction
{
    /**
     * @var TokenManager
     */
    private $tokenManager;

    /**
     * @var LoginReader
     */
    private $loginReader;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Constructor.
     *
     * @param TokenManager       $tokenManager The token manager
     * @param LoginReader        $loginReader  The login reader/manager
     * @param ContainerInterface $ci           The container interface
     * @param LoggerFactory      $lf           The loggerFactory
     */
    public function __construct(TokenManager $tokenManager, LoginReader $loginReader, ContainerInterface $ci, LoggerFactory $lf)
    {
        $this->tokenManager = $tokenManager;
        $this->loginReader = $loginReader;
        $this->retention = $ci->get('settings')['jwt']['retention'];
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        //$token = explode(' ', (string) $request->getHeaderLine('Authorization'))[1] ?? ''; // if header is used

        $logincleanup = $this->loginReader->cleanupLogins($this->retention);
        $tokencleanup = $this->tokenManager->cleanupTokens();

        // Build JSON representation
        $result = [
            'message' => 'Cleanup result: '.$logincleanup.' login(s), '.$tokencleanup.' token(s)',
            'class' => 'CleanupAction',
            'status' => 'ok',
            'code' => 422,
        ];

        // Feed the logger
        $this->logger->debug("CleanupAction: {$logincleanup} logins {$tokencleanup} tokens deleted");

        $response->getBody()->write((string) json_encode($result));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}
