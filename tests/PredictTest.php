<?php

use Partitech\PhpTeiClient\TeiClient;
use Partitech\PhpTeiClient\TeiClientException;
use PHPUnit\Framework\TestCase;

class PredictTest extends TestCase
{

    private TeiClient $client;
    private string $apiKey = 'testKey';
    private string $url = 'http://localhost:8082';
    protected function setUp(): void
    {
        $this->client = new TeiClient(url: $this->url, apiKey: $this->apiKey);
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(TeiClient::class, $this->client);
        $client = new TeiClient(url: $this->url, apiKey: $this->apiKey);
        $reflection = new \ReflectionClass($client);
        $apiKeyProperty = $reflection->getProperty('apiKey');
        $endpointProperty = $reflection->getProperty('url');
        $this->assertEquals($this->apiKey, $apiKeyProperty->getValue($client));
        $this->assertEquals($this->url, $endpointProperty->getValue($client));
    }


    public function testExceptionApiKeyError(){
        $client = new TeiClient(url: $this->url, apiKey: 'wrongKey');
        $this->expectException(TeiClientException::class);
        $this->expectExceptionMessageMatches('/Unauthorized/');
        $client->predict('I like you.');
    }

    public function testExceptionServerAddressError(){
        $client = new TeiClient(url: 'http://wrong.address:0123456', apiKey: 'wrongKey');
        $this->expectException(TeiClientException::class);
        $client->predict('I like you.');
    }

    public function testPredictString(){
        $results = $this->client->predict('I like you.');
        $this->assertIsArray($results);
        foreach ($results as $key => $result){
            $this->assertIsArray($result);
            $this->assertIsFloat($result['score']);
            $this->assertIsString($result['label']);
        }
    }

}