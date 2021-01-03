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

use FriendsOfHyperf\ClosureCommand\Listener\RegisterCommandListener;

class ConfigProvider
{
    public function __invoke(): array
    {
        defined('BASE_PATH') or define('BASE_PATH', '');

        return [
            'dependencies' => [
                Console::class => Console::class,
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'commands' => [],
            'listeners' => [
                RegisterCommandListener::class,
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'config file.',
                    'source' => __DIR__ . '/../publish/console.php',
                    'destination' => BASE_PATH . '/config/console.php',
                ],
            ],
        ];
    }
}
