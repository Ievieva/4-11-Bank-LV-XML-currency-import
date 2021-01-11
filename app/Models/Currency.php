<?php

namespace App\Models;

class Currency
{
    private string $id;
    private string $rate;

    public function __construct(string $id, string $rate)
    {
        $this->id = $id;
        $this->rate = $rate;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function rate(): string
    {
        return $this->rate;
    }
}
