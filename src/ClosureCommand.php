<?php

declare(strict_types=1);
/**
 * This file is part of closure-command.
 *
 * @link     https://github.com/friendsofhyperf/closure-command
 * @document https://github.com/friendsofhyperf/closure-command/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace FriendsOfHyperf\ClosureCommand;

use Closure;
use Hyperf\Command\Command;

class ClosureCommand extends Command
{
    protected $callback;

    public function __construct(string $signature, Closure $callback)
    {
        $this->signature = $signature;
        $this->callback = $callback;

        parent::__construct();
    }

    public function handle()
    {
    }

    public function describe(string $description)
    {
        $this->setDescription($description);

        return $this;
    }
}
