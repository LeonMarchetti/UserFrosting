<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class UsuarioUnluAuxScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $baseTable = $model->getTable();
        // Hardcode the table name here, or you can access it using the classMapper and `getTable`
        $auxTable = 'members';

        // Specify columns to load from base table and aux table
        $builder->addSelect(
            "$baseTable.*",
            "$auxTable.telefono as telefono",
            "$auxTable.institucion as institucion",
            "$auxTable.dependencia as dependencia",
            "$auxTable.rol as rol",
            "$auxTable.activo as activo"
        );

        // Join on matching `usuario_unlu` records
        $builder->leftJoin($auxTable, function ($join) use ($baseTable, $auxTable) {
            $join->on("$auxTable.id", '=', "$baseTable.id");
        });
    }
}