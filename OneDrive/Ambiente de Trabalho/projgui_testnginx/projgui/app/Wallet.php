<?php

namespace App;


use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use   Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'email', 'ballance',
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
