<?php /** @noinspection PhpUnusedFieldDefaultValueInspection */
/** @noinspection PhpUnused */

/**
 * Created by PhpStorm.
 * User: seyfer
 * Date: 10.08.17
 * Time: 10:43
 */

namespace Sly\NotificationPusher;

use InvalidArgumentException;
use RuntimeException;
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
    private string $certificatePath = '';

    /**
     * @var string|null
     */
    private ?string $passPhrase = '';

    /**
     * @var array
     */
    private array $feedback = [];

    /**
     * IOSPushNotificationService constructor.
     * @param string $environment
     * @param string $certificatePath
     * @param string|null $passPhrase
     */
    public function __construct(string $certificatePath, string $passPhrase = null, string $environment = PushManager::ENVIRONMENT_DEV)
    {
        parent::__construct($environment);

        $this->certificatePath = $certificatePath;
        $this->passPhrase = $passPhrase;
    }

    /**
     * @param array $tokens List of targets
     * @param array $notifications Message(s) to send to each token
     * @param array $params
     * @return ResponseInterface|null
     * @noinspection DuplicatedCode
     */
    public function push(array $tokens = [], array $notifications = [], array $params = []): ?ResponseInterface
    {
        if (!$tokens || !$notifications) {
            return null;
        }

        if (!$this->certificatePath) {
            throw new RuntimeException('IOS certificate path must be set');
        }

        $fs = new Filesystem();
        if (!$fs->exists($this->certificatePath) || !is_readable($this->certificatePath)) {
            throw new InvalidArgumentException('Wrong or not readable certificate path');
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
        /** @noinspection PhpUnusedLocalVariableInspection */
        $pushes = $pushManager->push();

        $this->response = $apnsAdapter->getResponse();
        $this->feedback = [];

        return $this->response;
    }

    /**
     * Use feedback to get not registered tokens from last send
     * and remove them from your DB
     *
     * @return array
     */
    public function feedback(): array
    {
        $adapterParams = [];
        $adapterParams['certificate'] = $this->certificatePath;
        $adapterParams['passPhrase'] = $this->passPhrase;

        // Development one by default (without argument).
        $pushManager = new PushManager($this->environment);

        // Then declare an adapter.
        $apnsAdapter = new ApnsAdapter($adapterParams);

        $this->feedback = $pushManager->getFeedback($apnsAdapter);

        return $this->feedback;
    }

    /**
     * The Apple Push Notification service includes a feedback service to give you information
     * about failed remote notifications. When a remote notification cannot be delivered
     * because the intended app does not exist on the device,
     * the feedback service adds that device’s token to its list.
     *
     * @return array
     */
    public function getFeedback(): array
    {
        return $this->feedback;
    }

    /**
     * @return array
     */
    public function getInvalidTokens(): array
    {
        if (!$this->response) {
            return [];
        }

        if (!$this->feedback) {
            $this->feedback = $this->feedback();
        }

        $feedbackTokens = array_keys($this->feedback);

        //all bad
        if ($feedbackTokens) {
            return $feedbackTokens;
        }

        return [];
    }

    /**
     * @return array
     */
    public function getSuccessfulTokens(): array
    {
        if (!$this->response) {
            return [];
        }

        if (!$this->feedback) {
            $this->feedback = $this->feedback();
        }

        $feedbackTokens = array_keys($this->feedback);
        $sentTokens = array_keys($this->response->getParsedResponses());

        //all bad
        if (!$feedbackTokens) {
            return $sentTokens;
        }

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $tokens = array_diff($sentTokens, $feedbackTokens);

        return $tokens;
    }
}
