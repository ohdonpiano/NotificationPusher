<?php
/**
 * Created by PhpStorm.
 * User: seyfer
 * Date: 10.08.17
 * Time: 10:30
 */

namespace Sly\NotificationPusher\Model;

use Sly\NotificationPusher\Collection\PushCollection;

/**
 * @author Oleg Abrazhaev <seyferseed@gmail.com>
 */
interface ResponseInterface
{
    /**
     * @param DeviceInterface $device
     * @param array $response
     */
    public function addParsedResponse(DeviceInterface $device, array $response);

    /**
     * @param DeviceInterface $device
     * @param mixed $originalResponse
     */
    public function addOriginalResponse(DeviceInterface $device, mixed $originalResponse);

    /**
     * @param PushInterface $push Push
     */
    public function addPush(PushInterface $push);

    /**
     * @return array
     */
    public function getParsedResponses(): array;

    /**
     * @return mixed
     */
    public function getOriginalResponses(): mixed;

    /**
     * @return PushCollection
     */
    public function getPushCollection(): PushCollection;
}
