<?php
/**
 * tests/mail_probe.php — Print resolved mail config for precedence tests.
 * Usage: php tests/mail_probe.php   (env vars control the outcome)
 * Never sends mail — constants only.
 */

define('PROJECT_ROOT', realpath(__DIR__ . '/..'));
require PROJECT_ROOT . '/config/config.php';

echo 'MAIL_TO=' . MAIL_TO . PHP_EOL;
echo 'MAIL_FROM=' . MAIL_FROM . PHP_EOL;
