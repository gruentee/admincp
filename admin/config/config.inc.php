<?php

/*
 * 'development' für lokale Entwicklungsumgebung
 * 'production' für Produktivumgebung
 */
$deployment_mode = 'development';

$picDir         = PUB_ROOT."photos/pics/";
$thumbDir       = PUB_ROOT."photos/thumbs/";
$picMaxWidth    = 600;
$thumbWidth     = 100;

// TODO: einzubindendes MySQL-File auswählen


// Error-Reporting anpassen
if($deployment_mode == 'development')
{
    error_reporting(E_ERROR | E_PARSE);
}
else
{
    // TODO: set production env error reporting
    error_reporting(E_ERROR | E_PARSE);
}
