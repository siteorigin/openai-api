# A PHP wrapper for the OpenAI API

[![Latest Version on Packagist](https://badgen.net/packagist/v/siteorigin/openai-api)](https://packagist.org/packages/siteorigin/openai-api)
[![PHP Version Requirement](https://badgen.net/packagist/php/siteorigin/openai-api)](https://packagist.org/packages/siteorigin/openai-api)
[![GitHub Tests Action Status](https://github.com/siteorigin/openai-api/actions/workflows/run-tests.yml/badge.svg)](https://github.com/siteorigin/openai-api/actions?query=workflow%3ATests+branch%3Amaster)
[![GitHub Code Style Action Status](https://github.com/siteorigin/openai-api/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/siteorigin/openai-api/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://badgen.net/packagist/dt/siteorigin/openai-api)](https://packagist.org/packages/siteorigin/openai-api)

---

GPT-3 lets you inject a little bit of AI magic into your apps, and this PHP wrapper makes it even easier to access. Covers the entire OpenAI API, in a familiar PHP way, without limiting any functionality.

```php
use SiteOrigin\OpenAI\Client;
$client = new Client($_ENV['OPENAI_API_KEY']);
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

Read more on the [documentation wiki](https://github.com/siteorigin/openai-api/wiki).

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
use SiteOrigin\OpenAI\Models;

$client = new Client($_ENV['OPENAI_API_KEY']);

// Create a completion call
$c = $client->completions(Models::BABBAGE)->complete('The meaning of life is: ', [ /* ... */]);

// List all the available engines
$e = $client->engines()->list();

// Classify safe/unsafe content
$f = $client->filter()->classify('ponies are fantastic!'); // 'safe'
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
