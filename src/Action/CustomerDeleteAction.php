<?php

namespace App\Action;

use App\Domain\Customer\Service\CustomerDeletor;
use App\Factory\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Logger;

/**
 * Action.
 */
final class CustomerDeleteAction
{
    /**
     * @var CustomerDeletor
     */
    private $customerDeletor;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * The constructor.
     *
     * @param CustomerDeletor $customerDeletor The customer deletor
     * @param LoggerFactory   $lf              The loggerFactory
     */
    public function __construct(CustomerDeletor $customerDeletor, LoggerFactory $lf)
    {
        $this->customerDeletor = $customerDeletor;
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
        $this->logger->debug("CustomerDeleteAction: id: {$customerId}");

        // Invoke the Domain with inputs and retain the result
        $result = $this->customerDeletor->deleteCustomer($customerId);

        // Transform the result into the JSON representation
        $result = [
            'customer_id' => $result,
        ];

        // Build the HTTP response
        $response->getBody()->write((string) json_encode($result));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}
