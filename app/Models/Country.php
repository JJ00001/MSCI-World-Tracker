<?php

namespace App\Models;

use App\Models\Scopes\HasActiveCompanyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy(HasActiveCompanyScope::class)]
class Country extends Model
{
    protected $fillable = [
        'name',
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'country_id');
    }

    public function scopeWithCompaniesCount(Builder $query): void
    {
        $query->withCount('companies');
    }

    public function scopeWithWeight(Builder $query): void
    {
        $query->addSelect([
            'weight' => function ($query) {
                $query->selectRaw('SUM(market_data.weight)')
                    ->from('companies')
                    ->join('market_data', function ($join) {
                        $join->on('companies.id', '=', 'market_data.company_id')
                            ->where('market_data.date', MarketData::maxDate());
                    })
                    ->whereColumn('companies.country_id', 'countries.id');
            },
        ]);
    }

    public function scopeWithStats(Builder $query): void
    {
        $query
            ->withCompaniesCount()
            ->withWeight();
    }
}
