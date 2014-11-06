<?php
/**
 * MIT License (MIT)
 *
 * Copyright (c) 2014 Beno!t POLASZEK
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * CSV Iterator transforming the 1st row to array keys for each row
 * @author Beno!t POLASZEK - 2014
 */

namespace BenTools;

class CSVIteratorExtended extends \FilterIterator {

    protected   $keys       =   array();
    protected   $keysAreSet =   false;

    /**
     * @var CSVIteratorInterface
     */
    protected   $CSVIterator;

    /**
     * @param CSVIteratorInterface $CSVIterator
     * @param null                 $callable - A callable to apply on keys
     */
    public function __construct(CSVIteratorInterface $CSVIterator, $callable = null) {
        $this->setCSVIterator($CSVIterator);
        parent::__construct($CSVIterator);
        $this->setFirstRowAsKeys($callable);
    }

    /**
     * @return array|bool|mixed
     */
    public function current() {

        $row = parent::current();

        if (!$this->keys)
            return $row;

        if (!is_array($row))
            return array_combine($this->keys, array_fill(0, count($this->keys), ''));

        if (count($this->keys) === count($row))
            return array_combine($this->keys, $row);

        elseif (count($this->keys) > count($row))
            return array_combine($this->keys, (array) array_merge((array) $row, (array) array_fill(0, count($this->keys) - count($row), '')));

        elseif (count($this->keys) < count($row))
            return array_combine($this->keys, (array) array_slice($row, 0, count($this->keys)));

        else
            return false;

    }

    /**
     * @param null $callable
     * @return $this
     */
    public function setFirstRowAsKeys($callable = null) {
        $this->setKeys(((bool) $this->getCSVIterator()->getRowCounter()) ? $this->getCSVIterator()->rewind()->current() : $this->getCSVIterator()->current(), $callable);
        $this->keysAreSet   =   true;
        return $this;
    }

    /**
     * @param array $keys
     * @param null  $callable
     * @return $this
     */
    public function setKeys(Array $keys, $callable = null) {
        $this->keys = (is_callable($callable)) ? array_map($callable, $keys) : $keys;
        return $this;
    }

    /**
     * @return array
     */
    public function getKeys() {
        return $this->keys;
    }

    /**
     * @return CSVIteratorInterface
     */
    public function getCSVIterator() {
        return $this->CSVIterator;
    }

    /**
     * @param CSVIteratorInterface $CSVIterator
     * @return $this - Provides Fluent Interface
     */
    public function setCSVIterator(CSVIteratorInterface $CSVIterator) {
        $this->CSVIterator = $CSVIterator;
        return $this;
    }

    public function accept() {
        return !$this->keysAreSet || $this->getCSVIterator()->getRowCounter() !== 1;
    }


}