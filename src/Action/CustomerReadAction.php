<?php

namespace App\Action;

use App\Domain\Customer\Service\CustomerReader;
use App\Factory\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Logger;

/**
 * Action.
 */
final class CustomerReadAction
{
    /**
     * @var CustomerReader
     */
    private $CustomerReader;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * The constructor.
     *
     * @param CustomerReader $CustomerReader The Customer reader
     * @param LoggerFactory  $lf             The loggerFactory
     */
    public function __construct(CustomerReader $CustomerReader, LoggerFactory $lf)
    {
        $this->CustomerReader = $CustomerReader;
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
        $customerId = (int) $args['id'];

        // Feed the logger
        $this->logger->debug("CustomerReadAction: id: {$customerId}");

        // Invoke the Domain with inputs and retain the result
        $CustomerData = $this->CustomerReader->getCustomerDetails($customerId);

        // Transform the result into the JSON representation
        $result = [
            'Customer_id' => $CustomerData->id,
            'name' => $CustomerData->name,
            'address' => $CustomerData->address,
            'city' => $CustomerData->city,
            'phone' => $CustomerData->phone,
            'email' => $CustomerData->email,
        ];

        // Build the HTTP response
        $response->getBody()->write((string) json_encode($result));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
