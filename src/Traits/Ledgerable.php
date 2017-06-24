<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 2017-06-24
 * Time: 12:26 PM
 */

namespace FannyPack\Ledger\Traits;


use FannyPack\Ledger\LedgerEntry;
use Ledger;

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
     * @param $amount
     * @param $reason
     * @return mixed
     */
    public function debit($amount, $reason)
    {
        return Ledger::debit($this, $amount, $reason);
    }

    /**
     * credit entity
     * 
     * @param $amount
     * @param $reason
     * @return mixed
     */
    public function credit($amount, $reason)
    {
        return Ledger::credit($this, $amount, $reason);
    }

    /**
     * get entity's balance
     * 
     * @return mixed
     */
    public function balance()
    {
        return Ledger::balance($this);
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