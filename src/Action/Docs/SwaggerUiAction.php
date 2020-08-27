<?php

// https://odan.github.io/2020/06/12/slim4-swagger-ui.html
// petstore.yaml is example

namespace App\Action\Docs;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\Yaml\Yaml;

final class SwaggerUiAction
{
    /**
     * @var view
     */
    private $view;

    /**
     * The constructor.
     *
     */
    public function __construct(Twig $twig)
    {
        $this->view = $twig;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface{
        // Path to the yaml file
        //$yamlFile = __DIR__ . '/../../../resources/docs/swagger.yaml';
        $yamlFile = __DIR__ . '/../../../resources/docs/petstore.yaml';

        $viewData = [
            'spec' => json_encode(Yaml::parseFile($yamlFile)),
        ];

		return $this->view->render($response, 'docs/swagger.twig', $viewData);
        //return $this->responder->render($response, 'docs/swagger.twig', $viewData);
    }
}