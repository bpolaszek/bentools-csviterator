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
 * CSV Iterator
 * @author Beno!t POLASZEK - 2014
 */

namespace BenTools\CSVIterator;

class CSVIterator implements CSVIteratorInterface {

    const       DEFAULT_DELIMITER   =   ',';
    const       DEFAULT_ENCLOSURE   =   '"';
    const       DEFAULT_ESCAPE      =   '\\';

    protected   $file;
    protected   $nbRows;
    protected   $delimiter          =   self::DEFAULT_DELIMITER;
    protected   $enclosure          =   self::DEFAULT_ENCLOSURE;
    protected   $escape             =   self::DEFAULT_ESCAPE;

    /**
     * @param \SplFileObject|string $file - a filename or a SplFileObject
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     */
    public function __construct($file, $delimiter = self::DEFAULT_DELIMITER, $enclosure = self::DEFAULT_ENCLOSURE, $escape = self::DEFAULT_ESCAPE) {
        if (is_string($file))
            $file = new \SplFileObject($file, 'r');
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
        $file->setCsvControl($delimiter, $enclosure, $escape);
        $this->file = $file;
    }

    /**
     * Fluent interface
     * @return self
     */
    public static function NewInstance() {
        $currentClass = new \ReflectionClass(get_called_class());
        return $currentClass->NewInstanceArgs(func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function current() {
        return $this->file->fgetcsv();
    }

    /**
     * @inheritDoc
     */
    public function next() {
        $this->file->next();
    }

    /**
     * @inheritDoc
     */
    public function key() {
        return $this->file->key();
    }

    /**
     * @inheritDoc
     */
    public function valid() {
        return $this->file->valid();
    }

    /**
     * @inheritDoc
     */
    public function rewind() {
        $this->file->rewind();
    }

    /**
     * @inheritDoc
     */
    public function seek($position)
    {
        $this->file->seek($position);
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
     * @return int
     */
    public function getNbRows()
    {
        return $this->nbRows;
    }

    /**
     * @param int $nbRows
     * @return $this - Provides Fluent Interface
     */
    public function setNbRows($nbRows)
    {
        $this->nbRows = (int) $nbRows;
        return $this;
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * @param string $delimiter
     * @return $this - Provides Fluent Interface
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
        $this->file->setCsvControl($delimiter, $this->getEnclosure(), $this->getEscape());
        return $this;
    }

    /**
     * @return string
     */
    public function getEnclosure()
    {
        return $this->enclosure;
    }

    /**
     * @param string $enclosure
     * @return $this - Provides Fluent Interface
     */
    public function setEnclosure($enclosure)
    {
        $this->enclosure = $enclosure;
        $this->file->setCsvControl($this->getDelimiter(), $enclosure, $this->getEscape());
        return $this;
    }

    /**
     * @return string
     */
    public function getEscape()
    {
        return $this->escape;
    }

    /**
     * @param string $escape
     * @return $this - Provides Fluent Interface
     */
    public function setEscape($escape)
    {
        $this->escape = $escape;
        $this->file->setCsvControl($this->getDelimiter(), $this->getEnclosure(), $escape);
        return $this;
    }

    /**
     * @return int
     * @deprecated - to be removed
     */
    public function getRowSize() {
        return 4096;
    }
    /**
     * @param int $rowSize
     * @return $this - Provides Fluent Interface
     * @deprecated - to be removed
     */
    public function setRowSize($rowSize) {
        return $this;
    }

    /**
     * @return boolean
     * @deprecated - to be removed
     */
    public function isOpened() {
        return true;
    }

    /**
     * @return \SplFileObject
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param \SplFileObject $file
     * @return $this - Provides Fluent Interface
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }


}