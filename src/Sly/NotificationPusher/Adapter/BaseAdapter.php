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

use ReflectionClass;
use Sly\NotificationPusher\Model\BaseParameteredModel;
use Sly\NotificationPusher\Model\Response;
use Sly\NotificationPusher\Model\ResponseInterface;
use Sly\NotificationPusher\PushManager;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
abstract class BaseAdapter extends BaseParameteredModel implements AdapterInterface
{
    /**
     * @var string
     */
    protected string $adapterKey;

    /**
     * @var string
     */
    protected string $environment;

    /**
     * @var ResponseInterface
     */
    protected ResponseInterface $response;

    /**
     * @param array $parameters Adapter specific parameters
     */
    public function __construct(array $parameters = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined($this->getDefinedParameters());
        $resolver->setDefaults($this->getDefaultParameters());
        $resolver->setRequired($this->getRequiredParameters());

        $reflectedClass = new ReflectionClass($this);
        $this->adapterKey = lcfirst($reflectedClass->getShortName());
        $this->parameters = $resolver->resolve($parameters);
        $this->response = new Response();
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return ucfirst($this->getAdapterKey());
    }

    /**
     * @return string
     */
    public function getAdapterKey(): string
    {
        return $this->adapterKey;
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * @param string $environment Environment value to set
     *
     * @return AdapterInterface
     */
    public function setEnvironment(string $environment): AdapterInterface
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * @return bool
     * @noinspection PhpUnused
     */
    public function isDevelopmentEnvironment(): bool
    {
        return (PushManager::ENVIRONMENT_DEV === $this->getEnvironment());
    }

    /**
     * @return bool
     */
    public function isProductionEnvironment(): bool
    {
        return (PushManager::ENVIRONMENT_PROD === $this->getEnvironment());
    }

}
