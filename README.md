# A PHP wrapper for the OpenAI API

[![Latest Version on Packagist](https://img.shields.io/packagist/v/siteorigin/openai-api.svg?style=flat-square)](https://packagist.org/packages/siteorigin/openai-api)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/siteorigin/openai-api/run-tests?label=tests)](https://github.com/siteorigin/openai-api/actions?query=workflow%3ATests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/siteorigin/openai-api/Check%20&%20fix%20styling?label=code%20style)](https://github.com/siteorigin/openai-api/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/siteorigin/openai-api.svg?style=flat-square)](https://packagist.org/packages/siteorigin/openai-api)

---

A PHP wrapper that makes it easy to use the OpenAI API from your PHP application. Covers the entire OpenAI API, in a familiar PHP way, without limiting any functionality.

```php
use SiteOrigin\OpenAI\Client;
$client = new Client($key);
$completions = $client->completions('davinci')->complete("The most important technology for humanity is", [
  'max_tokens' => 32,
  'temperature' => 0.8,
  'n' => 4,
  'stop' => ["\n", '.']
]);
foreach($completions as $c) {
	echo $c->text . "\n";
}
```

## Installation

You can install the package via composer:

```bash
composer require siteorigin/openai-api
```

## Usage

For most of the features of this wrapper, you should have an understanding of the [OpenAI API](https://beta.openai.com/docs/api-reference/introduction).

```php
// Set up a client with your API key.
use SiteOrigin\OpenAI\Client;
$client = new Client($key);

// Create a completion call
$c = $client->completions('davinci')->complete($prompt, $options);

// List all the available engines
$e = $client->engines()->list();

// Perform a search
$r = $client->search()->search('President', [
  "White House","hospital","school"
]);
$r = $client->search()->search('President', 'the-file-id')

// Request an Answer
$documents = [
	"Puppy named Bailey is happy.",
	"Puppy named Bella is sad.",
];
$a = $client->answers()->create(
  'Which puppy is happy?',
  $documents, // Or a file-id
  'In 2017, U.S. life expectancy was 78.6 years.',
  [["What is human life expectancy in the United States?","78 years."]],
  ["max_tokens" => 5, "stop" => ["\n", "<|endoftext|>"] ]
);

// Request a Classification
$c = $client->classifications()->create(
	'It is a raining day :(',
  [["A happy moment", "Positive"],["I am sad.", "Negative"],["I am feeling awesome", "Positive"]]
);
$c = $client->classifications()->create("I'm so happy to be alive", 'the-file-id');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please email greg@siteorigin.com if you find any security vulnerabilities.

## Credits

- [Greg Priday](https://github.com/gregpriday)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
