<?php

namespace App\Action;

use App\Domain\Customer\Service\CustomerSearcher;
use App\Factory\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Logger;

/**
 * Action.
 */
final class CustomerSearchAction
{
    /**
     * @var CustomerSearcher
     */
    private $customerSearcher;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * The constructor.
     *
     * @param CustomerSearcher $customerSearcher The customer searcher
     * @param Logger           $logger           the loggerFactory
     */
    public function __construct(CustomerSearcher $customerSearcher, LoggerFactory $lf)
    {
        $this->customerSearcher = $customerSearcher;
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
        $keyword = (string) $args['keyword'];
        $in = isset($_GET['in']) ? $_GET['in'] : -1;
        $page = isset($_GET['page']) ? $_GET['page'] : -1;
        $size = isset($_GET['size']) ? $_GET['size'] : -1;

        // Feed the logger
        $this->logger->debug("CustomerSearchAction: page: {$page}, size: {$size}, in: {$in}, keyword: {$keyword}");

        // Invoke the Domain with inputs and retain the result
        $customerSearch = $this->customerSearcher->getCustomerSearch($keyword, $in, $page, $size);

        // Transform the result into the JSON representation
        $result = [];
        foreach ($customerSearch as $customerData) {
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
