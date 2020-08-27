<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action
 */

/**
 * @OA\Get(
 *     path="/hello/",
 *     summary="send greeting to user",
 *     description="send greeting to user given in query ",
 *
 *
 *     @OA\MediaType(
 *     mediaType="application/json",
 *     @OA\Schema(ref="#/components/schemas/SearchObject")
 *     )
 *     ),
 *
 *
 * )
 */
final class HelloAction
{
    /**
     * Invoke.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     * @param array<string> $args The arguments
     * @return ResponseInterface The response
     * @OA\Parameter(
     *     name="NAME",
     *     in="query",
     *     type="string",
     *     description="The field used to set name")
     *
     * @OA\Response(
     *         response=200,
     *         description="hello {name}",
     *      @OA\MediaType(
     *         mediaType="application/json",
     *
     *     )
     *     )
     *
     * @OA\Response(
     *         response="default",
     *         description="hello {name}"
     *     )
     *
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface {
		
		if(isset($args['name']))
			$name = (string)$args['name'];
			else $name = "World!";

		$result = ["Hello" => $name];
		//$response->getBody()->write((string)($result));
		$response->getBody()->write((string)json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }
}