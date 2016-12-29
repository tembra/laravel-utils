<?php

namespace Mytdt\Seeder;

class MyHasMany
{
    /**
     * Os relacionamentos do seeder.
     *
     * @var array
     */
    protected $relations = [];

    /**
     * Método construtor da classe.
     *
     * @param array $relations
     *
     * @return void
     */
    public function __construct(array $relations = null)
    {
        $this->relations = $relations;
    }

    /**
     * Obtém o relacionamento de determinada classe.
     *
     * @param string $class
     *
     * @return string|null
     */
    private function getRelation($class)
    {
        return array_key_exists($class, $this->relations) ? $this->relations[$class] : null;
    }

    /**
     * Cria uma quantidade randômica de relacionamentos recursivamente.
     *
     * @param string                              $class
     * @param \Illuminate\Database\Eloquent\Model $obj
     * @param \Illuminate\Database\Eloquent\Model $relObj
     *
     * @return void
     */
    public function createMany($class, $obj, $relObj = null)
    {
        // obtém o relacionamento da classe
        $relation = $this->getRelation($class);

        // determina array de objetos que serão adicionados
        $saveMany = [];

        // se não existir objeto relacionado, obtém todos os registros do relacionamento
        // caso contrário obtém os registros do relacionamento pela chave estrangeira
        if (is_null($relObj)) {
            $all = $relation::all();
        } else {
            $all = $relation::where($relObj->getForeignKey(), $relObj->id)->get();
        }

        // obtém a quantidade de registros retornados
        // randomiza a quantidade de relacionamentos a ser inserida
        $max = $all->count();
        $qtd = mt_rand(1, $max);
        // para cara um
        for ($i = 0; $i < $qtd; $i++) {
            // obtém um registro aleatório
            // adiciona no array de objetos
            // remove da lista dos registros
            $one = $all->random();
            $saveMany[] = $one;
            $all = $all->diff([$one]);

            // se este relacionamento tiver outros relacionamentos
            // chama a função recursivamente passando objeto recente como objeto relacional
            if (!is_null($this->getRelation($relation))) {
                $this->createMany($relation, $obj, $one);
            }
        }

        // obtém a função responsável pelo relacionamento executando no $obj o nome da tabela do relacionamento
        // salva todos os relacionamentos
        $relation = call_user_func([$obj, $one->getTable()]);
        $relation->saveMany($saveMany);
    }
}
