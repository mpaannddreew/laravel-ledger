<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 2017-06-24
 * Time: 12:26 PM
 */

namespace FannyPack\Ledger\Traits;


use FannyPack\Ledger\Facades\Ledger;
use FannyPack\Ledger\LedgerEntry;

trait Ledgerable
{
    /**
     * Get all of the entity's ledger entries.
     * 
     * @return mixed
     */
    public function entries()
    {
        return $this->morphMany(LedgerEntry::class, 'ledgerable')->orderBy('id', 'desc');
    }

    /**
     * Get all of the entity's ledger debit entries.
     * 
     * @return mixed
     */
    public function debits()
    {
        return $this->entries()->where('debit', '=', 1);
    }

    /**
     * Get all of the entity's ledger credit entries.
     * 
     * @return mixed
     */
    public function credits()
    {
        return $this->entries()->where('credit', '=', 1);
    }

    /**
     * debit entity
     *
     * @param $from
     * @param $amount
     * @param $reason
     * @return mixed
     */
    public function debit($from, $amount, $amount_currency, $reason)
    {
        return Ledger::debit($this, $from, $amount, $amount_currency, $reason);
    }

    /**
     * credit entity
     *
     * @param $to
     * @param $amount
     * @param $reason
     * @return mixed
     */
    public function credit($to, $amount, $amount_currency, $reason)
    {
        return Ledger::credit($this, $to, $amount, $amount_currency, $reason);
    }

    /**
     * get entity's balance
     * 
     * @return mixed
     */
    public function balance()
    {
        $credits = $this->credits()->sum('amount');
        $debits = $this->debits()->sum('amount');
        $balance = $debits - $credits;
        return $balance;
    }

    /**
     * transfer amount from entity to each recipient
     * 
     * @param $to
     * @param $amount
     * @param $reason
     * @return mixed
     */
    public function transfer($to, $amount, $reason)
    {
        return Ledger::transfer($this, $to, $amount, $reason);
    }
}