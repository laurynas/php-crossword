<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<head>
<style>
body, td { font-family: Courier; font-size: 10pt; }
.crossTable { border-spacing:0px;  border-collapse: collapse; }
.cellEmpty {  padding: 0px; }
.cellLetter { padding: 2px; background-color: #EEEEEE; border: 1px solid #000000; width: 20px; height: 20px; }
</style>
</head>
<body>

<?php
require 'init.php';

$groupid = 'demo';

$pc->setGroupID($groupid);

switch ($_REQUEST['act'])
{
	case 'add_single':
		$pc->insertWord($_REQUEST['word'], $_REQUEST['question']);
		break;
}
?>

<table align="center">
<tr>
	<td>Group ID:</td>
	<td><b><?=$pc->groupid?></b></td>
</tr>
<tr>
	<td>Words in group:</td>
	<td><b><?=$pc->countWordsInGroup()?></b></td>
</tr>
<tr>
	<td colspan="2">&nbsp;</td>
</tr>
<form action="?act=add_single" method="post">
<tr>
	<td>Word:</td>
	<td><input type="text" name="word" /></td>
</tr>
<tr>
	<td>Question:</td>
	<td><input type="text" name="question" /></td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" value="Add one word" /></td>
</tr>
</form>
</table>


</body>
</html>