<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

class SearchController extends Controller
{
    //
    public function index()
    {
        $res = $this->_create_index();
        print_r($res);
    }

    private function _create_index()
    {
        $client = $this->_init_es();
        $params = [
            'index' => 'my_index',
            'body'  => [
                'query' => [
                    'match' => [
                        'testField' => 'abc'
                    ]
                ]
            ]
        ];
        $response = $client->index($params);

        printf("Total docs: %d\n", $response['hits']['total']['value']);
        printf("Max score : %.4f\n", $response['hits']['max_score']);
        printf("Took      : %d ms\n", $response['took']);

        print_r($response['hits']['hits']); // documents
    }

    private function _init_es()
    {
        $client = ClientBuilder::create()->setHosts(['localhost:9200'])->setBasicAuthentication('elastic', '123456')->build();
        return $client;
    }
}
