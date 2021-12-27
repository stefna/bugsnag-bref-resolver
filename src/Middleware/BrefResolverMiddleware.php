<?php declare(strict_types=1);

namespace Stefna\BugsnagBrefResolver\Middleware;

use Bref\Event\LambdaEvent;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Stefna\BugsnagBrefResolver\BrefResolver;

final class BrefResolverMiddleware implements MiddlewareInterface
{
	public function __construct(
		private BrefResolver $resolver,
	) {}

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$lambdaEvent = $request->getAttribute('lambda-event');
		if ($lambdaEvent instanceof LambdaEvent) {
			$this->resolver->setEvent($lambdaEvent);
		}
		return $handler->handle($request);
	}
}
