<?php

namespace App\Action;

use App\Domain\Customer\Service\CustomerCreator;
use App\Factory\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Logger;

/**
 * @OA\Post(
 *     path="/api/customers",
 *     summary="create customer",
 *     description="create customer according to posted data ",
 *     @OA\RequestBody(
 *         description="Customer side search object",
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
 * Action
 */
final class CustomerCreateAction
{
    /**
     * @var Logger
     */
    private $logger;


    /**
     * @var CustomerCreator
     */
    private $customerCreator;

    /**
     * The constructor.
     *
     * @param CustomerCreator $customerCreator The customer creator
     * @param LoggerFactory $lf The loggerFactory
     */
    public function __construct(CustomerCreator $customerCreator, LoggerFactory $lf)
    {
        $this->customerCreator = $customerCreator;
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    /**
     * Invoke.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Collect input from the HTTP request
        $data = (array)$request->getParsedBody();

        // Invoke the Domain with inputs and retain the result
        $customerId = $this->customerCreator->createCustomer($data);

       // Feed the logger
       $this->logger->debug("CustomerCreateAction: id: $customerId");

       // Transform the result into the JSON representation
        $result = [
            'customer_id' => $customerId
        ];

        // Build the HTTP response
        $response->getBody()->write((string)json_encode($result));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}