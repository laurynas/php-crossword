<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$charset?>">
<title>PHP Crossword Generator</title>
<style>
body, td { font-family: Courier; font-size: 10pt; }
.crossTable { border-spacing:0px;  border-collapse: collapse; }
.cellEmpty {  padding: 0px; }
.cellLetter { padding: 1px; background-color: #EEEEEE; border: 1px solid #000000; width: 20px; height: 20px; }
.cellDebug { padding: 1px; border: 1px solid #000000; width: 20px; height: 20px; }
</style>
</head>
<body>

<?php
require 'init.php';
?>

<div align="center">

<form method="post">
	Add some words: <br />
	<textarea name="words" cols="30" rows="10"><?=htmlspecialchars(stripslashes($_REQUEST['words']))?></textarea>
	<br /><br />
	<input type="submit" value="Generate" />
</form>

<? if (!empty($_REQUEST['words'])): ?>

	<?
	$success = $pc->generateFromWords($_REQUEST['words']);
	?>
	
	<? if (!$success): ?>
	
		SORRY, UNABLE TO GENERATE CROSSWORD FROM YOUR WORDS
		
	<? else: ?>
	
	<?
	$html = $pc->getHTML($_REQUEST['colors']);
	$words = $pc->getWords();
	?>
	
	<p><?=$html?></p>
	
	<p><b>Words: <?=count($words)?></b></p>
	
	<table border=1 align="center">
	<tr>
		<th>Nr.</th>
		<th>Word</th>
		<th>X</th>
		<th>Y</th>
		<th>Axis</th>
	</tr>
	<? foreach ($words as $key=>$word): ?>
	<tr>
		<td><?=$key+1?>.</td>
		<td><?=$word["word"]?></td>
		<td><?=$word["x"]?></td>
		<td><?=$word["y"]?></td>
		<td><?=$word["axis"]?></td>
	</tr>
	<? endforeach; ?>
	</table>
	
	<? endif; ?>

<? endif; ?>

<?=sprintf("<p>Generated in %.4f sec.</p>",(getmicrotime() - $script_start))?>

</div>

</body>
</html>