<?php
require 'init.php';

$success = $pc->generate();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$charset?>">
<title>PHP Crossword Generator</title>
<style>
body, td { font-family: Courier; font-size: 10pt; }
.crossTable { border-spacing:0px;  border-collapse: collapse; }
.cellEmpty {  padding: 0px; }
.cellNumber { padding: 1px; background-color: #FFFFFF; border: 0px solid #000000; width: 20px; height: 20px; }
.cellLetter { padding: 1px; background-color: #EEEEEE; border: 1px solid #000000; width: 20px; height: 20px; }
.cellDebug { padding: 1px; border: 1px solid #000000; width: 20px; height: 20px; }
.crossTableA { border-spacing:0px;  border-collapse: collapse; }
.cellEmptyA {  padding: 0px; }
.cellNumberA { padding: 1px; background-color: #FFFFFF; border: 0px solid #000000; width: 30px; height: 30px; }
.cellLetterA { padding: 1px; background-color: #EEEEEE; border: 1px solid #000000; width: 30px; height: 30px; }
.cellDebugA { padding: 1px; border: 1px solid #000000; width: 30px; height: 30px; }
.crossTableB { border-spacing:0px;  border-collapse: collapse; }
.cellEmptyB {  padding: 0px; }
.cellNumberB { padding: 1px; background-color: #FFFFFF; border: 0px solid #000000; width: 10px; height: 10px; }
.cellLetterB { padding: 1px; background-color: #EEEEEE; border: 1px solid #000000; width: 10px; height: 10px; }
.cellDebugB { padding: 1px; border: 1px solid #000000; width: 10px; height: 10px; }
</style>
</head>
<body>
<div align="center">

<form>
	Columns: <input type="text" name="cols" value="<?=$pc->cols?>" size="5" />
	Rows: <input type="text" name="rows" value="<?=$pc->rows?>" size="5" />
	Words: <input type="text" name="max_words" value="<?=$pc->max_words?>" size="5" />
	
	Group: 
	<select name="groupid">
	<? foreach ($pc->getGroupIDs() as $groupid): ?>
	<option value="<?=$groupid?>" <? if ($groupid == $pc->groupid) echo 'selected'; ?>>
		<?=$groupid?> [w: <?=$pc->countWordsInGroup($groupid);?>]
	</option>
	<? endforeach; ?>
	</select>
	
	Colors:
	<input type="checkbox" name="colors" value="1" <? if (!empty($_REQUEST['colors'])) echo 'checked'; ?> />

	Big blocks?:
	<input type="checkbox" name="bigblocks" value="1" <? if (!empty($_REQUEST['bigblocks'])) echo 'checked'; ?> />
	
	<input type="submit" value="Generate" />
</form>

<? if (!$success): ?>

	SORRY, UNABLE TO GENERATE DEMO CROSSWORD - TRY WITH MORE AREA OR LESS WORDS.
	
<? else: ?>

<?

$params = array(
	'colors'	=> $_REQUEST['colors'],
	'fillflag'	=> 0,
	'cellflag'	=> $_REQUEST['bigblocks'] ? 'A' : ''
	);

$html = $pc->getHTML($params);
$words = $pc->getWords();
?>

<?  print "\n\n<hr><!-- break here -->\n\n"; ?>
<p><?=$html?></p>

<!-- <p><b>Words: <?=count($words)?></b></p> -->

<table border=0 align="center" cellpadding=4>
<tr align=left>
	<th>Num.</th>
<!--	<th>Word</th> -->
	<th>Question</th>
<!--	<th>X</th>
	<th>Y</th>
	<th>Axis</th> -->
</tr>
<? foreach ($words as $key=>$word): ?>
<tr align=left>
	<td><?=$key+1?>.</td>
<!--	<td><?=$word["word"]?></td> -->
	<td><?=$word["question"]?></td>
<!--	<td><?=$word["x"]?></td>
	<td><?=$word["y"]?></td>
	<td><?=$word["axis"]?></td> -->
</tr>
<? endforeach; ?>
</table>

<? endif; ?>


<?=sprintf("<!--<p>Generated in %.4f sec.</p>-->",(getmicrotime() - $script_start))?>

<p>
<?
  $params = array(
	'colors'	=> $_REQUEST['colors'],
	'fillflag'	=> 1,
	'cellflag'	=> 'B'
	);
  
  $html = $pc->getHTML($params);
  print "\n\n<hr><!-- break here -->\n\n";
  print $html;
  print "\n\n<hr><!-- break here -->\n\n";
?>

</div>

</body>
</html>
