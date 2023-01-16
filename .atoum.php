<?php

$runner->addTestsFromDirectory(__DIR__ . '/tests/units');

$script->noCodeCoverageForNamespaces('atoum', 'Symfony');
$script->bootstrapFile(__DIR__ . DIRECTORY_SEPARATOR . '.atoum.bootstrap.php');
