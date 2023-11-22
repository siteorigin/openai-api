# Archive Notice for the OpenAI PHP Wrapper

This repository for the OpenAI PHP Wrapper, developed by SiteOrigin, is now archived. For updated and ongoing PHP integration with OpenAI's API, please refer to the [OpenAI PHP Client](https://github.com/openai-php/client) instead.

## Original Repository Details:

- **Repository:** [OpenAI PHP Wrapper by SiteOrigin](https://github.com/siteorigin/openai-api)
- **Packagist:** [siteorigin/openai-api](https://packagist.org/packages/siteorigin/openai-api)
- **Functionality:** Facilitated integration with the OpenAI API, offering a PHP-based approach for incorporating GPT-3 into applications.

### Features:

- Provided a comprehensive interface for the OpenAI API in PHP.
- Simplified syntax for common tasks like generating completions.
- Supported full API functionality without constraints.

### Example Code Snippet:

```php
use SiteOrigin\OpenAI\Client;
$client = new Client($_ENV['OPENAI_API_KEY']);
// Example usage for generating completions
$completions = $client->completions('davinci')->complete("Sample prompt", [/* parameters */]);
foreach($completions as $c) {
    echo $c->text . "\n";
}
```

### Further Information:

For those who previously utilized or are interested in this PHP wrapper, it's recommended to transition to the [new OpenAI PHP Client](https://github.com/openai-php/client) for the latest updates and continued support.
