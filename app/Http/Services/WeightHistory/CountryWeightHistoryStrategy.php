<?php

namespace App\Http\Services\WeightHistory;

use Illuminate\Support\Facades\DB;

class CountryWeightHistoryStrategy implements WeightHistoryStrategy
{
    public function getWeightHistory(int $id): array
    {
        $query = "
            SELECT
              market_data.date,
              SUM(market_data.weight) AS weight
            FROM
              market_data
              JOIN companies ON companies.id = market_data.company_id
            WHERE
              companies.country_id = ?
            GROUP BY
              market_data.date
            ORDER BY
              market_data.date
        ";

        $countryMarketData = DB::select($query, [$id]);

        return [
            'dates' => array_map(fn($data) => $data->date, $countryMarketData),
            'weights' => array_map(fn($data) => $data->weight, $countryMarketData),
        ];
    }
}
