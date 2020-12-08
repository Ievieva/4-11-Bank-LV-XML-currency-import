<?php

namespace App\Services;

use App\Repositories\CurrencyRepository;

class CurrencyService
{
    public function getData($result): array
    {
        foreach ($result as $currency) {
            CurrencyRepository::store($currency['ID'], $currency['Rate']);
            CurrencyRepository::update($currency['ID'], $currency['Rate']);
        }

        return CurrencyRepository::index();
    }
}

