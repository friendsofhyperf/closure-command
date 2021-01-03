<?php

declare(strict_types=1);
/**
 * This file is part of closure-command.
 *
 * @link     https://github.com/friendsofhyperf/closure-command
 * @document https://github.com/friendsofhyperf/closure-command/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace FriendsOfHyperf\ClosureCommand\Listener;

use FriendsOfHyperf\ClosureCommand\Console;
use Hyperf\Contract\ApplicationInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Utils\ApplicationContext;
use Symfony\Component\Console\Application;

class RegisterCommandListener implements ListenerInterface
{
    protected $routes = [BASE_PATH . '/config/console.php'];

    /**
     * @return string[] returns the events that you want to listen
     */
    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    /**
     * Handle the Event when the event is triggered, all listeners will
     * complete before the event is returned to the EventDispatcher.
     */
    public function process(object $event)
    {
        foreach ($this->routes as $route) {
            if (file_exists($route)) {
                require_once $route;
            }
        }

        if (ApplicationContext::hasContainer()) {
            $container = ApplicationContext::getContainer();
            /** @var Application $application */
            $application = $container->get(ApplicationInterface::class);
            $commands = Console::all();

            foreach ($commands as $command) {
                $application->add($command);
            }
        }
    }
}
