<?php

use Partitech\PhpTeiClient\TeiClient;
use Partitech\PhpTeiClient\TeiClientException;
use PHPUnit\Framework\TestCase;

class EmbedTest extends TestCase
{

    private TeiClient $client;
    private string $apiKey = 'testKey';
    private string $url = 'http://localhost:8080';
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

    /**
     * @throws TeiClientException
     */
    public function testEmbedString()
    {
        $result = $this->client->embed(content: 'What is Deep Learning?');
        $this->assertCount(1, $result);
        $this->assertIsArray($result[0]);
        $this->assertContainsOnly('float', $result[0]);
    }

    /**
     * @throws TeiClientException
     */
    public function testEmbedSingleString()
    {
        $result = $this->client->embed(content: ['What is Deep Learning?']);
        $this->assertCount(1, $result);
        $this->assertIsArray($result[0]);
        $this->assertContainsOnly('float', $result[0]);
    }

    /**
     * @throws TeiClientException
     */
    public function testEmbedArrayOfStrings()
    {
        $contents = ['What is Deep Learning?', 'What is Deep Learning?', 'What is Deep Learning?' ];
        $result = $this->client->embed(content: $contents);
        $this->assertCount(count($contents), $result);
        for($i=0, $count=count($contents); $i<$count; $i++) {
            $this->assertIsArray($result[$i]);
            $this->assertContainsOnly('float', $result[$i]);
        }
    }

    public function testExceptionApiKeyError(){
        $client = new TeiClient(url: $this->url, apiKey: 'wrongKey');
        $this->expectException(TeiClientException::class);
        $this->expectExceptionMessageMatches('/Unauthorized/');
        $client->embed(content: 'What is Deep Learning?');
    }

    public function testExceptionServerAddressError(){
        $client = new TeiClient(url: 'http://wrong.address:0123456', apiKey: 'wrongKey');
        $this->expectException(TeiClientException::class);
        $client->embed(content: 'What is Deep Learning?');
    }

}