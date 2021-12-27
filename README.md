# Bugsnag Resolver for Bref

Custom resolver for `bugsnag/bugsnag` to help populate request context when using [Type handlers](https://bref.sh/docs/function/handlers.html)

For now, it only handles `HttpRequestEvent` but it would be easy to implement support for the rest of the different event types.

## Installation

    $ composer require stefna/bugsnag-bref-resolver

## Usage

```php
use Bugsnag\Client;
use Bugsnag\Configuration;
use Bugsnag\Handler;
use Stefna\BugsnagBrefResolver\BrefResolver;
use Stefna\BugsnagBrefResolver\Middleware\BrefResolverMiddleware;

$env = new Env();
$config = new Configuration($apiKey);

// Should probably be coming from a di container
$resolver = new BrefResolver;

$client = new Client($config, $resolver);
$client->registerDefaultCallbacks();
Handler::register($client);

$middleware = new BrefResolverMiddleware($resolver):
// add to middleware dispatcher
```
