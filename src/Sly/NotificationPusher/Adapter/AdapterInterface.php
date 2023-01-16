<?php

/*
 * This file is part of NotificationPusher.
 *
 * (c) 2013 Cédric Dugat <cedric@dugat.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sly\NotificationPusher\Adapter;

use Sly\NotificationPusher\Collection\DeviceCollection;
use Sly\NotificationPusher\Model\PushInterface;
use Sly\NotificationPusher\Model\ResponseInterface;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
interface AdapterInterface
{
    /**
     * @param PushInterface $push Push
     *
     * @return DeviceCollection
     */
    public function push(PushInterface $push): DeviceCollection;

    /**
     * @param string $token Token
     *
     * @return boolean
     */
    public function supports(string $token): bool;

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface;

    /**
     * @param ResponseInterface $response
     */
    public function setResponse(ResponseInterface $response);

    /**
     * @return array
     */
    public function getDefinedParameters(): array;

    /**
     * @return array
     */
    public function getDefaultParameters(): array;

    /**
     * @return array
     */
    public function getRequiredParameters(): array;

    /**
     * @return string
     */
    public function getEnvironment(): string;

    /**
     * @param string $environment Environment value to set
     *
     * @return AdapterInterface
     */
    public function setEnvironment(string $environment): AdapterInterface;
}
