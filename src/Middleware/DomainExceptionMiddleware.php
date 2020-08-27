<?php

namespace App\Middleware;

use App\Factory\LoggerFactory;
use DomainException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

final class DomainExceptionMiddleware implements MiddlewareInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory, LoggerFactory $lf)
    {
        $this->responseFactory = $responseFactory;
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        try {
            return $handler->handle($request);
        } catch (DomainException $domainexception) {
            // Handle the domain exception here
            $statusCode = $domainexception->getCode();
            if (!is_int($statusCode) || $statusCode < 400 || $statusCode > 599) {
                $statusCode = 500;
            }
            $response = $this->responseFactory->createResponse()->withStatus($statusCode);
            $errorMessage = sprintf('%s %s', $statusCode, $domainexception->getMessage());
            $className = new \ReflectionClass(get_class($domainexception));
            $data = [
                'message' => $domainexception->getMessage(),
                'class' => $className->getShortName(),
                'status' => 'error',
                'code' => $statusCode,
            ];
            $body = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            $type = 'application/problem+json';
            $response->getBody()->write($body);

            // Log the errror message
            $this->logger->debug($errorMessage);

            return $response->withStatus($statusCode)->withHeader('Content-type', $type);
            // $response = $app->getResponseFactory()->createResponse();
            // Render twig template or just add the content to the body
            // $response->getBody()->write($errorMessage);
            // return $response;
        }
    }
}
