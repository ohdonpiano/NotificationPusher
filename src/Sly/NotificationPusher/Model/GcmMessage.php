<?php

namespace Sly\NotificationPusher\Model;


class GcmMessage extends Message
{
    /**
     * @var array
     */
    private array $notificationData = [];

    /**
     * @return array
     */
    public function getNotificationData(): array
    {
        return $this->notificationData;
    }

    /**
     * @param array $notificationData
     * @noinspection PhpUnused
     */
    public function setNotificationData(array $notificationData)
    {
        $this->notificationData = $notificationData;
    }
}
