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

namespace BenTools\CSVIterator;

class CSVIteratorExtended extends \FilterIterator implements CSVIteratorInterface {

    protected $keys = array();

    protected $firstRowsAsKeys = false;

    /**
     * @var callable
     */
    protected $formatKeys;

    /**
     * @var callable
     */
    protected $rowFilter;

    protected $nbRows;

    /**
     * @param CSVIterator $CSVIterator
     * @param callable|null $formatKeys  - A callable to apply on keys
     * @param bool|true $setFirstRowASKeys - Consider the 1st row as keys
     * @param callable|null $rowFilter - A callable to filter rows
     */
    public function __construct(CSVIterator $CSVIterator, callable $formatKeys = null, $setFirstRowASKeys = true, callable $rowFilter = null)
    {
        parent::__construct($CSVIterator);
        $this->formatKeys       =   $formatKeys;
        $this->firstRowAsKeys   =   $setFirstRowASKeys;
        $this->rowFilter        =   $rowFilter;
        $this->rewind();
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        $row = parent::current();

        if ($this->firstRowAsKeys && !$this->keys && $this->key() === 0) {
            $this->keys = is_callable($this->formatKeys) ? array_map($this->formatKeys, $row) : $row;
        }

        return $this->formatRow($row);
    }

    /**
     * @param $row
     * @return array|bool
     */
    protected function formatRow($row)
    {

        if ($this->firstRowAsKeys && $this->key() === 0) {
            return false;
        }

        if (is_array($row) && !array_filter($row, $this->getRowFilter()))
            return false;

        if (!$this->keys)
            return $row;

        if (!is_array($row))
            return [];

        if (count($this->keys) === count($row))
            return array_combine($this->keys, $row);

        elseif (count($this->keys) > count($row))
            return array_combine($this->keys, (array) array_merge((array) $row, (array) array_fill(0, count($this->keys) - count($row), '')));

        elseif (count($this->keys) < count($row))
            return array_combine($this->keys, (array) array_slice($row, 0, count($this->keys)));

        else
            return [];
    }


    /**
     * @inheritDoc
     */
    public function seek($position)
    {
        $this->getInnerIterator()->seek($position);
    }

    /**
     * @inheritDoc
     */
    public function accept()
    {
        return (bool) $this->current();
    }

    /**
     * @return array
     */
    public function getKeys()
    {
        return $this->keys;
    }

    /**
     * @param array $keys
     * @return $this - Provides Fluent Interface
     */
    public function setKeys($keys)
    {
        $this->keys = $keys;
        return $this;
    }

    /**
     * @return callable
     */
    public function getRowFilter()
    {
        if (is_null($this->rowFilter))
            $this->rowFilter = function($cell) {
                return trim($cell) !== '';
            };
        return $this->rowFilter;
    }

    /**
     * @param callable $rowFilter
     * @return $this - Provides Fluent Interface
     */
    public function setRowFilter($rowFilter)
    {
        $this->rowFilter = $rowFilter;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        if (is_null($this->nbRows)) {
            $currentKey = $this->key();
            $this->rewind();
            $this->nbRows = 0;
            foreach ($this AS $row)
                $this->nbRows++;
            $this->seek($currentKey);
        }
        return $this->nbRows;
    }

    /**
     * @return boolean
     */
    public function getFirstRowsAsKeys()
    {
        return $this->firstRowsAsKeys;
    }

    /**
     * @param boolean $firstRowsAsKeys
     * @return $this - Provides Fluent Interface
     */
    public function setFirstRowsAsKeys($firstRowsAsKeys)
    {
        $this->firstRowsAsKeys = $firstRowsAsKeys;
        return $this;
    }

    /**
     * @return CSVIteratorInterface
     * @deprecated - to be removed
     */
    public function getCSVIterator() {
        return $this->getInnerIterator();
    }
    /**
     * @param CSVIteratorInterface $CSVIterator
     * @return $this - Provides Fluent Interface
     * @deprecated - to be removed
     */
    public function setCSVIterator(CSVIteratorInterface $CSVIterator) {
        parent::__construct($CSVIterator);
        return $this;
    }

}