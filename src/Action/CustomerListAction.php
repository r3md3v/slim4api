<?php

namespace App\Action;

use App\Domain\Customer\Service\CustomerLister;
use App\Factory\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Logger;

/**
 * Action.
 */
final class CustomerListAction
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var CustomerLister
     */
    private $customerLister;

    /**
     * The constructor.
     *
     * @param CustomerLister $customerLister The customer lister
     * @param Logger         $logger         the loggerFactory
     */
    public function __construct(CustomerLister $customerLister, LoggerFactory $lf)
    {
        $this->customerLister = $customerLister;
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
        $page = isset($_GET['page']) ? $_GET['page'] : -1;
        $size = isset($_GET['size']) ? $_GET['size'] : -1;

        // Feed the logger
        $this->logger->debug("CustomerListAction: page: {$page}, size: {$size}");

        // Invoke the Domain with inputs and retain the result
        $customerList = $this->customerLister->getCustomerList($page, $size);

        // Transform the result into the JSON representation
        $result = [];
        foreach ($customerList as $customerData) {
            array_push($result, [
                'customer_id' => $customerData->id,
                'name' => $customerData->name,
                'address' => $customerData->address,
                'city' => $customerData->city,
                'phone' => $customerData->phone,
                'email' => $customerData->email,
            ]);
        }

        // Build the HTTP response
        $response->getBody()->write((string) json_encode($result));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
