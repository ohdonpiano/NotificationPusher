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

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
abstract class BaseParameteredModel
{
    /**
     * @var array
     */
    protected array $parameters = [];

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param string $key Key
     *
     * @return boolean
     */
    public function hasParameter(string $key): bool
    {
        return array_key_exists($key, $this->parameters);
    }

    /**
     * @param string $key Key
     * @param mixed|null $default Default
     *
     * @return mixed
     */
    public function getParameter(string $key, mixed $default = null): mixed
    {
        return $this->hasParameter($key) ? $this->parameters[$key] : $default;
    }

    /**
     * @param array $parameters Parameters
     *
     * @return BaseParameteredModel
     */
    public function setParameters(array $parameters): static
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @param string $key Key
     * @param mixed $value Value
     *
     * @return mixed
     */
    public function setParameter(string $key, mixed $value): mixed
    {
        $this->parameters[$key] = $value;

        return $value;
    }
}
