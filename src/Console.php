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

class Console
{
    protected static $commands = [];

    public static function command(string $signature, Closure $callback)
    {
        $command = new ClosureCommand($signature, $callback);

        self::$commands[] = $command;

        return $command;
    }

    public static function all()
    {
        return self::$commands;
    }
}
