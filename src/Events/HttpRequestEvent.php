<?php declare(strict_types=1);

namespace Stefna\BugsnagBrefResolver\Events;

use Bref\Event\Http\HttpRequestEvent as BrefHttpRequestEvent;
use Bugsnag\Request\RequestInterface;

final class HttpRequestEvent implements RequestInterface
{
	public function __construct(
		private BrefHttpRequestEvent $event,
	) {}

	/**
	 * Are we currently processing a request?
	 */
	public function isRequest(): bool
	{
		return true;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function getSession(): array
	{
		return [];
	}

	/**
	 * @return array<string, mixed>
	 */
	public function getCookies(): array
	{
		return $this->event->getCookies();
	}

	/**
	 * Get the request formatted as metadata.
	 *
	 * @return array<string, mixed>
	 */
	public function getMetaData(): array
	{
		$data = [];
		$data['url'] = $this->getCurrentUrl();
		$data['httpMethod'] = $this->event->getMethod();

		if ($this->event->getMethod() === 'GET') {
			$data['params'] = $this->event->getQueryParameters();
		}
		else {
			$data['params'] = $this->event->getBody();
		}

		$requestContext = $this->event->getRequestContext();
		if (isset($requestContext['identity']['sourceIp'])) {
			$data['clientIp'] = $requestContext['identity']['sourceIp'];
		}

		if (isset($requestContext['identity']['userAgent'])) {
			$data['userAgent'] = $requestContext['identity']['userAgent'];
		}

		if ($this->event->getHeaders()) {
			$data['headers'] = $this->event->getHeaders();
		}

		return ['request' => $data];
	}

	public function getContext(): ?string
	{
		return $this->event->getMethod() . ' ' . $this->event->getPath();
	}

	/**
	 * Get the request user id.
	 *
	 * @return string|null
	 */
	public function getUserId(): ?string
	{
		return $this->getRequestIp();
	}

	protected function getCurrentUrl(): string
	{
		$schema = 'https://';
		$host = $this->event->getServerName();

		$requestContext = $this->event->getRequestContext();
		$path = $requestContext['path'] ?? $this->event->getPath();

		return $schema . $host . $path;
	}

	protected function getRequestIp(): ?string
	{
		$requestContext = $this->event->getRequestContext();
		return $requestContext['identity']['sourceIp'] ?? null;
	}
}
