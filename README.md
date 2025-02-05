<p align="center">
    <img src="/art/logo.png" alt="xstate php logo"/>
</p>

## XState - State Machine for PHP

XState is a [state machine](https://statecharts.dev/what-is-a-state-machine.html) library to play with any complex behavior of your PHP objects (inspired from [xstate.js](https://github.com/statelyai/xstate))

### Installation

The recommended way to install Xstate is through
[Composer](https://getcomposer.org/).

```bash
composer require mouadziani/xstate
```

### Define state machine workflow

<p align="center">
    <img height="400px" src="/art/diagram.png" alt="Video state machine diagram"/>
</p>

Let's say we want to define a state machine workflow for a video object, generally a video may have 3 states (playing, stopped, paused),

as a first step you have to create a new object from `StateMachine` class

```php
use \Mouadziani\XState\StateMachine;

$video = StateMachine::make();
```

Then you have to define the allowed states as well as the default state

```php
$video
    ->defaultState('stopped')
    ->states(['playing', 'stopped', 'paused']);
```

And finally the transitions

```php
use \Mouadziani\XState\Transition;

$video->transitions([
    new Transition('PLAY', ['stopped', 'paused'], 'playing'),
    new Transition('STOP', 'playing', 'stopped'),
    new Transition('PAUSE', 'playing', 'paused'),
    new Transition('RESUME', 'paused', 'playing'),
]);
```

The `Transition` class expect 3 required params:

- **Trigger**: As a name of the transition which will be used to trigger a specific transition *(should be unique)*
- **From**: Expect a string for a single / or array for multiple initial allowed states
- **To**: Expect string which is the next target state *(should match one of the defined allowed states)*

#### Guards (optional)

You can either define a guard callback for a specific transition using `guard` method, which must return a bool. If a guard returns false, the transition cannot be performed.

```php
use \Mouadziani\XState\Transition;

$video->transitions([
    (new Transition('PLAY', ['stopped', 'paused'], 'playing'))
        ->guard(function ($from, $to) {
            return true;
        })
]);
```


### 💡 You can define the whole workflow with a single statement:

```php 
$video = StateMachine::make()
    ->defaultState('playing')
    ->states(['playing', 'stopped', 'paused'])
    ->transitions([
        new Transition('PLAY', ['stopped', 'paused'], 'playing'),
        new Transition('STOP', 'playing', 'stopped'),
        new Transition('PAUSE', 'playing', 'paused'),
    ]);
```

### Work with states & transitions

#### Trigger transition
There are two ways to trigger a specific defined transition

1- Using `transitionTo` method and specify the name of the transition as an argument

```php
$video->transitionTo('PLAY');
```

2- Or just calling the name of the transition from your machine object as a method

```php
$video->play();
```

Occasionally triggering a transition may throw an exception if the target transition is not defined /or not allowed:

```php
use \Mouadziani\XState\Exceptions;

try {
    $video->transitionTo('RESUME');
} catch (Exceptions\TransitionNotDefinedException $ex) {
    // the target transition is not defined
} catch (Exceptions\TransitionNotAllowedException $ex) {
    // the target transition is not allowed
}
```

#### Get the current state

```php
echo $video->currentState(); // playing
```

#### Get the allowed transitions

```php
$video->allowedTransitions(); // ['STOP', 'PAUSE']
```

#### Adding in-demand transition

```php
$video->addTransition(new Transition('TURN_OFF', 'playing', 'stopped'));
```

## Upcoming features

- [x] Add the ability to define guard for a specific transition
- [ ] Define/handle hooks before/after triggering transition


## Testing

```bash
composer test
```

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Mouad Ziani](https://github.com/mouadziani)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

featured_repository
