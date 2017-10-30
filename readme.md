## About this library

This is a simple/basic implementation of a ledger in laravel 5

## Actions supported
- RECORDING DEBITS
- RECORDING CREDITS
- BALANCE COMPUTATION

## Installation

#### Composer 
Above installation can also be simplify by using the following command:

```php
composer require fannypack/ledger
```

#### Service Provider and Facade

Add `FannyPack\Ledger\LedgerServiceProvider::class` to your application service providers in `config/app.php` file:

```php
FannyPack\Ledger\LedgerServiceProvider::class,
```

You can use the facade for shorter code. Add this to your aliases:

```php
'Ledger' => FannyPack\Ledger\Facades\Ledger::class,
```
#### Migrations

Finally, you'll also need to run migration on the package:
```
php artisan vendor:publish
```

#### Vue
Optionally, this command will copy the library's vue components into your codebase. You can publish with this command:
```
php artisan vendor:publish --tag=ledger
```

Using the provided `Ledger.vue` component in your blade templates
```php
<ledger></ledger>
```

Register package routes in your app's `RouteServiceProvider` boot method
```
public function boot() {
    Ledger::routes();
}
```

## Usage

Add `Ledgerable` trait to your model. See the following example:

```php
namespace App;

use FannyPack\Ledger\Traits\Ledgerable;
use Illuminate\Database\Eloquent\Model;

class Account extends Model {
    use Ledgerable;
}
```

#### Show available balance
```php
$account = Account::find(1);
$balance = Ledger::balance($account);

// or

$account = Account::find(1);
$balance = $account->balance();
```
#### Record a credit entry
```php
$account = Account::find(1);
Ledger::credit($account, $to, $amount, $reason);

// or

$account = Account::find(1);
$account->credit($to, $amount, $reason);
```
#### Record a debit entry
```php
$account = Account::find(1);
Ledger::debit($account, $from, $amount, $reason);

// or

$account = Account::find(1);
$account->debit($from, $amount, $reason);
```

#### Recording debits and credits in one transaction
```php
$account = Account::find(1);
$account2 = Account::find(2);
$account3 = Account::find(3);
Ledger::transfer($account, [$account2, $account3], $amount, $reason);

// or

Ledger::transfer($account, $account2, $amount, $reason);
Ledger::transfer($account, $account3, $amount, $reason);
```

```php
$account = Account::find(1);
$account2 = Account::find(2);
$account3 = Account::find(3);
$account->transfer([$account2, $account3], $amount, $reason);

// or

$account->transfer($account2, $amount, $reason);
$account->transfer($account3, $amount, $reason);
```
#### Retrieving all entries of a ledgerable
```php
$account = Account::find(1);
$entries = $account->entries();
```
#### Retrieving all debits of a ledgerable
```php
$account = Account::find(1);
debits = $account->debits();
```
#### Retrieving all credits of a ledgerable
```php
$account = Account::find(1);
debits = $account->credits();
```

## Bugs
For any bugs found, please email me at andrewmvp007@gmail.com or register an issue at [issues](https://github.com/mpaannddreew/laravel-ledger/issues)
