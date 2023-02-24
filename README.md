# Event emitter

An event emitter that supports namespaces.

## Installing

```shell
$ composer require benit8/event-emitter
```

## Usage

You can either extend the `EventEmitter` class or use the `EventEmitterTrait`. An interface
`EventEmitterInterface` is also available.

```php
use Benit8\EventEmitter\EventEmitterTrait;

class MyReactor
{
	use EventEmitterTrait;
}
```

### Reference

#### Adding listeners

```php
$ev->on('user.created', function ($user) {
	// Fired every time a 'user.created' event is emitted
});

$ev->on('user', function ($user) {
	// Listening to all 'user.*' events
});
```

#### Adding one-shot listeners

```php
$ev->once('user.created', function ($user) {
	// Will remove itself after firing
});
```

#### Emitting events

```php
$user = new User(/* ... */);

// Will trigger 'user.created' and 'user' listeners
$ev->emit('user.created', $user);
```

#### Removing a listener

```php
$ev->removeListener('user.created', $myCallable);
```

#### Removing all listeners

```php
// All listeners
$ev->removeAllListeners();

// Subset listeners
$ev->removeAllListeners('user');
```
