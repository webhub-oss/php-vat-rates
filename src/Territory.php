<?php

namespace Webhub\Vat;

use Carbon\Carbon;

class Territory
{

    /**
     * @var array
     */
    protected $rates;

    public function __construct(array $rates)
    {
        $this->rates = $rates;
    }

    /**
     * @param string|null $type e.g. 'standard'
     * @return Rate
     * @throws AmbiguousResultException
     * @throws NoResultException
     */
    public function current(string $type = null) : Rate
    {
        return $this->at(Carbon::now(), $type);
    }

    /**
     * @param Carbon|string $at
     * @param string|null $type
     * @return Rate
     * @throws AmbiguousResultException
     * @throws NoResultException
     */
    public function at($at, string $type = null) : Rate
    {
        if (!$at instanceof Carbon) {
            $at = Carbon::make($at);
        }

        $result = $this->filter($at, $type);

        $this->guardUnambiguous($result);

        return new Rate(current($result));
    }

    /**
     * @return array of `Rate`
     */
    public function all() : array
    {
        return array_map(function ($rate) {
            return new Rate($rate);
        }, $this->rates);
    }

    /**
     * Filters rates
     *
     * @return array
     */
    protected function filter(Carbon $at = null, string $type = null) : array
    {
        return array_filter($this->rates, function ($rate) use ($at, $type) {
            if ($type !== null && $rate['rate_type'] !== $type) {
                return false;
            }

            if ($at !== null && !$this->holdsAt($rate, $at)) {
                return false;
            }

            return true;
        });
    }

    protected function holdsAt(array $rate, Carbon $at)
    {
        $at = $at->startOfDay();

        if ($rate['start_date'] && $at < $rate['start_date']) {
            return false;
        }

        if ($rate['stop_date'] && $at >= $rate['stop_date']) {
            return false;
        }

        return true;
    }

    /**
     * @param array $result
     * @throws AmbiguousResultException
     * @throws NoResultException
     */
    protected function guardUnambiguous(array $result)
    {
        $count = count($result);

        if ($count === 0) {
            throw new NoResultException;
        }

        if ($count > 1) {
            throw new AmbiguousResultException;
        }
    }
}
