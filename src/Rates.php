<?php

namespace Webhub\Vat;

use Carbon\Carbon;

class Rates
{
    /**
     * @var array of vat rates data
     */
    protected $data;

    public function __construct(array $rules = null)
    {
        $this->data = $rules ?? require __DIR__.'/data.php';
    }

    /**
     * @return Rate
     * @throws AmbiguousResultException
     * @throws NoResultException
     */
    public function get() : Rate
    {
        $count = count($this->data);

        if ($count === 0) {
            throw new NoResultException;
        }

        if ($count > 1) {
            throw new AmbiguousResultException;
        }

        return new Rate(current($this->data));
    }

    /**
     * Return rates that hold now
     *
     * @return Rates
     */
    public function current() : Rates
    {
        return $this->at(Carbon::now());
    }

    /**
     * Return rates that hold at the specified time
     *
     * @param $when
     * @return Rates
     */
    public function at($when) : Rates
    {
        $when = ($when instanceof Carbon) ? $when : Carbon::make($when);

        return $this->whereValidAt($when);
    }

    /**
     * Return rates that hold in a specific territory
     *
     * @param string $territory
     * @return Rates
     */
    public function in(string $territory) : Rates
    {
        $territory = strtoupper(trim($territory));

        return $this->whereTerritory($territory);
    }

    /**
     * Return rates that have a specific type
     *
     * @param string $type
     * @return Rates
     */
    public function type(string $type) : Rates
    {
        $type = strtolower(trim($type));

        return $this->whereType($type);
    }

    /**
     * Return all matching rates
     *
     * @return array
     */
    public function all() : array
    {
        return array_map(function ($rate) {
            return new Rate($rate);
        }, $this->data);
    }

    /**
     * Return all territories in the current match
     *
     * @return array
     */
    public function territories() : array
    {
        $territories = [];

        foreach ($this->data as $rate) {
            $territories = array_merge($territories, explode("\n", $rate['territory_codes']));
        }

        $territories = array_unique($territories);

        sort($territories);

        return $territories;
    }

    /**
     * Proxies all other calls to a single Rate, if it exists
     *
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws AmbiguousResultException
     * @throws NoResultException
     */
    public function __call($name, $arguments)
    {
        return $this->get()->{$name}(...$arguments);
    }

    /**
     * Create a new set with rules that are valid at `$at`
     *
     * @param Carbon $at
     * @return Rates
     */
    protected function whereValidAt(Carbon $at) : Rates
    {
        return $this->filter(function (array $data) use ($at) {
            if ($data['start_date'] && $at->isBefore($data['start_date'])) {
                return false;
            }

            if ($data['stop_date'] && $at->isAfter($data['stop_date'])) {
                return false;
            }

            return true;
        });
    }

    /**
     * Create a new set with rules that apply to territory `$where`
     *
     * @param string $where
     * @return Rates
     */
    protected function whereTerritory(string $where) : Rates
    {
        return $this->filter(function (array $data) use ($where) {
            return in_array($where, explode("\n", $data['territory_codes']));
        });
    }

    /**
     * Create a new set with rules that are of type `$type`
     *
     * @param string $type
     * @return Rates
     */
    protected function whereType(string $type) : Rates
    {
        return $this->filter(function (array $data) use ($type) {
            return $data['rate_type'] === $type;
        });
    }

    /**
     * Apply a callback to `$data` and return a new set
     *
     * @param $callback
     * @return Rates
     */
    protected function filter($callback) : Rates
    {
        return new self(array_filter($this->data, $callback));
    }
}
