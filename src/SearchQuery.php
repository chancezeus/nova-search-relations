<?php

namespace Titasgailius\SearchRelations;

use Illuminate\Database\Eloquent\Builder;

class SearchQuery
{
    /**
     * Searchable columns.
     *
     * @var array
     */
    protected $columns;

    /**
     * Instantiate a new search query.
     *
     * @param array $columns
     */
    public function __construct(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * Apply search query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $search
     * @return Builder
     */
    public function apply(Builder $query, $search): Builder
    {
        return $query->where(function ($query) use ($search) {
            return $this->applySearchQuery($query, $search);
        });
    }

    /**
     * Apply search query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applySearchQuery(Builder $query, $search): Builder
    {
        $model = $query->getModel();
        $operator = $this->operator($query);

        foreach ($this->columns as $column) {
            $query->orWhere($model->qualifyColumn($column), $operator, '%'.$search.'%');
        }

        return $query;
    }

    /**
     * Get the like operator for the given query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return string
     */
    protected static function operator(Builder $query): string
    {
        if ($query->getModel()->getConnection()->getDriverName() === 'pgsql') {
            return 'ILIKE';
        }

        return 'LIKE';
    }
}
