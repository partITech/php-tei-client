<?php

use Partitech\PhpTeiClient\TeiClient;
use Partitech\PhpTeiClient\TeiClientException;
use PHPUnit\Framework\TestCase;

class EmbedSparseTest extends TestCase
{
    private TeiClient $client;
    private string $apiKey = 'testKey';
    private string $url = 'http://localhost:8083';
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
        $results = $this->client->embedSparse(content: 'What is Deep Learning?');
        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $results = $this->client->embedSparse(content: ['What is Deep Learning?', 'hey hi there !']);
        $this->assertIsArray($results);
        $this->assertCount(2, $results);

        foreach($results[0] as $result){
            $this->assertIsArray($result);
            $this->assertIsInt($result['index']);
            $this->assertIsFloat($result['value']);
        }
    }

    public function testExceptionApiKeyError(){
        $client = new TeiClient(url: $this->url, apiKey: 'wrongKey');
        $this->expectException(TeiClientException::class);
        $this->expectExceptionMessageMatches('/Unauthorized/');
        $client->embedSparse(content: 'What is Deep Learning?');
    }

    public function testExceptionServerAddressError(){
        $client = new TeiClient(url: 'http://wrong.address:0123456', apiKey: 'wrongKey');
        $this->expectException(TeiClientException::class);
        $client->embedSparse(content: 'What is Deep Learning?');
    }

}