<?php

namespace LifeOnScreen\SortRelations;

use Illuminate\Support\Str;

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
    public static function sortableRelations($query): array
    {
        $model = $query->getModel();
        $return = [];
        if (static::$sortRelations) {
            foreach (static::$sortRelations as $relation => $columns) {
                $relatedKey = $model->{$relation}()->getForeignKeyName();
                $return[$relatedKey] = ['relation' => $relation, 'columns' => $columns];
            }
        }
        return $return;
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
        $sortRelations = static::sortableRelations($query);

        $model = $query->getModel();
        $relation = $sortRelations[$column];
        $related = $model->{$relation['relation']}()->getRelated();

        $foreignKey =  $model->{$relation['relation']}()->getForeignKeyName();
        $ownerKey = $model->{$relation['relation']}()->getOwnerKeyName();

        $query->select($model->getTable() . '.*');
        $query->leftJoin($related->getConnection()->getDatabaseName() . '.' . $related->getTable(), $model->qualifyColumn($foreignKey), '=', $related->qualifyColumn($ownerKey));
        if (is_string($relation['columns'])) {
            $qualified = $related->qualifyColumn($relation['columns']);
            $query->orderBy($qualified, $direction);
        }
        if (is_array($relation['columns'])) {
            foreach ($relation['columns'] as $orderColumn) {
                $qualified = $related->qualifyColumn($orderColumn);
                $query->orderBy($qualified, $direction);
            }
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

        $sortRelations = static::sortableRelations($query);

        foreach ($orderings as $column => $direction) {
            if (is_null($direction))
              $direction = 'asc';
            if (array_key_exists($column, $sortRelations)) {
                $query = self::applyRelationOrderings($column, $direction, $query);
            } else {
                $query->orderBy($column, $direction);
            }
        }

        return $query;
    }
}
