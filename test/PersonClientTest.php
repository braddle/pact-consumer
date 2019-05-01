<?php
declare(strict_types=1);

namespace Braddle\Test\Consumer;

use Braddle\Consumer\PersonService;
use GuzzleHttp\Client;
use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServerEnvConfig;
use PHPUnit\Framework\TestCase;

class PersonClientTest extends TestCase
{
    public function testRetrievingPersonByID()
    {
        $request = (new ConsumerRequest())
            ->setMethod("GET")
            ->setPath("/person/1")
            ->addHeader("Accept", "application/json");

        $matcher = new Matcher();
        $response = (new ProviderResponse())
            ->setStatus(200)
            ->addHeader('Content-Type', "application/json")
            ->setBody(
                [
                    "first_name" => $matcher->like("Mark"),
                    "last_name"  => $matcher->like("Bradley"),
                ]
            );

        $config   = new MockServerEnvConfig();

        $builder  = new InteractionBuilder($config);
        $builder->given("User 1 exists")
            ->uponReceiving("A GET request to /user/{user_id}")
            ->with($request)
            ->willRespondWith($response);

        $guzzleClient  = new Client(["base_uri" => $config->getBaseUri()]);
        $personService = new PersonService($guzzleClient);

        $person = $personService->getPersonById(1);

        $builder->verify();

        $this->assertEquals("Mark", $person->getFirstName());
        $this->assertEquals("Bradley", $person->getLastName());
    }
}
