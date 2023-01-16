<?php

/*
 * This file is part of NotificationPusher.
 *
 * (c) 2013 Cédric Dugat <cedric@dugat.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sly\NotificationPusher\Collection;

use ArrayIterator;
use Countable;
use Exception;
use IteratorAggregate;
use ReturnTypeWillChange;
use SeekableIterator;
use Sly\NotificationPusher\Model\MessageInterface;

/**
 * @uses IteratorAggregate
 * @author Cédric Dugat <cedric@dugat.me>
 */
abstract class AbstractCollection implements IteratorAggregate, Countable
{
    /**
     * @var ArrayIterator
     */
    protected ArrayIterator $coll;

    /**
     * @inheritdoc
     * @return ArrayIterator|SeekableIterator
     */
    abstract public function getIterator(): ArrayIterator;

    /**
     * @param string $key Key
     *
     * @return MessageInterface|false
     */
    public function get(string $key): bool|MessageInterface
    {
        return isset($this->coll[$key]) ? $this->coll[$key] : false;
    }

    /**
     * @return integer
     * @throws Exception
     */
    #[ReturnTypeWillChange] public function count(): int
    {
        return $this->getIterator()->count();
    }

    /**
     * @return boolean
     * @throws Exception
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * Clear categories.
     */
    public function clear()
    {
        $this->coll = new ArrayIterator();
    }

    /**
     * @return mixed|null
     */
    public function first(): mixed
    {
        $tmp = clone $this->coll;

        //go to the beginning
        $tmp->rewind();

        if (!$tmp->valid()) {
            return null;
        }

        return $tmp->current();
    }

    /**
     * @return mixed|null
     */
    public function last(): mixed
    {
        $tmp = clone $this->coll;

        //go to the end
        $tmp->seek($tmp->count() - 1);

        if (!$tmp->valid()) {
            return null;
        }

        return $tmp->current();
    }
}
