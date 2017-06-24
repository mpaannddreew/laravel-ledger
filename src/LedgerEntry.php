<?php

namespace FannyPack\Ledger;

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
        'debit' => 'boolean'
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
    protected $fillable = ['reason', 'debit', 'credit', 'amount', 'current_balance'];

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
