<?php

declare(strict_types=1);

namespace Contributte\RabbitMQ\Queue;

use Contributte\RabbitMQ\Connection\ConnectionFactory;
use Contributte\RabbitMQ\Queue\Exception\QueueFactoryException;

final class QueueDeclarator
{

	private QueuesDataBag $queuesDataBag;
	private ConnectionFactory $connectionFactory;


	public function __construct(ConnectionFactory $connectionFactory, QueuesDataBag $queuesDataBag)
	{
		$this->queuesDataBag = $queuesDataBag;
		$this->connectionFactory = $connectionFactory;
	}


	public function declareQueue(string $name): void
	{
		try {
			$queueData = $this->queuesDataBag->getDataBykey($name);

		} catch (\InvalidArgumentException $e) {
			throw new QueueFactoryException("Queue [$name] does not exist");
		}

		$connection = $this->connectionFactory->getConnection($queueData['connection']);

		$connection->getChannel()->queueDeclare(
			$queueData['name'],
			$queueData['passive'],
			$queueData['durable'],
			$queueData['exclusive'],
			$queueData['autoDelete'],
			$queueData['noWait'],
			$queueData['arguments']
		);
	}
}
