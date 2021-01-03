<?php

declare(strict_types=1);
/**
 * This file is part of closure-command.
 *
 * @link     https://github.com/friendsofhyperf/closure-command
 * @document https://github.com/friendsofhyperf/closure-command/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace FriendsOfHyperf\ClosureCommand\Aspect;

use FriendsOfHyperf\ClosureCommand\Console;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Symfony\Component\Console\Application;

/**
 * @Aspect
 */
class BootApplicationAspect extends AbstractAspect
{
    public $classes = [
        'Hyperf\Framework\ApplicationFactory::__invoke',
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        return tap($proceedingJoinPoint->process(), function ($application) {
            $route = BASE_PATH . '/config/console.php';

            if (file_exists($route)) {
                require_once $route;

                /** @var Application $application */
                $commands = Console::all();

                foreach ($commands as $command) {
                    $application->add($command);
                }
            }
        });
    }
}
