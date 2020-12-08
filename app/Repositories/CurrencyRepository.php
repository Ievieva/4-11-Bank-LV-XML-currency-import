<?php


namespace App\Repositories;


use App\Models\Currency;

class CurrencyRepository
{
    public static function store(string $id, string $rate)
    {
        $currency = self::getById($id);
        if ($currency == null) {
            query()->insert('currency')
                ->values([
                    'id' => ':id',
                    'rate' => ':rate'
                ])
                ->setParameters([
                    'id' => $id,
                    'rate' => $rate
                ])
                ->execute();
        }
    }

    public static function index(): array
    {
        $query = query()
            ->select('*')
            ->from('currency')
            ->execute()
            ->fetchAllAssociative();

        $currencies = [];

        foreach ($query as $currency) {
            $currencies[] = new Currency(
                $currency['id'],
                $currency['rate']
            );
        }
        return $currencies;
    }

    public static function getById(string $id): Currency
    {
        $query = query()
            ->select('*')
            ->from('currency')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetchAssociative();

        return new Currency(
            $query['id'],
            $query['rate']
        );
    }

    public static function update(string $id, string $rate)
    {
        if (self::getById($id)->rate() !== $rate) {
            query()->update('currency')
                ->set('rate', ':rate')
                ->setParameter('rate', $rate)
                ->where('id = :id')
                ->setParameter('id', $id)
                ->execute();
        }
    }
}
