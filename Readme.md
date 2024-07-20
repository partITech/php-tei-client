# PHP TEI Client

This is a very simple PHP client for the [Text Embeddings Inference (TEI)](https://github.com/huggingface/text-embeddings-inference) server from Huggingface , which is a blazing fast inference solution for text embeddings models. This client allows you to easily interact with the TEI server's API endpoints for embedding, reranking, and prediction.

## Installation

You can install this package via Composer:

```bash
composer require partitech/php-tei-client
```

## Usage

Here's a basic example of how to use the TEI client:

### Embedding
```php
<?php

use Partitech\PhpTeiClient\TeiClient;

$client = new TeiClient(url: 'http://localhost:8080', apiKey: 'yourApiKey');

// Embed a single string
$result = $client->embed(content: 'What is Deep Learning?');

// Embed an array of strings
$contents = ['What is Deep Learning?', 'What is Machine Learning?'];
$result = $client->embed(content: $contents);
```
Result: 
```php
Array
(
    [0] => Array
        (
            [0] => 0.007737029
            [1] => -0.06754478
            [2] => -0.0380035
-----
            [1023] => 0.026126498
        )

)
```

### Using Re-rankers
```php
<?php

use Partitech\PhpTeiClient\TeiClient;

$client = new TeiClient(url: 'http://localhost:8080', apiKey: 'yourApiKey');


// Rerank an array of texts based on a query
$content = ['Deep learning is...', 'cheese is made of', 'Deep Learning is not...'];
$result = $client->rerank('What is the difference between Deep Learning and Machine Learning?', $content);
```
Result:
```php
Array
(
    [0] => Array
        (
            [index] => 0
            [score] => 0.94238955
        )

    [1] => Array
        (
            [index] => 2
            [score] => 0.120219156
        )

    [2] => Array
        (
            [index] => 1
            [score] => 3.7323738E-5
        )

)

```
You can also get associated text in the result and limit to the top n result:
```php
<?php

use Partitech\PhpTeiClient\TeiClient;

$client = new TeiClient(url: 'http://localhost:8080', apiKey: 'yourApiKey');


// Rerank an array of texts based on a query
$content = ['Deep learning is...', 'cheese is made of', 'Deep Learning is not...'];
$result = $client->getRerankedContent('What is the difference between Deep Learning and Machine Learning?', $content);
```
Result: 

```php 
Array
(
    [0] => Array
        (
            [index] => 0
            [score] => 0.94238955
            [content] => Deep learning is...
        )

    [1] => Array
        (
            [index] => 2
            [score] => 0.120219156
            [content] => Deep Learning is not...
        )

)

```

### Using Sequence Classification
```php
<?php

use Partitech\PhpTeiClient\TeiClient;

$client = new TeiClient(url: 'http://localhost:8080', apiKey: 'yourApiKey');

// Predict the sentiment of a string
$result = $client->predict('I love this product!');
```

Result:
```php 
Array
(
    [0] => Array
        (
            [score] => 0.986059
            [label] => love
        )

    [1] => Array
        (
            [score] => 0.006502793
            [label] => admiration
        )

    [2] => Array
        (
            [score] => 0.0020027023
            [label] => approval
        )

    [3] => Array
        (
            [score] => 0.0008381181
            [label] => neutral
        )

    [4] => Array
        (
            [score] => 0.0005737838
            [label] => joy
        )
---------

    [27] => Array
        (
            [score] => 2.2074879E-5
            [label] => grief
        )

)

```

### Using SPLADE pooling
```php
<?php

use Partitech\PhpTeiClient\TeiClient;

$client = new TeiClient(url: 'http://localhost:8080', apiKey: 'yourApiKey');
$result = $client->embedSparse(content: 'What is Deep Learning?');
```
Result:

```php
Array
(
    [0] => Array
        (
            [0] => Array
                (
                    [index] => 1012
                    [value] => 1.0751953
                )

            [1] => Array
                (
                    [index] => 2003
                    [value] => 1.5722656
                )

            [2] => Array
                (
                    [index] => 2784
                    [value] => 2.9082031
                )

            [3] => Array
                (
                    [index] => 4083
                    [value] => 2.7929688
                )

        )

)

```

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

[MIT](https://choosealicense.com/licenses/mit/)



