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
use Sly\NotificationPusher\Exception\AdapterException;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class Push extends BaseOptionedModel implements PushInterface
{

    /**
     * @var string
     */
    private string $status;

    /**
     * @var AdapterInterface
     */
    private AdapterInterface $adapter;

    /**
     * @var MessageInterface
     */
    private MessageInterface $message;

    /**
     * @var DeviceCollection
     */
    private DeviceCollection $devices;

    /**
     * @var DateTime
     */
    private DateTime $pushedAt;

    /**
     * @var ?ResponseCollection
     */
    private ?ResponseCollection $responses = null;

    /**
     * @param AdapterInterface $adapter Adapter
     * @param DeviceCollection|DeviceInterface $devices Device(s)
     * @param MessageInterface $message Message
     * @param array $options Options
     *
     * Options are adapters specific ones, like Apns "badge" or "sound" option for example.
     * Of course, they can be more general.
     *
     * @throws AdapterException
     */
    public function __construct(AdapterInterface $adapter, DeviceInterface|DeviceCollection $devices, MessageInterface $message, array $options = [])
    {
        if ($devices instanceof DeviceInterface) {
            $devices = new DeviceCollection([$devices]);
        }

        $this->adapter = $adapter;
        $this->devices = $devices;
        $this->message = $message;
        $this->options = $options;
        $this->status = self::STATUS_PENDING;

        $this->checkDevicesTokens();
    }

    /**
     * @throws AdapterException
     */
    private function checkDevicesTokens()
    {
        $devices = $this->getDevices();
        $adapter = $this->getAdapter();

        foreach ($devices as $device) {
            if (false === $adapter->supports($device->getToken())) {
                throw new AdapterException(
                    sprintf(
                        'Adapter %s does not support %s token\'s device',
                        $adapter,
                        $device->getToken()
                    )
                );
            }
        }
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status Status
     *
     * @return PushInterface
     */
    public function setStatus(string $status): PushInterface
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPushed(): bool
    {
        return self::STATUS_PUSHED === $this->status;
    }

    /**
     * @return PushInterface
     */
    public function pushed(): PushInterface
    {
        $this->status = self::STATUS_PUSHED;
        $this->pushedAt = new DateTime();

        return $this;
    }

    /**
     * @return AdapterInterface
     */
    public function getAdapter(): AdapterInterface
    {
        return $this->adapter;
    }

    /**
     * @param AdapterInterface $adapter Adapter
     *
     * @return PushInterface
     */
    public function setAdapter(AdapterInterface $adapter): PushInterface
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * @return MessageInterface
     */
    public function getMessage(): MessageInterface
    {
        return $this->message;
    }

    /**
     * @param MessageInterface $message Message
     *
     * @return PushInterface
     */
    public function setMessage(MessageInterface $message): PushInterface
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return DeviceCollection
     */
    public function getDevices(): DeviceCollection
    {
        return $this->devices;
    }

    /**
     * @param DeviceCollection $devices Devices
     *
     * @return PushInterface
     */
    public function setDevices(DeviceCollection $devices): PushInterface
    {
        $this->devices = $devices;

        $this->checkDevicesTokens();

        return $this;
    }

    /**
     * @return ResponseCollection
     */
    public function getResponses(): ResponseCollection
    {
        if (!$this->responses)
            $this->responses = new ResponseCollection();

        return $this->responses;
    }

    /**
     * @param DeviceInterface $device
     * @param mixed $response
     */
    public function addResponse(DeviceInterface $device, mixed $response)
    {
        $this->getResponses()->add($device->getToken(), $response);
    }

    /**
     * @return DateTime
     */
    public function getPushedAt(): DateTime
    {
        return $this->pushedAt;
    }

    /**
     * @param DateTime $pushedAt PushedAt
     *
     * @return PushInterface
     */
    public function setPushedAt(DateTime $pushedAt): PushInterface
    {
        $this->pushedAt = $pushedAt;

        return $this;
    }
}
