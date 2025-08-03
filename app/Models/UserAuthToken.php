<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserAuthToken extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_token_auth';


    /**
     * Verifica se o token é
     * válido
     *
     * @return boolean
     */
    public function isValid()
    {
        $agora = Carbon::now();
        if ($agora->greaterThan($this->validity) || $this->was_used === 1) {
            // É inválido
            return false;
        }
        return true;
    }


    /**
     * Gera um novo token
     *
     * @param integer $user_id ID do usuário
     * @param string $tipo Tipo do hashcode: numerico (6 numeros) ou string (tamanho 40)
     * @param integer $validade Validade em minutos; padrão: 10 minutos
     * @return \self
     */
    public static function new($user_id, $tipo = 'number', $validade = 10)
    {
        // Apaga os anteriores se houver
        DB::table('users_token_auth')
            ->where('id_user', $user_id)
            ->delete();

        $agora = Carbon::now();
        $t = new self();

        $t->id_user = $user_id;
        $t->validity = $agora->addMinutes($validade);
        $t->was_used = 0;

        if ($tipo === 'number') {
            $t->hashcode = str_pad(rand(1, 999999), 6, "0", STR_PAD_LEFT);
        } else if ($tipo === 'alfa') {
            $t->hashcode = Str::random(40);
        } else {
            throw new \Exception('Tipo definido inválido');
        }
        $t->save();
        return $t;
    }

    /**
     * pega um novo token
     *
     * @param string $tipo Tipo do hashcode: numerico (6 numeros) ou string (tamanho 40)
     * @param integer $user_id ID do usuário
     * @return \self
     */
    public static function getToken($hashcode, $user_id)
    {
        // Como é um método estático, instanciamos abaixo...
        $t = new self();
        return $t->where('hashcode', $hashcode)
            ->where('id_user', $user_id)
            ->orderBy('id', 'desc')
            ->first();
    }
}
