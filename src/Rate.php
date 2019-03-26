<?php

namespace Webhub\Vat;

use Carbon\Carbon;

class Rate implements \ArrayAccess
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function rate() : string
    {
        return $this->data['rate'];
    }

    public function rateType() : string
    {
        return $this->data['rate_type'];
    }

    public function description() : string
    {
        return $this->data['description'];
    }

    public function currencyCode() : string
    {
        return $this->data['currency_code'];
    }

    public function startDate() : Carbon
    {
        return Carbon::make($this->data['start_date']);
    }

    public function stopDate() : Carbon
    {
        return Carbon::make($this->data['stop_date']);
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}
