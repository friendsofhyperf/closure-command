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
    /**
     * @var ClosureCommand[]
     */
    protected static $commands = [];

    /**
     * @return ClosureCommand
     */
    public static function command(string $signature, Closure $callback)
    {
        $command = new ClosureCommand($signature, $callback);

        self::$commands[] = $command;

        return $command;
    }

    /**
     * @return ClosureCommand[]
     */
    public static function getCommands()
    {
        return self::$commands;
    }
}
