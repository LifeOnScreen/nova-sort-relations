<?php

namespace LifeOnScreen\SortRelations;

/**
 * Trait SortRelations
 * @package LifeOnScreen\SortRelations
 */
trait SortRelations
{
    /**
     * Get the sortable columns for the resource.
     *
     * @return array
     */
    public static function sortableRelations(): array
    {
        return static::$sortRelations ?? [];
    }

    /**
     * Apply any applicable orderings to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $column
     * @param  string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function applyRelationOrderings(string $column, string $direction, $query)
    {
        $sortRelations = static::sortableRelations();

        if (is_string($sortRelations[$column])) {
            return $query->orderBy($sortRelations[$column], $direction);
        }
        foreach ($sortRelations[$column] as $orderColumn) {
            $query->orderBy($orderColumn, $direction);
        }

        return $query;
    }

    /**
     * Apply any applicable orderings to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array $orderings
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function applyOrderings($query, array $orderings)
    {
        if (empty($orderings)) {
            return empty($query->orders)
                ? $query->latest($query->getModel()->getQualifiedKeyName())
                : $query;
        }

        $sortRelations = static::sortableRelations();

        foreach ($orderings as $column => $direction) {
            if (array_key_exists($column, $sortRelations)) {
                $query = self::applyRelationOrderings($column, $direction, $query);
            } else {
                $query->orderBy($column, $direction);
            }
        }

        return $query;
    }
}
