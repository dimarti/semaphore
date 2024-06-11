<?php

namespace Dimarti\Semaphore;

class Semaphore
{
    private $lockfile;

    public function __construct(string $lockfile)
    {
        $this->lockfile = $lockfile;
    }

    public function isLocked(): bool
    {
        return file_exists($this->lockfile);
    }

    public function unlock(): void
    {
        unlink($this->lockfile);
    }

    public function lock(): void
    {
        file_put_contents($this->lockfile, date('Y-m-d H:i:s'));
    }
}
