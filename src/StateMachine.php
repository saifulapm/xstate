<?php

namespace Mouadziani\XState;

use Closure;
use Mouadziani\XState\Exceptions\TransitionNotAllowedException;
use Mouadziani\XState\Exceptions\TransitionNotDefinedException;

class StateMachine
{
    public array $states;

    private array $transitions;

    public ?string $defaultState;

    private ?string $currentState = null;

    private ?Closure $beforeEachTransition = null;

    private ?Closure $afterEachTransition = null;

    public static function make(): self
    {
        return new static();
    }

    public function defaultState(string $default): self
    {
        $this->defaultState = $default;
        $this->currentState = $default;

        return $this;
    }

    public function states(array $states): self
    {
        $this->states = $states;

        return $this;
    }

    public function addState(string $state): self
    {
        $this->states[] = $state;

        return $this;
    }

    public function transitions(array $transitions): self
    {
        $this->transitions = $transitions;

        return $this;
    }

    public function addTransition(Transition $transition): self
    {
        $this->transitions[] = $transition;

        return $this;
    }

    public function beforeEachTransition(Closure $beforeEachTransition): self
    {
        $this->beforeEachTransition = $beforeEachTransition;

        return $this;
    }

    public function afterEachTransition(Closure $afterTransition): self
    {
        $this->afterTransition = $afterTransition;

        return $this;
    }

    public function currentState(): string
    {
        return $this->currentState;
    }

    public function transitionTo(string $trigger): self
    {
        $transition = $this->findTransition($trigger);

        if (! $transition) {
            throw new TransitionNotDefinedException('Transition not defined');
        }

        if (! in_array($trigger, $this->allowedTransitions())) {
            throw new TransitionNotAllowedException('Transition not allowed');
        }

        $transition->handle($this->currentState, $this->beforeEachTransition, $this->afterEachTransition);

        return $this;
    }

    public function canTransisteTo(string $trigger): bool
    {
        $transition = $this->findTransition($trigger);

        return $transition && in_array($trigger, $this->allowedTransitions());
    }

    public function allowedTransitions(): array
    {
        $allowedTransitions = array_filter($this->transitions, fn ($transition) =>
            in_array($this->currentState(), is_array($transition->from) ? $transition->from : [$transition->from])
        );

        return array_map(fn ($transition) => $transition->trigger, array_values($allowedTransitions));
    }

    public function __call(string $name, array $arguments)
    {
        if (! $this->findTransition(strtoupper($name))) {
            throw new TransitionNotDefinedException('Transition not defined');
        }

        $this->transitionTo(strtoupper($name));
    }

    private function findTransition(string $trigger): ?Transition
    {
        return array_values(
            array_filter($this->transitions, fn ($transition) => $transition->trigger === $trigger) ?? []
        )[0] ?? null;
    }
}
