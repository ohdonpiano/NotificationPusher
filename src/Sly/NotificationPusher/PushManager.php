<?php

/*
 * This file is part of NotificationPusher.
 *
 * (c) 2013 Cédric Dugat <cedric@dugat.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sly\NotificationPusher;

use Exception;
use Sly\NotificationPusher\Adapter\AdapterInterface;
use Sly\NotificationPusher\Adapter\FeedbackAdapterInterface;
use Sly\NotificationPusher\Collection\PushCollection;
use Sly\NotificationPusher\Exception\AdapterException;
use Sly\NotificationPusher\Model\Push;
use Sly\NotificationPusher\Model\PushInterface;
use Sly\NotificationPusher\Model\ResponseInterface;

/**
 * @uses PushCollection
 * @author Cédric Dugat <cedric@dugat.me>
 */
class PushManager
{
    const ENVIRONMENT_DEV = 'dev';
    const ENVIRONMENT_PROD = 'prod';

    /**
     * @var string
     */
    private string $environment;

    /**
     * @var PushCollection
     */
    private PushCollection $pushCollection;

    /**
     * @var ResponseInterface
     */
    private ResponseInterface $response;

    /**
     * @param string $environment Environment
     */
    public function __construct(string $environment = self::ENVIRONMENT_DEV)
    {
        $this->environment = $environment;
        $this->pushCollection = new PushCollection();
    }

    /**
     * @param PushInterface $push Push
     */
    public function add(PushInterface $push): void
    {
        $this->pushCollection->add($push);
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * @return PushCollection
     * @throws Exception
     */
    public function push(): PushCollection
    {
        /** @var Push $push */
        foreach ($this->pushCollection as $push) {
            $adapter = $push->getAdapter();
            $adapter->setEnvironment($this->getEnvironment());

            if ($adapter->push($push)) {
                $push->pushed();
            }
        }

        if (!$this->pushCollection->isEmpty()) {
            /** @var Push $push */
            $push = $this->pushCollection->first();
            $this->response = $push->getAdapter()->getResponse();
        }

        return $this->pushCollection;
    }

    /**
     * @param AdapterInterface $adapter Adapter
     *
     * @return array
     *
     * @throws AdapterException When the adapter has no dedicated `getFeedback` method
     */
    public function getFeedback(AdapterInterface $adapter): array
    {
        if (!$adapter instanceof FeedbackAdapterInterface) {
            throw new AdapterException(
                sprintf(
                    '%s adapter has no dedicated "getFeedback" method',
                    $adapter
                )
            );
        }
        $adapter->setEnvironment($this->getEnvironment());

        return $adapter->getFeedback();
    }

    /**
     * @return PushCollection
     */
    public function getPushCollection(): PushCollection
    {
        return $this->pushCollection;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
