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

    const       DEFAULT_ROW_SIZE    =   4096;
    const       DEFAULT_DELIMITER   =   ',';
    const       DEFAULT_ENCLOSURE   =   '"';
    const       DEFAULT_ESCAPE      =   '\\';

    protected   $file               =   '';
    protected   $filePointer;
    protected   $currentElement;
    protected   $rowCounter         =   0;
    protected   $rowSize            =   self::DEFAULT_ROW_SIZE;
    protected   $delimiter          =   self::DEFAULT_DELIMITER;
    protected   $enclosure          =   self::DEFAULT_ENCLOSURE;
    protected   $escape             =   self::DEFAULT_ESCAPE;
    protected   $nbRows             =   0;
    private     $opened             =   false;

    /**
     * @param string $file - the file name to open
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     */
    public function __construct($file, $delimiter = self::DEFAULT_DELIMITER, $enclosure = self::DEFAULT_ENCLOSURE, $escape = self::DEFAULT_ESCAPE, $rowSize = self::DEFAULT_ROW_SIZE) {
        $this   ->  setFile($file)
                ->  setDelimiter($delimiter)
                ->  setEnclosure($enclosure)
                ->  setEscape($escape)
                ->  setRowSize($rowSize);
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
     * Opens the CSV file.
     * @return $this
     */
    protected function open() {
        $this->filePointer = fopen($this->file, 'r');
        $this->count();
        $this->setOpened(true);
        return $this;
    }

    /**
     * @return int
     */
    public function getRowCounter() {
        return $this->rowCounter;
    }

    /**
     * @return string
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * @param string $file
     * @return $this - Provides Fluent Interface
     */
    public function setFile($file) {
        $this->file = $file;
        return $this;
    }

    /**
     * @return string
     */
    public function getDelimiter() {
        return $this->delimiter;
    }

    /**
     * @param string $delimiter
     * @return $this - Provides Fluent Interface
     */
    public function setDelimiter($delimiter) {
        $this->delimiter = $delimiter;
        return $this;
    }

    /**
     * @return string
     */
    public function getEnclosure() {
        return $this->enclosure;
    }

    /**
     * @param string $enclosure
     * @return $this - Provides Fluent Interface
     */
    public function setEnclosure($enclosure) {
        $this->enclosure = $enclosure;
        return $this;
    }

    /**
     * @return string
     */
    public function getEscape() {
        return $this->escape;
    }

    /**
     * @param string $escape
     * @return $this - Provides Fluent Interface
     */
    public function setEscape($escape) {
        $this->escape = $escape;
        return $this;
    }

    /**
     * @return int
     */
    public function getNbRows() {
        return $this->nbRows;
    }

    /**
     * @param int $nbRows
     * @return $this - Provides Fluent Interface
     */
    public function setNbRows($nbRows) {
        $this->nbRows = $nbRows;
        return $this;
    }

    /**
     * @return int
     */
    public function getRowSize() {
        return $this->rowSize;
    }

    /**
     * @param int $rowSize
     * @return $this - Provides Fluent Interface
     */
    public function setRowSize($rowSize) {
        $this->rowSize = (int) $rowSize;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isOpened() {
        return $this->opened;
    }

    /**
     * @param boolean $opened
     * @return $this - Provides Fluent Interface
     */
    private function setOpened($opened) {
        $this->opened = (bool) $opened;
        return $this;
    }

    /**
     * @return $this
     */
    public function rewind() {
        if (!$this->isOpened())
            $this->open();
        $this->rowCounter = 0;
        if (!@rewind($this->filePointer)) {
            $this->close();
            $this->open();
        }
        return $this;
    }

    /**
     * @return array
     */
    public function current() {
        if (!$this->isOpened())
            $this->rewind();
        $this->currentElement = fgetcsv($this->filePointer, $this->getRowSize(), $this->delimiter, $this->enclosure, $this->escape);
        $this->rowCounter++;
        return $this->currentElement;
    }

    /**
     * @return int
     */
    public function key() {
        return $this->rowCounter;
    }

    /**
     * @return bool
     */
    public function next() {
        return !feof($this->filePointer);
    }

    /**
     * @return bool
     */
    protected function close() {
        @fclose($this->filePointer);
        $this->setOpened(false);
        return false;
    }

    /**
     * @return bool
     */
    public function valid() {
        return (!$this->next()) ? $this->close() : true;
    }

    /**
     * @return int
     */
    public function count() {
        if (!$this->nbRows) {
            while (!feof($this->filePointer)) {
                $line = fgets($this->filePointer, $this->getRowSize());
                $this->nbRows = $this->nbRows + substr_count($line, PHP_EOL);
            }
            rewind($this->filePointer);
        }
        return $this->nbRows;
    }

}