<?php

/*
 * This file is part of NotificationPusher.
 *
 * (c) 2013 Cédric Dugat <cedric@dugat.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sly\NotificationPusher\Model;

use DateTime;
use Sly\NotificationPusher\Adapter\AdapterInterface;
use Sly\NotificationPusher\Collection\DeviceCollection;
use Sly\NotificationPusher\Collection\ResponseCollection;

interface PushInterface
{
    /**
     * Constants define available statuses
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PUSHED = 'sent';

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @param string $status Status
     *
     * @return PushInterface
     */
    public function setStatus(string $status): PushInterface;

    /**
     * @return boolean
     */
    public function isPushed(): bool;

    /**
     * @return PushInterface
     */
    public function pushed(): PushInterface;

    /**
     * @return AdapterInterface
     */
    public function getAdapter(): AdapterInterface;

    /**
     * @param AdapterInterface $adapter Adapter
     *
     * @return PushInterface
     */
    public function setAdapter(AdapterInterface $adapter): PushInterface;

    /**
     * @return MessageInterface
     */
    public function getMessage(): MessageInterface;

    /**
     * @param MessageInterface $message Message
     *
     * @return PushInterface
     */
    public function setMessage(MessageInterface $message): PushInterface;

    /**
     * @return DeviceCollection
     */
    public function getDevices(): DeviceCollection;

    /**
     * @param DeviceCollection $devices Devices
     *
     * @return PushInterface
     */
    public function setDevices(DeviceCollection $devices): PushInterface;

    /**
     * @return ResponseCollection
     */
    public function getResponses(): ResponseCollection;

    /**
     * @param DeviceInterface $device
     * @param mixed $response
     */
    public function addResponse(DeviceInterface $device, mixed $response);

    /**
     * @return DateTime
     */
    public function getPushedAt(): DateTime;

    /**
     * @param DateTime $pushedAt PushedAt
     *
     * @return PushInterface
     */
    public function setPushedAt(DateTime $pushedAt): PushInterface;
}
