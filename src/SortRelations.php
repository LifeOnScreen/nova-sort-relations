<?php

namespace LifeOnScreen\SortRelations;

/**
 * Trait SortRelations
 * @package LifeOnScreen\SortRelations
 */
trait SortRelations
{
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
        if (is_string(self::$sortRelations[$column])) {
            return $query->orderBy(self::$sortRelations[$column], $direction);
        }
        foreach (self::$sortRelations[$column] as $orderColumn) {
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

        foreach ($orderings as $column => $direction) {
            if (array_key_exists($column, self::$sortRelations)) {
                $query = self::applyRelationOrderings($column, $direction, $query);
            } else {
                $query->orderBy($column, $direction);
            }
        }

        return $query;
    }
}