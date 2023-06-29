<?php

namespace App\Filters;
use Pricecurrent\LaravelEloquentFilters\AbstractEloquentFilter;
use Illuminate\Database\Eloquent\Builder;

class CustomerFilter extends AbstractEloquentFilter {
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder): Builder{

        $customers = $builder->withCount(['contract' => function($query){
            $query->where('open_ticket_contract','1');
        }])->where('first_name', 'like', "{$this->request->search_text}%")->orWhere('last_name','like',"{$this->request->search_text}%");
        return $customers;
    }
}
