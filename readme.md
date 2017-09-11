## About this library

This is a simple/basic implementation of a ledger in laravel 5

## Actions supported
- RECORDING DEBITS
- RECORDING CREDITS
- BALANCE COMPUTATION

## Installation
git clone https://github.com/mpaannddreew/laravel-ledger.git

Register service provider
```php
FannyPack\Ledger\LedgerServiceProvider::class,
```
Register Facade
Register service provider
```php
'Ledger' => FannyPack\Ledger\Facades\Ledger::class,
```

After the service provider is registered run this command
```
php artisan vendor:publish --tag=ledger
```
This command will copy the library's vue components into your codebase

## Environment setup
Register package routes in your app's RouteServiceProvider
```
Ledger::routes();
```

## Usage
Using it with your models, add 
```php
namespace App;

use FannyPack\Ledger\Traits\Ledgerable;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use Ledgerable;
}
```

Show available balance
```php
$account = Account::find(1);
$balance = Ledger::balance($account);
```
or
```php
$account = Account::find(1);
$balance = $account->balance();
```
Record a credit entry
```php
$account = Account::find(1);
Ledger::credit($account, $to, $amount, $reason);
```
or
```php
$account = Account::find(1);
$account->credit($to, $amount, $reason);
```
Record a debit entry
```php
$account = Account::find(1);
Ledger::debit($account, $from, $amount, $reason);
```
or
```php
$account = Account::find(1);
$account->debit($from, $amount, $reason);
```

Recording debits and credits in one transaction
```php
$account = Account::find(1);
$account2 = Account::find(2);
$account3 = Account::find(3);
Ledger::transfer($account, [$account2, $account3], $amount, $reason)
// or
Ledger::transfer($account, $account2, $amount, $reason)
Ledger::transfer($account, $account3, $amount, $reason)
```
or
```php
$account = Account::find(1);
$account2 = Account::find(2);
$account3 = Account::find(3);
$account->transfer([$account2, $account3], $amount, $reason)
// or
$account->transfer($account2, $amount, $reason)
$account->transfer($account3, $amount, $reason)
```
Retrieving all entries of a ledgerable
```php
$account = Account::find(1);
$entries = $account->entries()
```
Retrieving all debits of a ledgerable
```php
$account = Account::find(1);
debits = $account->debits()
```
Retrieving all credits of a ledgerable
```php
$account = Account::find(1);
debits = $account->credits()
```

## Bugs
For any bugs found, please email me at andrewmvp007@gmail.com or register an issue at [issues](https://github.com/mpaannddreew/beyonic-laravel/issues)
