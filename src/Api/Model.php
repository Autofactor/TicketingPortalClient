<?php
namespace Albertarni\TicketingPortalClient\Api;


/**
 * Class Model
 *
 * @package Picqer\Financials\Exact
 *
 */

abstract class Model
{

    /**
     * @var Connection
     */
    protected $connection;

    protected $attributes = [];

    protected $url = '';

    protected $primaryKey = 'id';

    protected $fillable = [];
    protected $relations = [];

    protected $filters = [];

    public function __construct(Connection $connection, array $attributes = [ ])
    {
        $this->connection = $connection;
        $this->fill($attributes);
    }

    /**
     * Get the connection instance
     *
     * @return Connection
     */
    public function connection()
    {
        return $this->connection;
    }


    /**
     * Get the model's attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get the model's primary key
     *
     * @return string
     */
    public function primaryKey() {
        return $this->primaryKey;
    }


    /**
     * Fill the entity from an array
     *
     * @param array $attributes
     */
    protected function fill(array $attributes)
    {
        foreach ($this->fillableFromArray($attributes) as $key => $value) {
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            }
        }
    }


    /**
     * Get the fillable attributes of an array
     *
     * @param array $attributes
     *
     * @return array
     */
    protected function fillableFromArray(array $attributes)
    {
        if (count($this->fillable) > 0) {
            return array_intersect_key($attributes, array_flip($this->fillable));
        }

        return $attributes;
    }


    protected function isFillable($key)
    {
        return in_array($key, $this->fillable);
    }


    protected function setAttribute($key, $value)
    {
        if (array_key_exists($key, $this->relations)) {
            $class = $this->relations[$key];
            $this->attributes[$key] = [];

            foreach($value['data'] as $item) {
                if (is_array($item)) {
                    $item = new $class($this->connection(), $item);
                }

                $this->attributes[$key][] = $item;
            }
        }
        else {
            $this->attributes[$key] = $value;
        }
    }


    public function __get($key)
    {
        if (isset( $this->attributes[$key] )) {
            return $this->attributes[$key];
        }

        return null;
    }


    public function __set($key, $value)
    {
        if ($this->isFillable($key)) {
            return $this->setAttribute($key, $value);
        }
    }


    protected function parsePagination(PaginatedCollection $collection, $result) {
        $collection->setLimit($result['meta']['pagination']['per_page']);
        $collection->setOffset($result['meta']['pagination']['current_page']);
        $collection->setTotalOffsets($result['meta']['pagination']['total_pages']);
        $collection->setTotal($result['meta']['pagination']['total']);

        return $this;
    }

    /**
     * @param $result
     * @return PaginatedCollection|array
     * @throws ApiException
     */
    public function collectionFromResult($result)
    {
        $collection = new PaginatedCollection();
        $this->parsePagination($collection, $result);

        foreach($result['data'] as $item) {
            $collection[] = new static($this->connection(), $item);
        }

        return $collection;
    }


    /**
     * Sets gilter parameters
     * @param $filter
     * @param bool $append
     * @return $this
     */
    public function filter($filters, $append = true) {
        if ($append) {
            $this->filters = array_merge($this->filters, $filters);
        }

        return $this;
    }

    /**
     * Returns paginated result
     * @param int $limit
     * @param int $offset
     * @return PaginatedCollection|array
     */
    public function paginate($limit = 15, $offset = 1) {
        $this->filters['per_page'] = $limit;
        $this->filters['page'] = $offset;

        return $this->get();
    }


    /**
     * Returns all results if pagination was not yet set
     * @return PaginatedCollection|array
     * @throws ApiException
     */
    public function get() {
        $result = $this->connection()->get($this->url, $this->filters);
        return $this->collectionFromResult($result);
    }
}
