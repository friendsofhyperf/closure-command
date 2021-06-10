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
use Hyperf\Command\Event\AfterExecute;
use Hyperf\Command\Event\AfterHandle;
use Hyperf\Command\Event\BeforeHandle;
use Hyperf\Command\Event\FailToHandle;
use Hyperf\Contract\NormalizerInterface;
use Hyperf\Di\ClosureDefinitionCollectorInterface;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Coroutine;
use InvalidArgumentException as GlobalInvalidArgumentException;
use Psr\Container\ContainerInterface;
use RuntimeException;
use Swoole\ExitException;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use TypeError;

class ClosureCommand extends Command
{
    protected $callback;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @var ClosureDefinitionCollectorInterface
     */
    private $closureDefinitionCollector;

    public function __construct(string $signature, Closure $callback)
    {
        $this->signature = $signature;
        $this->callback = $callback;

        $this->container = ApplicationContext::getContainer();
        $this->normalizer = $this->container->get(NormalizerInterface::class);
        if ($this->container->has(ClosureDefinitionCollectorInterface::class)) {
            $this->closureDefinitionCollector = $this->container->get(ClosureDefinitionCollectorInterface::class);
        }

        parent::__construct();
    }

    public function handle()
    {
    }

    /**
     * @return $this
     */
    public function describe(string $description)
    {
        $this->setDescription($description);

        return $this;
    }

    public function getClosureDefinitionCollector(): ClosureDefinitionCollectorInterface
    {
        return $this->closureDefinitionCollector;
    }

    public function getNormalizer(): NormalizerInterface
    {
        return $this->normalizer;
    }

    /**
     * @throws InvalidArgumentException
     * @throws TypeError
     * @throws RuntimeException
     * @throws Throwable
     * @throws Throwable
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->enableDispatcher($input);

        $callback = function () use ($input) {
            try {
                $this->eventDispatcher && $this->eventDispatcher->dispatch(new BeforeHandle($this));

                $inputs = array_merge($input->getArguments(), $input->getOptions());
                $parameters = $this->parseClosureParameters($this->callback, $inputs);

                \call($this->callback->bindTo($this, $this), $parameters);

                $this->eventDispatcher && $this->eventDispatcher->dispatch(new AfterHandle($this));
            } catch (ExitException $exception) {
                // Do nothing.
            } catch (\Throwable $exception) {
                if (! $this->eventDispatcher) {
                    throw $exception;
                }

                $this->eventDispatcher->dispatch(new FailToHandle($this, $exception));

                return $exception->getCode();
            } finally {
                $this->eventDispatcher && $this->eventDispatcher->dispatch(new AfterExecute($this));
            }

            return 0;
        };

        if ($this->coroutine && ! Coroutine::inCoroutine()) {
            run($callback, $this->hookFlags);
            return 0;
        }

        return $callback();
    }

    /**
     * @throws GlobalInvalidArgumentException
     */
    protected function parseClosureParameters(Closure $closure, array $arguments): array
    {
        if (! $this->container->has(ClosureDefinitionCollectorInterface::class)) {
            return [];
        }

        $definitions = $this->getClosureDefinitionCollector()->getParameters($closure);

        return $this->getInjections($definitions, 'Closure', $arguments);
    }

    /**
     * @throws GlobalInvalidArgumentException
     */
    private function getInjections(array $definitions, string $callableName, array $arguments): array
    {
        $injections = [];

        foreach ($definitions ?? [] as $pos => $definition) {
            $value = $arguments[$pos] ?? $arguments[$definition->getMeta('name')] ?? null;
            if ($value === null) {
                if ($definition->getMeta('defaultValueAvailable')) {
                    $injections[] = $definition->getMeta('defaultValue');
                } elseif ($definition->allowsNull()) {
                    $injections[] = null;
                } elseif ($this->container->has($definition->getName())) {
                    $injections[] = $this->container->get($definition->getName());
                } else {
                    throw new \InvalidArgumentException("Parameter '{$definition->getMeta('name')}' "
                        . "of {$callableName} should not be null");
                }
            } else {
                $injections[] = $this->getNormalizer()->denormalize($value, $definition->getName());
            }
        }

        return $injections;
    }
}
