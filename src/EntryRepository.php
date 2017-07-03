<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 2017-06-25
 * Time: 9:49 AM
 */

namespace FannyPack\Ledger;
use Carbon\Carbon;

class EntryRepository
{
    /**
     * @var Carbon
     */
    protected $time;

    /**
     * record fields to retrieve
     *
     * @var array
     */
    protected $fields = ['debit', 'credit', 'amount', 'ledgerable_id', 'created_at', 'id'];

    /**
     * record type
     *
     * @var array
     */
    protected $entry_type = ['credit' => 'credit', 'debit' => 'debit'];

    /**
     * EntryRepository constructor.
     * @param Carbon $carbon
     */
    public function __construct(Carbon $carbon)
    {
        $this->time = $carbon->setTimezone($carbon->getTimezone());
    }

    /**
     * get ledger entries
     *
     * @param int $days_ago
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getEntries($days_ago = 0, $offset = 0, $limit = 10)
    {
        if($days_ago)
            return $this->fromDaysAgo(null, $days_ago, $offset, $limit);

        $response = [];
        $entries = LedgerEntry::select()->latest()->skip($offset)->take($limit)->orderBy('id', 'desc')->get();;
        foreach ($entries as $entry)
        {
            $item = $entry->toArray();
            $item['name'] = $entry->ledgerable->name;
            array_push($response, $item);
        }

        return $response;
    }

    /**
     * get ledger entries according to type
     *
     * @param $type
     * @param int $days_ago
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getTypeEntries($type, $days_ago = 0, $offset = 0, $limit = 10)
    {
        if($days_ago)
            return $this->fromDaysAgo($type, $days_ago, $offset, $limit);

        if (!array_has($this->entry_type, strtolower($type)))
            return [];

        $response = [];
        $entries = LedgerEntry::select()->where(strtolower($type), '=', 1)->latest()->skip($offset)->take($limit)->orderBy('id', 'desc')->get();
        foreach ($entries as $entry)
        {
            $item = $entry->toArray();
            $item['name'] = $entry->ledgerable->name;
            array_push($response, $item);
        }
        return $response;
    }

    /**
     * find a specific ledger entry
     *
     * @param $entry_id
     * @return mixed
     */
    public function find($entry_id)
    {
        $entry = LedgerEntry::find($entry_id);

        $item = $entry->toArray();
        $item['name'] = $entry->ledgerable->name;

        return $item;
    }

    /**
     * get entries starting from specific date
     *
     * @param $date
     * @param int $days_from_date
     * @param null|string $type
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function findFromDate($date, $days_from_date = 1, $type = null, $offset = 0, $limit = 100)
    {
        $response = [];
        list($year, $month, $day) = $this->getYearMonthDay($date);
        
        $datetime = $this->time->setDate($year, $month, $day)->setTimeFromTimeString("00:01:00");

        if ($type){
            if (!array_has($this->entry_type, strtolower($type)))
                return [];


            $entries = LedgerEntry::select()->where($type, '=', 1)->where('created_at', '>=', $datetime->toDateTimeString())
                ->where('created_at', '<=', $datetime->addDay($days_from_date)->toDateTimeString())
                ->latest()->skip($offset)->take($limit)->orderBy('id', 'desc')->get();
            foreach ($entries as $entry)
            {
                $item = $entry->toArray();
                $item['name'] = $entry->ledgerable->name;
                array_push($response, $item);
            }

            return $response;
        }

        $entries = LedgerEntry::select()->where('created_at', '>=', $datetime->toDateTimeString())
            ->where('created_at', '<=', $datetime->addDay($days_from_date)->toDateTimeString())
            ->latest()->skip($offset)->take($limit)->orderBy('id', 'desc')->get();
        foreach ($entries as $entry)
        {
            $item = $entry->toArray();
            $item['name'] = $entry->ledgerable->name;
            array_push($response, $item);
        }

        return $response;
    }

    /**
     * get entries from a number of days ago
     *
     * @param null|string $type
     * @param int $days_ago
     * @param int $offset
     * @param int $limit
     * @return array
     */
    protected function fromDaysAgo($type = null, $days_ago = 0, $offset = 0, $limit = 10)
    {
        $response = [];
        $datetime = $this->dateFromThen($days_ago);
        
        if ($type){
            if (!array_has($this->entry_type, strtolower($type)))
                return [];

            $entries = LedgerEntry::select()->where($type, '=', 1)->where('created_at', '>=', $datetime->toDateTimeString())->latest()->skip($offset)->take($limit)->orderBy('id', 'desc')->get();
            foreach ($entries as $entry)
            {
                $item = $entry->toArray();
                $item['name'] = $entry->ledgerable->name;
                array_push($response, $item);
            }

            return $response;
        }

        $entries = LedgerEntry::select()->where('created_at', '>=', $datetime->toDateTimeString())->latest()->skip($offset)->take($limit)->orderBy('id', 'desc')->get();
        foreach ($entries as $entry)
        {
            $item = $entry->toArray();
            $item['name'] = $entry->ledgerable->name;
            array_push($response, $item);
        }

        return $response;
    }

    /**
     * get datetime from string
     *
     * @param string $datetime
     * @return static
     */
    protected function fromDateTimeString($datetime)
    {
        $datetime = explode(' ', $datetime);
        $date = $datetime[0];
        $time = $datetime[1];
        
        list($year, $month, $day) = $this->getYearMonthDay($date);

        return $this->time->setDate($year, $month, $day)->setTimeFromTimeString($time);
    }

    /**
     * get date days ago
     *
     * @param $days
     * @return static
     */
    protected function dateFromThen($days)
    {
        return $this->time->now()->subDays($days);
    }

    /**
     * get year, month, day from date string
     *
     * @param $date
     * @return array
     */
    protected function getYearMonthDay($date)
    {
        return explode('-', $date);
    }
}