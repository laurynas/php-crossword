<?
function GetXMLChildren($vals, &$i)
{
	$children = array();
	if ($vals[$i]['value'])	array_push($children, $vals[$i]['value']);
	
	while (++$i < count($vals)) // so pra nao botar while true ;-)
	{
		switch ($vals[$i]['type'])
		{
			case 'cdata':
			array_push($children, $vals[$i]['value']);
			break;
			
			case 'complete':
			array_push($children, array('tag' => $vals[$i]['tag'], 'value' => $vals[$i]['value'], 'attributes' => $vals[$i]['attributes']));
			break;
			
			case 'open':
			array_push($children, array('tag' => $vals[$i]['tag'], 'value' => $vals[$i]['value'], 'attributes' => $vals[$i]['attributes'],
			'children' => GetXMLChildren($vals, $i)));
			break;
			
			case 'close':
			return $children;
		}
	}
}

function GetXMLTreeFromString($data)
{
	$p = xml_parser_create();
	xml_parser_set_option($p, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($p, $data, $vals, $index);
	xml_parser_free($p);
	// dump($vals);
	
	$tree = array();
	$i = 0;
	array_push($tree, array('tag' => $vals[$i]['tag'], 'attributes' => $vals[$i]['attributes'],
	'value' => $vals[$i]['value'],
	'children' => GetXMLChildren($vals, $i)));
	return $tree;    
}

function GetXMLTree($file)
{
	$data = implode('', file($file));
	return GetXMLTreeFromString($data);
}
?>