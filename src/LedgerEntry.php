<?php

namespace FannyPack\Ledger;

use App\User;
use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    /**
     * The attributes that should be cast to native types.
     * 
     * @var array
     */
    protected $casts = [
        'credit' => 'boolean',
        'debit' => 'boolean',
        'created_at' => 'datetime'
    ];


    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = "ledger_entries";

    /**
     * @var array
     */
    protected $fillable = ['reason', 'debit', 'credit', 'amount', 'current_balance', 'money_to', 'money_from'];

    protected $hidden = ['ledgerable_id', 'ledgerable_type', 'current_balance', 'updated_at'];

    /**
     * Get the ledgerable entity that the entry belongs to.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function ledgerable()
    {
        return $this->morphTo();
    }
}
