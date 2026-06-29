# Semaphore

A simple, dependency-free PHP semaphore based on a lock file.

Use it to prevent concurrent execution of a script — for example, to stop a cron job from overlapping with a previous run that hasn't finished yet.

## Installation

```bash
composer require dimarti/semaphore
```

Requires PHP 7.0+ (uses scalar type hints and `void` return types).

## Usage

```php
use Dimarti\Semaphore\Semaphore;

$semaphore = new Semaphore('/tmp/my-task.lock');

if ($semaphore->isLocked()) {
    exit("Another instance is already running.\n");
}

$semaphore->lock();

try {
    // ... do your work ...
} finally {
    $semaphore->unlock();
}
```

## API

| Method | Description |
| --- | --- |
| `__construct(string $lockfile)` | Creates a semaphore bound to the given lock file path. |
| `lock(): void` | Creates the lock file and writes the current timestamp (`Y-m-d H:i:s`) into it. |
| `unlock(): void` | Removes the lock file. |
| `isLocked(): bool` | Returns `true` if the lock file exists, `false` otherwise. |

## How it works

The lock state is represented by the presence of a single file on disk:

- `lock()` writes the file (with a timestamp as its contents).
- `unlock()` deletes the file.
- `isLocked()` checks whether the file exists.

## Caveats

This is intentionally minimal. Be aware of the following before using it in critical paths:

- **Not atomic.** Checking `isLocked()` and then calling `lock()` is a two-step operation. Two processes can pass the check at the same time and both acquire the lock. If you need a true atomic guarantee, use `fopen($file, 'x')` or `flock()` instead.
- **Stale locks.** If a process dies before calling `unlock()`, the lock file remains and the task stays blocked forever. The timestamp written by `lock()` is never read back, so there is no automatic expiration.
- **`unlock()` assumes the file exists.** Calling it when the lock file is absent triggers an `unlink()` warning.

## License

[MIT](LICENSE)