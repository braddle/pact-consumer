<?php
declare(strict_types=1);

namespace Braddle\Consumer;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class PersonService
{
    /**
     * @var Client
     */
    private $guzzleClient;

    public function __construct(Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    public function getPersonById(int $userId)
    {
        $request = new Request("GET", "/person/" . $userId, ["Accept" => "application/json"]);

        $response = $this->guzzleClient->send($request);

        $body = json_decode($response->getBody()->getContents(), true);

        return new Person($body["first_name"], $body["last_name"]);
    }
}
