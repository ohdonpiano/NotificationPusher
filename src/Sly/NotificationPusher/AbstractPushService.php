<?php
/**
 * Created by PhpStorm.
 * User: seyfer
 * Date: 10.08.17
 * Time: 11:06
 */

namespace Sly\NotificationPusher;


use Sly\NotificationPusher\Model\ResponseInterface;

/**
 * @author Oleg Abrazhaev <seyferseed@gmail.com>
 */
abstract class AbstractPushService
{
    /**
     * @var string
     */
    protected string $environment;

    /**
     * @var ?ResponseInterface
     */
    protected ?ResponseInterface $response = null;

    /**
     * @param string $environment
     */
    public function __construct(string $environment = PushManager::ENVIRONMENT_DEV)
    {
        $this->environment = $environment;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
