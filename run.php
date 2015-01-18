#! /usr/bin/env php

<?php

use Fb2pdf\Flipperbook2pdfCommand;
use Symfony\Component\Console\Application;

require 'vendor/autoload.php';

$app = new Application('Flipperbook to PDF', '1.0');

$app->add(new Flipperbook2pdfCommand());

$app->run();
