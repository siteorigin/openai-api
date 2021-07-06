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
$client->completions('davinci')->complete("The most important technology for humanity is", [
    'max_tokens' => 32,
    'temperature' => 0.8,
    'n' => 4,
    'stop' => ["\n", '.']
]);
```

## Installation

You can install the package via composer:

```bash
composer require siteorigin/openai-api
```

## Usage

```php
use SiteOrigin\OpenAI\Client;
$client = new Client($key);

$c = $client->completions('davinci')->complete($prompt, $options);
$e = $client->engines()->list();
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
