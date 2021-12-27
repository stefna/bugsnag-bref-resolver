<?php declare(strict_types=1);

namespace Stefna\BugsnagBrefResolver;

use Bref\Event\Http\HttpRequestEvent as BrefHttpRequestEvent;
use Bref\Event\LambdaEvent;
use Bugsnag\Request\NullRequest;
use Bugsnag\Request\RequestInterface;
use Bugsnag\Request\ResolverInterface;
use Stefna\BugsnagBrefResolver\Events\HttpRequestEvent;

final class BrefResolver implements ResolverInterface
{
	/** @var array<class-string, class-string> */
	private const EVENT_MAP = [
		BrefHttpRequestEvent::class => HttpRequestEvent::class,
	];

	private ?LambdaEvent $event = null;
	/** @var array<string, RequestInterface> */
	private array $cachedRequest = [];

	public function setEvent(LambdaEvent $event): void
	{
		$this->event = $event;
	}

	public function resolve()
	{
		if (!$this->event) {
			return new NullRequest();
		}

		$eventId = spl_object_hash($this->event);
		if (isset($this->cachedRequest[$eventId])) {
			return $this->cachedRequest[$eventId];
		}

		foreach (self::EVENT_MAP as $eventType => $requestType) {
			if ($this->event instanceof $eventType) {
				return $this->cachedRequest[$eventId] =  new $requestType($this->event);
			}
		}

		return $this->cachedRequest[$eventId] = new NullRequest();
	}
}
