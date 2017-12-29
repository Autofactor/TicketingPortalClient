<?php
namespace Albertarni\TicketingPortalClient\Api;

/**
 * Class Model
 *
 * @package Picqer\Financials\Exact
 *
 */

class PaginatedCollection implements \Iterator, \ArrayAccess
{
    private $limit = 15;
    private $total = 0;
    private $offset = 1;
    private $position = 0;
    private $total_offsets = 1;

    private $container = [];


    /**
     * Pagination constructor.
     * @param int $limit
     * @param int $total
     * @param int $offset
     */
    public function __construct($data = [], $limit = 15, $total = 0, $offset = 1, $total_offsets = 1)
    {
        $this->container = $data;
        $this->limit = $limit;
        $this->total = $total;
        $this->offset = $offset;
        $this->total_offsets = $total_offsets;
        $this->position = 0;
    }


    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    function rewind() {
        $this->position = 0;
    }

    function current() {
        return $this->container[$this->position];
    }

    function key() {
        return $this->position;
    }

    function next() {
        ++$this->position;
    }

    function valid() {
        return isset($this->container[$this->position]);
    }


    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }


    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }


    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return int
     */
    public function getTotalOffsets()
    {
        return $this->total_offsets;
    }

    /**
     * @param int $total_offsets
     */
    public function setTotalOffsets($total_offsets)
    {
        $this->total_offsets = $total_offsets;
    }

    public function toArray() {
        return $this->container;
    }
}
