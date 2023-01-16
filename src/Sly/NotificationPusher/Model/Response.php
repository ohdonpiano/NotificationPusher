<?php
/**
 * Created by PhpStorm.
 * User: seyfer
 * Date: 09.08.17
 * Time: 17:57
 */

namespace Sly\NotificationPusher\Model;


use InvalidArgumentException;
use Sly\NotificationPusher\Collection\PushCollection;

/**
 * @author Oleg Abrazhaev <seyferseed@gmail.com>
 */
class Response implements ResponseInterface
{
    /**
     * @var array
     */
    private array $parsedResponses = [];

    /**
     * @var array
     */
    private array $originalResponses = [];

    /**
     * @var PushCollection
     */
    private PushCollection $pushCollection;

    public function __construct()
    {
        $this->pushCollection = new PushCollection();
    }

    /**
     * @param DeviceInterface $device
     * @param mixed $response
     */
    public function addParsedResponse(DeviceInterface $device, mixed $response)
    {
        if (!is_array($response)) {
            throw new InvalidArgumentException('Response must be array type');
        }

        $this->parsedResponses[$device->getToken()] = $response;
    }

    /**
     * @param DeviceInterface $device
     * @param mixed $originalResponse
     */
    public function addOriginalResponse(DeviceInterface $device, mixed $originalResponse)
    {
        $this->originalResponses[$device->getToken()] = $originalResponse;
    }

    /**
     * @param PushInterface $push Push
     */
    public function addPush(PushInterface $push)
    {
        $this->pushCollection->add($push);
    }

    /**
     * @return array
     */
    public function getParsedResponses(): array
    {
        return $this->parsedResponses;
    }

    /**
     * @return mixed
     */
    public function getOriginalResponses(): array
    {
        return $this->originalResponses;
    }

    /**
     * @return PushCollection
     */
    public function getPushCollection(): PushCollection
    {
        return $this->pushCollection;
    }
}
