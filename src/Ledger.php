<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 2017-06-24
 * Time: 1:33 PM
 */

namespace FannyPack\Ledger;


use FannyPack\Ledger\Exceptions\InvalidRecipientException;
use FannyPack\Ledger\Exceptions\InsufficientBalanceException;
use Illuminate\Routing\Router;

class Ledger
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * Ledger constructor.
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * debit a ledgerable instance
     * 
     * @param $ledgerable
     * @param $amount
     * @param $reason
     * @return mixed
     */
    public function debit($ledgerable, $amount, $reason)
    {
        $entry = $ledgerable->entries()->first();
        $balance = $entry ? $entry->current_balance : 0;
        $data = [
            'debit' => 1, 'reason' => $reason, 'amount' => $amount, 'current_balance' => (int)$balance + (int)$amount
        ];

        return $this->log($ledgerable, $data);
    }

    /**
     * credit a ledgerable instance
     * 
     * @param $ledgerable
     * @param $amount
     * @param $reason
     * @return mixed
     * @throws InsufficientBalanceException
     */
    public function credit($ledgerable, $amount, $reason)
    {
        $entry = $ledgerable->entries()->first();
        $balance = $entry ? $entry->current_balance : 0;
        if ((int)$balance == 0 || (int)$amount > (int)$balance )
            throw new InsufficientBalanceException("Insufficient balance");
        
        $data = [
            'credit' => 1, 'reason' => $reason, 'amount' => $amount, 'current_balance' => (int)$balance - (int)$amount
        ];

        return $this->log($ledgerable, $data);
    }

    /**
     * persist an entry to the ledger
     * 
     * @param $ledgerable
     * @param array $data
     * @return mixed
     */
    protected function log($ledgerable, array $data)
    {
        return $ledgerable->entries()->create($data);
    }

    /**
     * balance of a ledgerable instance
     * 
     * @param $ledgerable
     * @return int
     */
    public function balance($ledgerable)
    {
        $entry = $ledgerable->entries()->first();
        return $entry ? $entry->current_balance : 0;
    }

    /**
     * transfer an amount to each ledgerable instance
     * 
     * @param $from
     * @param $to
     * @param $amount
     * @param string $reason
     * @return mixed
     * @throws InvalidRecipientException
     * @throws InsufficientBalanceException
     */
    public function transfer($from, $to, $amount, $reason = "funds transfer")
    {
        if (!is_array($to))
            return $this->transferOnce($from, $to, $amount, $reason);

        $total_amount = (int)$amount * count($to);
        if ($total_amount > $from->balance())
            throw new InsufficientBalanceException("Insufficient balance");
        
        $recipients = [];
        foreach ($to as $recipient)
        {
            array_push($recipients, $this->transferOnce($from, $recipient, $amount, $reason));
        }
        
        return $recipients;
    }

    /**
     * transfer an amount to one ledgerable instance
     *
     * @param $from
     * @param $to
     * @param $amount
     * @param $reason
     * @return mixed
     * @throws InsufficientBalanceException
     * @throws InvalidRecipientException
     */
    protected function transferOnce($from, $to, $amount, $reason)
    {
        if (get_class($from) == get_class($to) && $from->id == $to->id)
            throw new InvalidRecipientException("Source and recipient cannot be the same object");

        $this->credit($from, $amount, $reason);
        return $this->debit($to, $amount, $reason);
    }

    /**
     * register routes for ledger api access
     */
    public function routes()
    {
        $this->router->group(['namespace' => 'FannyPack\Ledger\Http\Controllers'], function() {
            $this->router->resource('ledger', 'LedgerController');
        });
    }
}