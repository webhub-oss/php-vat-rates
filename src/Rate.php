<?php

namespace Webhub\Vat;

class Rate
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

    public function type() : string
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
}
