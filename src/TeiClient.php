<?php
namespace Partitech\PhpTeiClient;

use Symfony\Component\HttpClient\HttpClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;

use Throwable;
class TeiClient
{
    protected ?string $apiKey;
    protected string $url;
    private HttpClientInterface $httpClient;

    public function __construct(string $url, ?string $apiKey=null)
    {
        $this->httpClient = HttpClient::create();
        $this->apiKey = $apiKey;
        $this->url = $url;
    }

    /**
     * @throws TeiClientException
     */
    protected function request(
        string $method,
        string $path,
        array $request = []
    ): array
    {
        try {
            $response = $this->httpClient->request(
                $method,
                $this->url . '/' . $path,
                [
                    'json' => $request,
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->apiKey
                    ]
                ]
            );
        } catch (Throwable $e) {
            throw new TeiClientException($e->getMessage(), $e->getCode(), $e);
        }

        try {
            $result = $response->toArray(true);
        }catch (\Throwable $e){
            throw new TeiClientException($e->getMessage(), $e->getCode(), $e);
        }

        return $result;
    }

    /**
     * @throws TeiClientException
     */
    public function embed(string|array $content): array
    {
        return $this->request(method: 'POST', path: 'embed', request: [ 'inputs' => $content ] );
    }

    /**
     * @throws TeiClientException
     */
    public function embedSparse(string|array $content): array
    {
        return $this->request(method: 'POST', path: 'embed_sparse', request: [ 'inputs' => $content ] );
    }

    /**
     * @throws TeiClientException
     */
    public function rerank(string $query, array $texts): array
    {
        return $this->request(method: 'POST', path: 'rerank', request: [ 'query' => $query, 'texts' => $texts ]);
    }

    /**
     * @throws TeiClientException
     */
    public function getRerankedContent(string $query, array $texts, ?int $top=null): array
    {
        $result = $this->request(method: 'POST', path: 'rerank', request: [ 'query' => $query, 'texts' => $texts ]);
        $rerankedContent  = [];
        for($i=0, $size = count($result); $i < $size && ($top === null || $i < $top); $i++) {
            $result[$i]['content'] = $texts[$result[$i]['index']];
            $rerankedContent[] = $result[$i];
        }
        return $rerankedContent;
    }

    /**
     * @throws TeiClientException
     */
    public function predict(string|array $content): array
    {
        return $this->request(method: 'POST', path: 'predict', request: [ 'inputs' => $content ] );
    }

}