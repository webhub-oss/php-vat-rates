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

        if($count === 0){
            throw new NoResultException;
        }

        if($count > 1){
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

    public function type(string $type) : Rates
    {
        $type = strtolower(trim($type));

        return $this->whereType($type);
    }

    public function all() : array
    {
        return array_map(function ($rate) {
            return new Rate($rate);
        }, $this->data);
    }

    public function territories(bool $current = true) : array
    {
        $now = Carbon::now();

        $territories = [];

        foreach ($this->data as $rate) {
            if ($current) {
                if ($rate['start_date'] && $now->isBefore($rate['start_date'])) {
                    continue;
                }

                if ($rate['stop_date'] && $now->isAfter($rate['stop_date'])) {
                    continue;
                }
            }

            $territories = array_merge($territories, explode("\n", $rate['territory_codes']));
        }

        $territories = array_unique($territories);

        sort($territories);

        return $territories;
    }

    protected function whereValidAt(Carbon $at) : Rates
    {
        return $this->filter(function (array $data) use ($at){

            if($data['start_date'] && $at->isBefore($data['start_date'])){
                return false;
            }

            if($data['stop_date'] && $at->isAfter($data['stop_date'])){
                return false;
            }

            return true;

        });
    }

    protected function whereTerritory(string $where) : Rates
    {
        return $this->filter(function(array $data) use ($where){
            return in_array($where, explode("\n", $data['territory_codes']));
        });
    }

    protected function whereType(string $type) : Rates
    {
        return $this->filter(function (array $data) use ($type){
            return $data['rate_type'] === $type;
        });
    }

    protected function filter($callback) : Rates
    {
        return new self(array_filter($this->data, $callback));
    }
}
