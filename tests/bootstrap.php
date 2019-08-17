<?php

if (isset($_ENV['BOOTSTRAP_CLEAR_CACHE_ENV'])) {
    // executes the "php bin/console cache:clear" command
    passthru(sprintf(
        'APP_ENV=%s php "%s/../bin/console" cache:clear --no-warmup',
        $_ENV['BOOTSTRAP_CLEAR_CACHE_ENV'],
        __DIR__
    ));
}

print 'Loading DB test data fixtures...' . PHP_EOL;

passthru(sprintf(
    'php "%s/../bin/console" doctrine:fixtures:load --no-interaction',
    __DIR__
));

print PHP_EOL;

require __DIR__.'/../config/bootstrap.php';