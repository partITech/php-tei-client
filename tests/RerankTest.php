<?php

use Partitech\PhpTeiClient\TeiClient;
use Partitech\PhpTeiClient\TeiClientException;
use PHPUnit\Framework\TestCase;

class RerankTest extends TestCase
{

    private TeiClient $client;
    private string $apiKey = 'testKgey';
    private string $url = 'http://localhost:8081';
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
        $client->rerank('What is Deep Learning?', ['Deep Learning is not...', 'Deep learning is...']);
    }

    public function testExceptionServerAddressError(){
        $client = new TeiClient(url: 'http://wrong.address:0123456', apiKey: 'wrongKey');
        $this->expectException(TeiClientException::class);
        $client->rerank('What is Deep Learning?', ['Deep Learning is not...', 'Deep learning is...']);
    }

    public function testRerank(){
        $content = ['Deep learning is...', 'sheeze is made of', 'Deep Learning is not...'];
        $result = $this->client->rerank('What is Deep Learning?', $content);
        $this->assertIsArray($result);


        for($i=0, $count=count($content); $i<$count; $i++) {
            $this->assertIsArray($result[$i]);
            $this->assertIsFloat($result[$i]['score']);
            $this->assertIsInt($result[$i]['index']);
        }
        $this->assertEquals(0, $result[0]['index']);
        $this->assertEquals(2, $result[1]['index']);
        $this->assertEquals(1, $result[2]['index']);
    }

    public function testRerankedContent(){
        $content = ['Deep learning is...', 'sheeze is made of', 'Deep Learning is not...'];
        $result = $this->client->getRerankedContent('What is Deep Learning?', $content);
        $this->assertIsArray($result);
        $this->assertCount(3, $result);


        $result = $this->client->getRerankedContent('What is Deep Learning?', $content, 2);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);

        $this->assertEquals($content[0], $result[0]['content']);
        $this->assertEquals($content[2], $result[1]['content']);
    }

}