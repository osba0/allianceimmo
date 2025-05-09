<?php

namespace App\Libraries\LaravelVueDatatable;

use JamesDordoy\LaravelVueDatatable\Classes\Filters\FilterBelongsToManyRelationships;
use JamesDordoy\LaravelVueDatatable\Classes\Filters\FilterHasManyRelationships;
use JamesDordoy\LaravelVueDatatable\Classes\Joins\JoinBelongsToManyRelationships;
use JamesDordoy\LaravelVueDatatable\Classes\Joins\JoinBelongsToRelationships;
use JamesDordoy\LaravelVueDatatable\Classes\Joins\JoinHasManyRelationships;

class QueryBuilder extends \JamesDordoy\LaravelVueDatatable\Classes\QueryBuilder
{
    /**
     * @param $searchValue
     * @return $this|\JamesDordoy\LaravelVueDatatable\Classes\QueryBuilder
     * @throws \JamesDordoy\LaravelVueDatatable\Exceptions\RelationshipColumnsNotFoundException
     * @throws \JamesDordoy\LaravelVueDatatable\Exceptions\RelationshipModelNotSetException
     */
    public function filter($searchValue)
    {
        if (isset($searchValue) && ! empty($searchValue)) {

            $filterLocalData = new FilterLocalData;
            $this->query = $filterLocalData($this->query, $searchValue, $this->model, $this->localColumns);

            $filterBelongsTo = new FilterBelongsToRelationships;
            $this->query = $filterBelongsTo($this->query, $searchValue, $this->relationshipModelFactory, $this->model, $this->relationships);

            $filterHasMany = new FilterHasManyRelationships;
            $this->query = $filterHasMany($this->query, $searchValue, $this->relationshipModelFactory, $this->model, $this->relationships);

            $filterBelongsToMany = new FilterBelongsToManyRelationships;
            $this->query = $filterBelongsToMany($this->query, $searchValue, $this->relationshipModelFactory, $this->model, $this->relationships);

            return $this;
        }

        return $this;
    }

    public function selectData($dataTableColumns = [], $dataTableRelationships = [])
    {
        //Select local data
        $columnKeys = $this->selectModelColumns();
        $columnKeys = $this->selectLocalForeignKeysForJoining($columnKeys);

        $joinBelongsTo = new JoinBelongsToRelationships;
        $this->query = $joinBelongsTo($this->query, $this->model, $this->relationships, $this->relationshipModelFactory);

        $joinHasMany = new JoinHasManyRelationships;
        $this->query = $joinHasMany($this->query, $this->model, $this->relationships, $this->relationshipModelFactory);

        $joinBelongMany = new JoinBelongsToManyRelationships;
        $this->query = $joinBelongMany($this->query, $this->model, $this->relationships, $this->relationshipModelFactory);

        if (count($columnKeys)) {
            $this->query = $this->query->select($columnKeys);
        }

//       $this->query->groupBy($this->model->getTable().".".$this->model->getKeyName());

        return $this;
    }
}
