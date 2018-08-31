<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-08-23
 * Time: 11:59
 */

namespace App\Controller\Rest;

use FOS\RestBundle\Controller\FOSRestController;

use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\Response;

class ApiController extends FOSRestController
{

    /**
     * List API Methods
     * @Rest\Get(
     *     "/",
     *     name="api"
     * )
     *
     * @return array
     */
    public function getAction()
    {
        $help = [
            'API Help' => [
                'url' =>        'api',
                'method' =>     'GET',
                'parameters' => [],
                'returns' =>    'Array of operations supported by this API'
            ],
            'Listener Create' => [
                'url' =>        'api/listeners',
                'method' =>     'POST',
                'parameters' => [
                    'name' =>       'string'
                ],
                'returns' =>    'JSON object'
            ],
            'Listener Delete' => [
                'url' =>        'api/listeners/{id}',
                'method' =>     'DELETE',
                'parameters' => [
                    'id' =>         'integer'
                ],
                'returns' =>    'null'
            ],
            'Listener Fetch' => [
                'url' =>        'api/listeners/{id}',
                'method' =>     'GET',
                'parameters' => [
                    'id' =>         'integer'
                ],
                'returns' =>    'JSON object'
            ],
            'Listener Patch' => [
                'url' =>        'api/listeners/{id}',
                'method' =>     'Path',
                'options' => [
                    'name' =>       'string'
                ],
                'returns' =>    'JSON object'
            ],
            'Listener Update' => [
                'url' =>        'api/listeners/{id}',
                'method' =>     'PUT',
                'parameters' => [
                    'name' =>       'string'
                ],
                'returns' =>    'JSON object'
            ],
            'Listeners List' => [
                'url' =>        'api/listeners',
                'method' =>     'GET',
                'parameters' => [],
                'returns' =>    'Array of JSON objects'
            ],
            'Signal Create' => [
                'url' =>        'api/signals',
                'method' =>     'POST',
                'parameters' => [
                    'name' =>       'string',
                    'category' =>   'string',
                    'sku' =>        'string',
                    'price' =>      'decimal',
                    'quantity' =>   'integer'
                ],
                'returns' =>    'JSON object'
            ],
            'Signal Delete' => [
                'url' =>        'api/signals/{id}',
                'method' =>     'DELETE',
                'parameters' => [
                    'id' =>         'integer'
                ],
                'returns' =>    'null'
            ],
            'Signal Fetch' => [
                'url' =>        'api/signals/{id}',
                'method' =>     'GET',
                'parameters' => [
                    'id' => 'integer'
                ],
                'returns' => 'JSON object'
            ],
            'Signal Patch' => [
                'url' =>        'api/signals/{id}',
                'method' =>     'PATCH',
                'options' => [
                    'name' =>       'string',
                    'category' =>   'string',
                    'sku' =>        'string',
                    'price' =>      'decimal',
                    'quantity' =>   'integer'
                ],
                'returns' =>    'JSON object'
            ],
            'Signal Update' => [
                'url' =>        'api/signals/{id}',
                'method' =>     'PUT',
                'parameters' => [
                    'name' =>       'string',
                    'category' =>   'string',
                    'sku' =>        'string',
                    'price' =>      'decimal',
                    'quantity' =>   'integer'
                ],
                'returns' =>    'JSON object'
            ],
            'Signal List' => [
                'url' =>        'api/signals',
                'method' =>     'GET',
                'parameters' => [],
                'returns' =>    'Array of JSON objects'
            ]
        ];
        $view = $this->view($help, Response::HTTP_OK);

        return $this->handleView($view);
    }
}
