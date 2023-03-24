<?php declare(strict_types=1);

namespace FlareScoreboard;
use FlareScoreboard\HapiRecord;

/**
 * Interface for iterating over data returned from a HAPI server
 */
class HapiIterator implements \Countable, \Iterator, \ArrayAccess
{
    private array $data;
    private int $position;
    private string $method;

    public function __construct(array &$data, string &$method)
    {
        $this->data = &$data;
        $this->position = 0;
        $this->method = $method;
    }

    /**
     * Returns the parameters available on the records returned by this iterator
     */
    public function getParameters(): array
    {
        return array_column($this->data['parameters'], 'name');
    }

    public function count(): int
    {
        return count($this->data['data']);
    }

    public function current(): HapiRecord
    {
        return new HapiRecord($this->data['data'][$this->position], $this->data['parameters'], $this->method);
    }

    public function key(): mixed
    {
        return $this->position;
    }

    public function next(): void
    {
        $this->position++;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return $this->position < count($this);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $offset >= 0 && $offset < count($this);
    }

    public function offsetGet(mixed $offset): HapiRecord
    {
        return new HapiRecord($this->data['data'][$offset], $this->data['parameters'], $this->method);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \Exception("HapiIterator is read-only");
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \Exception("HapiIterator is read-only");
    }
}