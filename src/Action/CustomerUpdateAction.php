<?php

namespace App\Action;

use App\Domain\Customer\Service\CustomerUpdator;
use App\Factory\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Logger;

/**
 * @OA\Post(
 *     path="/api/customers",
 *     summary="update customer",
 *     description="update customer according to posted data ",
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
 * Action.
 */
final class CustomerUpdateAction
{
    /**
     * @var CustomerUpdator
     */
    private $customerUpdator;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * The constructor.
     *
     * @param CustomerUpdator $customerUpdator The customer updator
     * @param LoggerFactory   $lf              The loggerFactory
     */
    public function __construct(CustomerUpdator $customerUpdator, LoggerFactory $lf)
    {
        $this->customerUpdator = $customerUpdator;
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
        $data = (array) $request->getParsedBody();

        // Invoke the Domain with inputs
        $this->customerUpdator->updateCustomer($customerId, $data);

        // Feed the logger
        $this->logger->debug("CustomerUpdateAction: id: {$customerId}");

        // Transform the result into the JSON representation
        $result = [
            'customer_id' => $customerId,
        ];

        // Build the HTTP response
        $response->getBody()->write((string) json_encode($result));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}
