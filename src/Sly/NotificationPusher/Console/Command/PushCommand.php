<?php

/*
 * This file is part of NotificationPusher.
 *
 * (c) 2013 Cédric Dugat <cedric@dugat.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sly\NotificationPusher\Console\Command;

use Doctrine\Common\Inflector\Inflector;
use Exception;
use Sly\NotificationPusher\Adapter\AdapterInterface;
use Sly\NotificationPusher\Exception\AdapterException;
use Sly\NotificationPusher\Model\Device;
use Sly\NotificationPusher\Model\Message;
use Sly\NotificationPusher\Model\Push;
use Sly\NotificationPusher\PushManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @uses Command
 * @author Cédric Dugat <cedric@dugat.me>
 */
class PushCommand extends Command
{
    /**
     * @see Command
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('push')
            ->setDescription('Manual notification push')
            ->addArgument(
                'adapter',
                InputArgument::REQUIRED,
                'Adapter (apns, gcm, specific class name, ...)'
            )
            ->addArgument(
                'token',
                InputArgument::REQUIRED,
                'Device Token or Registration ID'
            )
            ->addArgument(
                'message',
                InputArgument::REQUIRED,
                'Message'
            )
            ->addOption(
                'certificate',
                null,
                InputOption::VALUE_OPTIONAL,
                'Certificate path (for APNS adapter)'
            )
            ->addOption(
                'api-key',
                null,
                InputOption::VALUE_OPTIONAL,
                'API key (for GCM adapter)'
            )
            ->addOption(
                'env',
                PushManager::ENVIRONMENT_DEV,
                InputOption::VALUE_OPTIONAL,
                sprintf(
                    'Environment (%s, %s)',
                    PushManager::ENVIRONMENT_DEV,
                    PushManager::ENVIRONMENT_PROD
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $adapter = $this->getReadyAdapter($input, $output);
        $pushManager = new PushManager($input->getOption('env'));
        $message = new Message($input->getArgument('message'));
        $push = new Push($adapter, new Device($input->getArgument('token')), $message);
        $pushManager->add($push);

        $pushManager->push();
        return 0;
    }

    /**
     * @param string $argument Given argument
     *
     * @return string
     *
     * @throws AdapterException When given adapter class doesn't exist
     */
    private function getAdapterClassFromArgument(string $argument): string
    {
        if (!class_exists($adapterClass = $argument) &&
            !class_exists($adapterClass = '\\Sly\\NotificationPusher\\Adapter\\' . ucfirst($argument))) {
            throw new AdapterException(
                sprintf(
                    'Adapter class %s does not exist',
                    $adapterClass
                )
            );
        }

        return $adapterClass;
    }

    /**
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return AdapterInterface
     * @noinspection PhpUnusedParameterInspection
     */
    private function getReadyAdapter(InputInterface $input, OutputInterface $output): AdapterInterface
    {
        $adapterClass = $this->getAdapterClassFromArgument($input->getArgument('adapter'));

        try {
            $adapter = new $adapterClass();
        } catch (Exception $e) {
            $adapterData = [];
            preg_match_all('/"(.*)"/i', $e->getMessage(), $matches);

            foreach ($matches[1] as $match) {
                $optionKey = str_replace('_', '-', Inflector::tableize($match));
                $option = $input->getOption($optionKey);

                if (!$option) {
                    throw new AdapterException(
                        sprintf(
                            'The option "%s" is needed by %s adapter',
                            $optionKey,
                            $adapterClass
                        )
                    );
                }

                $adapterData[$match] = $option;
            }

            $adapter = new $adapterClass($adapterData);
        }

        return $adapter;
    }
}
