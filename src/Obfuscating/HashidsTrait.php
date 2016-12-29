<?php

namespace Mytdt\Obfuscating;

use Hashids\Hashids;

trait HashidsTrait
{
    /**
     * Instância do objeto Hashids para obfuscação do ID.
     *
     * @var \Vinkla\Hashids\HashidsManager
     */
    protected $hashids;

    /**
     * Instância anterior do objeto Hashids.
     *
     * @var \Vinkla\Hashids\HashidsManager
     */
    protected $oldHashids;

    /**
     * Configuração do Hashids.
     *
     * @var array
     */
    protected $hashidsConfig = [
        'salt' => '',
        'length' => '',
        'alphabet' => '',
    ];

    /**
     * Inicializa o objeto Hashids.
     *
     * @return void
     */
    public function hashids()
    {
        // obtém configuração do salt, length e alphabet
        // inicializa o objeto com as configurações
        $salt = $this->getHashidsConfig('salt');
        $length = $this->getHashidsConfig('length');
        $alphabet = $this->getHashidsConfig('alphabet');
        $this->hashids = new Hashids($salt, $length, $alphabet);
    }

    /**
     * Obfusca o valor informado em um Hashids.
     *
     * @param mixed $id
     *
     * @return string
     */
    public function encodeHashids($id)
    {
        return $this->hashids->encode($id);
    }

    /**
     * Decodifica o Hashids informado.
     *
     * @param string $id
     *
     * @return int|null
     */
    public function decodeHashids($id)
    {
        // decodifica o código
        // concatena o array de retorno para formar uma string
        $ret = '';
        $decode = $this->hashids->decode($id);
        foreach ($decode as $index => $value) {
            $ret .= $value;
        }

        return $ret ?: null;
    }

    /**
     * Decodifica o Hashids informado de acordo com o tipo do recurso.
     *
     * @param string $id
     * @param string $type
     *
     * @return int|null
     */
    public function decodeHashidsWithType($id, $type)
    {
        $salt = str_replace('resource', $type, env('HASHIDS_SALT'));

        return $this->decodeHashidsWithSalt($id, $salt);
    }

    /**
     * Decodifica o Hashids informado de acordo com determinado salt.
     *
     * @param string $id
     * @param string $salt
     *
     * @return int|null
     */
    public function decodeHashidsWithSalt($id, $salt)
    {
        // salva hashids atual
        // modifica o salt
        // e inicializa um novo hashids
        $this->oldHashids = $this->hashids;
        $this->hashidsConfig = [
            'salt' => $salt,
        ];
        $this->hashids();

        // decodifica
        // restaura hashids antigo
        // e retorna a decodificação
        $ret = $this->decodeHashids($id);
        $this->hashids = $this->oldHashids;

        return $ret ?: null;
    }

    /**
     * Codifica o Hashids informado de acordo com o tipo do recurso.
     *
     * @param string $id
     * @param string $type
     *
     * @return string
     */
    public function encodeHashidsWithType($id, $type)
    {
        $salt = str_replace('resource', $type, env('HASHIDS_SALT'));

        return $this->encodeHashidsWithSalt($id, $salt);
    }

    /**
     * Codifica o Hashids informado de acordo com determinado salt.
     *
     * @param string $id
     * @param string $salt
     *
     * @return string
     */
    public function encodeHashidsWithSalt($id, $salt)
    {
        // salva hashids atual
        // modifica o salt
        // e inicializa um novo hashids
        $this->oldHashids = $this->hashids;
        $this->hashidsConfig = [
            'salt' => $salt,
        ];
        $this->hashids();

        // codifica
        // restaura hashids antigo
        // e retorna a decodificação
        $ret = $this->encodeHashids($id);
        $this->hashids = $this->oldHashids;

        return $ret;
    }

    /**
     * Retorna configuração do Hashids.
     *
     * @param string $key
     *
     * @return mixed
     */
    private function getHashidsConfig($key)
    {
        // obtém o valor da key através da configuração do ambiente
        $value = env('HASHIDS_'.strtoupper($key));
        
        // se a key já existir na configuração do hashid, despreza o valor do ambiente e utiliza o já existente
        if (array_key_exists($key, $this->hashidsConfig)) {
            if ($this->hashidsConfig[$key]) {
                $value = $this->hashidsConfig[$key];
            }
        }

        return $value;
    }
}
