<?php

namespace Mytdt\Model;

trait MyJoinTableTrait
{
    /**
     * Efetua join na query com os dados informados.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $table
     * @param string $one
     * @param string $operator
     * @param string $two
     * @param string $type
     * @param string $where
     * 
     * @return \Illuminate\Database\Query\Builder
     */
    protected function myJoinTable($query, $table, $one, $operator, $two, $type = 'inner', $where = false)
    {
        // obtém os joins da query
        $joins = $query->getQuery()->joins;
        if ($joins) {
            // para cada join
            // verifica se a tabela já está no relacionamento
            foreach ($joins as $join) {
                if ($join->table == $table) {
                    return $query;
                }
            }
        }
        // efetua o join na query
        return $query->join($table, $one, $operator, $two, $type, $where);
    }

}