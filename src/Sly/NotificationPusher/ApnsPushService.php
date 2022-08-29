<?php
/**
 * Created by PhpStorm.
 * User: seyfer
 * Date: 10.08.17
 * Time: 10:43
 */

namespace Sly\NotificationPusher;

use Sly\NotificationPusher\Adapter\Apns as ApnsAdapter;
use Sly\NotificationPusher\Collection\DeviceCollection;
use Sly\NotificationPusher\Model\Device;
use Sly\NotificationPusher\Model\Message;
use Sly\NotificationPusher\Model\Push;
use Sly\NotificationPusher\Model\ResponseInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @package Sly\NotificationPusher
 * @author Oleg Abrazhaev <seyferseed@gmail.com>
 */
class ApnsPushService extends AbstractPushService
{
    /**
     * @var string
     */
    private $certificatePath = '';

    /**
     * @var string|null
     */
    private $passPhrase = '';

    /**
     * IOSPushNotificationService constructor.
     * @param string $environment
     * @param string $certificatePath
     * @param string $passPhrase
     */
    public function __construct($certificatePath, $passPhrase = null, $environment = PushManager::ENVIRONMENT_DEV)
    {
        parent::__construct($environment);

        $this->certificatePath = $certificatePath;
        $this->passPhrase = $passPhrase;
    }

    /**
     * @param array $tokens List of targets
     * @param array $notifications Message(s) to send to each token
     * @param array $params
     * @return ResponseInterface
     */
    public function push(array $tokens = [], array $notifications = [], array $params = [])
    {
        if (!$tokens || !$notifications) {
            return null;
        }

        if (!$this->certificatePath) {
            throw new \RuntimeException('IOS certificate path must be set');
        }

        $fs = new Filesystem();
        if (!$fs->exists($this->certificatePath) || !is_readable($this->certificatePath)) {
            throw new \InvalidArgumentException('Wrong or not readable certificate path');
        }

        $adapterParams = [];
        $deviceParams = [];
        $messageParams = [];
        if (isset($params) && !empty($params)) {
            if (isset($params['adapter'])) {
                $adapterParams = $params['adapter'];
            }

            if (isset($params['device'])) {
                $deviceParams = $params['device'];
            }

            if (isset($params['message'])) {
                $messageParams = $params['message'];
            }
        }

        $adapterParams['certificate'] = $this->certificatePath;
        $adapterParams['passPhrase'] = $this->passPhrase;

        // Development one by default (without argument).
        $pushManager = new PushManager($this->environment);

        // Then declare an adapter.
        $apnsAdapter = new ApnsAdapter($adapterParams);

        // Set the device(s) to push the notification to.
        $devices = new DeviceCollection([]);

        //devices
        foreach ($tokens as $token) {
            $devices->add(new Device($token, $deviceParams));
        }

        foreach ($notifications as $notificationText) {
            // Then, create the push skel.
            $message = new Message($notificationText, $messageParams);

            // Finally, create and add the push to the manager, and push it!
            $push = new Push($apnsAdapter, $devices, $message);
            $pushManager->add($push);
        }

        // Returns a collection of notified devices
        $pushes = $pushManager->push();

        $this->response = $apnsAdapter->getResponse();

        return $this->response;
    }

    /**
     * @return array
     */
    public function getInvalidTokens()
    {
        if (!$this->response) {
            return [];
	}
	$tokens = [];
        foreach ($this->response->getParsedResponses() as $token => $response) { 
            if (array_key_exists('token', $response) && $response['token'] === ServiceResponse::RESULT_INVALID_TOKEN) {
                $tokens[] = $token;
            }
        }
 
        return $tokens;
    }

    /**
     * @return array
     */
    public function getSuccessfulTokens()
    {
        if (!$this->response) {
            return [];
        }
        $tokens = [];
        foreach ($this->response->getParsedResponses() as $token => $response) { 
            if (array_key_exists('token', $response) && $response['token'] === ServiceResponse::RESULT_OK) {
                $tokens[] = $token;
            }
        }
 
        return $tokens;
    }
}
