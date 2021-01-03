# closure-command

## Installation

```bash
composer require friendsofhyperf/closure-command
```

## Publish

```bash
php bin/hyperf.php vendor:publish friendsofhyperf/closure-command
```

## Usage

```php
// config/console.php

use FriendsOfHyperf/ClosureCommand/Console;

Console::command('foo:bar', function() {
    $this->info('Command foo:bar executed.');
});
```
