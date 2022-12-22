<?php

declare(strict_types=1);

namespace Contributte\RabbitMQ\DI\Helpers;

use Contributte\RabbitMQ\Queue\QueueDeclarator;
use Contributte\RabbitMQ\Queue\QueueFactory;
use Contributte\RabbitMQ\Queue\QueuesDataBag;
use Nette\DI\ContainerBuilder;
use Nette\DI\ServiceDefinition;

final class QueuesHelper extends AbstractHelper
{

	/**
	 * @var array
	 */
	protected array $defaults = [
		'name' => null,
		'connection' => 'default',
		'passive' => false,
		'durable' => true,
		'exclusive' => false,
		'autoDelete' => false,
		'noWait' => false,
		'arguments' => [],
		'autoCreate' => false,
	];


	public function setup(ContainerBuilder $builder, array $config = []): ServiceDefinition
	{
		$queuesConfig = [];

		foreach ($config as $queueName => $queueData) {
			$queueConfig = $this->extension->validateConfig(
				$this->getDefaults(),
				$queueData
			);

			if ($queueConfig['name'] === null) {
				$queueConfig['name'] = $queueName;
			}

			$queuesConfig[$queueName] = $queueConfig;
		}

		$queuesDataBag = $builder->addDefinition($this->extension->prefix('queuesDataBag'))
			->setFactory(QueuesDataBag::class)
			->setArguments([$queuesConfig]);

		$builder->addDefinition($this->extension->prefix('queueDeclarator'))
			->setFactory(QueueDeclarator::class);

		return $builder->addDefinition($this->extension->prefix('queueFactory'))
			->setFactory(QueueFactory::class)
			->setArguments([$queuesDataBag]);
	}

}
