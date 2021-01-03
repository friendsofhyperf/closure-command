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

if (! function_exists('FriendsOfHyperf\ClosureCommand\command')) {
    /**
     * @return ClosureCommand
     */
    function command(string $signature, Closure $callback)
    {
        return Console::command($signature, $callback);
    }
}

if (! function_exists('FriendsOfHyperf\ClosureCommand\commands')) {
    /**
     * @return ClosureCommand[]
     */
    function commands()
    {
        return Console::getCommands();
    }
}
