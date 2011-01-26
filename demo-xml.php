<?php
require 'init.php';

header("Content-type: text/xml; charset={$charset}");
echo "<?xml version=\"1.0\" encoding=\"{$charset}\"?>\n";

$pc->generate();

echo $pc->getXML();
?>