<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpException;

final class HttpExceptionMiddleware implements MiddlewareInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        try {
            return $handler->handle($request);
        } catch (HttpException $httpException) {
            // Handle the http exception here
            $statusCode = $httpException->getCode();
            $response = $this->responseFactory->createResponse()->withStatus($statusCode);
            //$errorMessage = sprintf('%s %s', $statusCode, $response->getReasonPhrase());
            $className = new \ReflectionClass(get_class($httpException));
            $data = [
                'message' => $httpException->getMessage(),
                'class' => $className->getShortName(),
                'status' => 'error',
                'code' => $statusCode,
            ];
            $body = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            $type = 'application/problem+json';
            //$response = $app->getResponseFactory()->createResponse();
            $response->getBody()->write($body);

            return $response->withStatus($statusCode)->withHeader('Content-type', $type);
            // Log the errror message
            // $this->logger->error($errorMessage);

            // Render twig template or just add the content to the body
            // $response->getBody()->write($errorMessage);

            return $response;
        }
    }
}
