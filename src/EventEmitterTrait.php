<?php declare(strict_types=1);

namespace Benit8\EventEmitter;

/**
 * @phpstan-type Node array{listeners: array{callable, bool}[], children: array<string, Node>}
 */
trait EventEmitterTrait
{
	/** @var Node */
	private array $tree = ['listeners' => [], 'children' => []];

	/**
	 * Listens for events.
	 *
	 * @param string   $event    An event name globbing pattern.
	 * @param callable $listener Callback on matching events.
	 *
	 * @return self
	 */
	public function on(string $event, callable $listener): self
	{
		$this->add($event, $listener, false);
		return $this;
	}

	/**
	 * Listens for a single event, removes itself after.
	 *
	 * @param string   $event    An event name globbing pattern.
	 * @param callable $listener Callback on matching events.
	 *
	 * @return self
	 */
	public function once(string $event, callable $listener): self
	{
		$this->add($event, $listener, true);
		return $this;
	}

	/**
	 * Emit an event to the listeners. The event name must be complete and
	 * not contains any wildcards.
	 *
	 * @param string  $event     Event name.
	 * @param mixed[] $arguments
	 *
	 * @return void
	 */
	public function emit(string $event, ...$arguments): void
	{
		$this->depthFirstSearch($event, static function (&$node) use ($arguments) {
			foreach ($node['listeners'] as $j => [$listener, $once]) {
				$listener(...$arguments);
				if ($once) {
					unset($node['listeners'][$j]);
				}
			}
			return true;
		});
	}

	/**
	 * Remove an event listener.
	 *
	 * @param string   $event    Event name pattern.
	 * @param callable $listener Listener to remove.
	 *
	 * @return void
	 */
	public function removeListener(string $event, callable $listener): void
	{
		$this->depthFirstSearch($event, static function (&$node) use ($listener) {
			foreach ($node['listeners'] as $j => [$l, $once]) {
				if ($l === $listener) {
					unset($node['listeners'][$j]);
					return false;
				}
			}
		});
	}

	/**
	 * Remove all or a subset of event listeners.
	 *
	 * @param string $event Event name pattern.
	 *
	 * @return void
	 */
	public function removeAllListeners(string $event = ''): void
	{
		$this->depthFirstSearch($event, static function (&$node) {
			$node['listeners'] = [];
			$node['children'] = [];
			return false;
		});
	}

	/**
	 * @internal
	 *
	 * @param string   $event    An event name globbing pattern.
	 * @param callable $listener Callback on matching events.
	 *
	 * @return void
	 */
	private function add(string $event, callable $listener, bool $once): void
	{
		$keys = array_filter(explode('.', $event));
		$node = &$this->tree;

		foreach ($keys as $i => $segment) {
			unset($keys[$i]);

			$node['children'][$segment] ??= ['listeners' => [], 'children' => []];
			$node = &$node['children'][$segment];
		}

		$node['listeners'][] = [$listener, $once];
	}

	/**
	 * @internal Depth first search into the nodes.
	 *
	 * @param string[]                   $event
	 * @param callable(Node $node): bool $cb   Return false to stop the
	 *        recursing.
	 *
	 * @return void
	 */
	private function depthFirstSearch(string $event, callable $cb): void
	{
		$path = array_filter(explode('.', $event));
		$node = &$this->tree;

		$this->depthFirstSearchRecurse($node, $path, $cb);
	}

	/**
	 * @internal
	 *
	 * @param Node                       $node
	 * @param string[]                   $path
	 * @param callable(Node $node): bool $cb
	 *
	 * @return bool
	 */
	private function depthFirstSearchRecurse(array &$node, array $path, callable $cb): bool
	{
		$key = array_shift($path);
		if ($key !== null && isset($node['children'][$key])) {
			$continue = $this->depthFirstSearchRecurse($node['children'][$key], $path, $cb);

			if (empty($node['children'][$key]['children']) && empty($node['children'][$key]['listeners'])) {
				unset($node['children'][$key]);
			}

			if (!$continue) {
				return false;
			}
		}

		return $cb($node);
	}
}
