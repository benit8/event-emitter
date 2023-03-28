<?php declare(strict_types=1);

namespace Benit8\EventEmitter;

interface EventEmitterInterface
{
	/**
	 * Listens for events.
	 *
	 * @param string   $event    An event name globbing pattern.
	 * @param callable $listener Callback on matching events.
	 *
	 * @return self
	 */
	public function on(string $event, callable $listener): self;

	/**
	 * Listens for a single event, removes itself after.
	 *
	 * @param string   $event    An event name globbing pattern.
	 * @param callable $listener Callback on matching events.
	 *
	 * @return self
	 */
	public function once(string $event, callable $listener): self;

	/**
	 * Emit an event to the listeners. The event name must be complete and
	 * not contains any wildcards.
	 *
	 * @param string  $event     Event name.
	 * @param mixed[] $arguments
	 *
	 * @return bool Whether the event was handled or not.
	 */
	public function emit(string $event, ...$arguments): bool;

	/**
	 * Remove an event listener.
	 *
	 * @param string   $event    Event name pattern.
	 * @param callable $listener Listener to remove.
	 *
	 * @return void
	 */
	public function removeListener(string $event, callable $listener): void;

	/**
	 * Remove all or a subset of event listeners.
	 *
	 * @param string $event Event name pattern.
	 *
	 * @return void
	 */
	public function removeAllListeners(string $event = ''): void;
}
