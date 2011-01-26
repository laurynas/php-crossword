<?php
// ----------------------------------------------------------------------------
// This file is part of PHP Crossword.
//
// PHP Crossword is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
// 
// PHP Crossword is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with Foobar; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
// ----------------------------------------------------------------------------

/**
 * PHP Crossword Client
 *
 * @package		PHP_Crossword 
 * @copyright	Laurynas Butkus, 2004
 * @license		http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version		0.2
 */

require_once dirname(__FILE__) . '/xml.php';

define("PC_AXIS_H", 1 );
define("PC_AXIS_V", 2 );

class PHPCrosswordClient
{
	var $url;
	var $cols = 15;
	var $rows = 15;
	var $words = 15;
	var $max_tries = 10;
	var $groupid = 'en';
	
	var $html;
	var $items = array();
	var $_map = array();
	var $_letters = array();
	var $_nr = 0;
	
	var $_generator_access_method = 'http';
	
	var $_error;

	/**
	 * Constructor
	 * @param string PHP Crossword server URL
	 */
	function PHPCrosswordClient($server_url)
	{
		$this->url = $server_url;
	}

	/**
	 * Set words group ID
	 * @param string $groupid
	 */
	function setGroupID($groupid)
	{
		$this->groupid = $groupid;
	}
	
	/**
	 * Set columns number
	 * @param int $cols
	 */
	function setCols($cols)
	{
		$this->cols = (int)$cols;
	}

	/**
	 * Get columns number
	 * @return int 
	 */
	function getCols()
	{
		return $this->cols;
	}
	
	/**
	 * Get error message
	 * @return string
	 */
	function getError()
	{
		return $this->_error;
	}

	/**
	 * Set rows number
	 * @param int $rows
	 */
	function setRows($rows)
	{
		$this->rows = (int)$rows;
	}

	/**
	 * Get rows number
	 * @return int 
	 */
	function getRows()
	{
		return $this->rows;
	}

	/**
	 * Set words number
	 * @param int $words
	 */
	function setWords($words)
	{
		$this->words = (int)$words;
	}

	/**
	 * Get words number
	 * @return int 
	 */
	function getWords()
	{
		return $this->words;
	}

	/**
	 * Set maximum number of tries to pick the word (for the generator)
	 * @param int $max_tries
	 */
	function setMaxTries($max_tries)
	{
		$this->max_tries = $max_tries;
	}
	
	/**
	 * Set generator access method (http/direct)
	 * @param string $type
	 */
	function setGeneratorAccessMethod($method)
	{
		$this->_generator_access_method = $method;
	}

	/**
	 * Try to generate crossword until we get items
	 * @return boolean TRUE - if success
	 */
	function generate()
	{
		do
		{
			$this->__generate();
			
			if (!empty($this->_error)) 
				return FALSE;
		}
		while (!count($this->items));
		
		return TRUE;
	}

	/**
	 * Call the server
	 * @private
	 * @return boolean TRUE - if success
	 */
	function __generate()
	{
		$xml = $this->__getXML();
		
		$tree = GetXMLTreeFromString($xml);
			
		unset($snoopy);
		
		if (empty($tree[0]['tag']))
		{
			$this->_error = 'ERROR: INVALID XML RESPONSE';
			return FALSE;
		}
		
		if ($tree[0]['tag'] != 'CROSSWORD')
		{
			$this->_error = 'ERROR: ' . $tree[0]['value'];
			return FALSE;
		}
		
		return $this->__load($tree[0]['children']);
	}
	
	/**
	 * Get crossword XML
	 * @private
	 * @return string XML
	 */
	function __getXML()
	{
		switch ($this->_generator_access_method)
		{
			case 'http': 	return $this->__getXML_HTTP();
			case 'direct': 	return $this->__getXML_Direct();
			default: 
				die("ERROR: invalid generator access method '{$this->_generator_access_method}'");
		}
	}
	
	/**
	 * Fetch XML using HTTP protocol
	 * @private
	 * @return string XML
	 */
	function __getXML_HTTP()
	{
		$url = $this->url . '?cols=' . $this->cols;
		$url.= '&rows=' . $this->rows;
		$url.= '&max_words=' . $this->words;
		$url.= '&max_tries=' . $this->max_tries;
		$url.= '&groupid=' . $this->groupid;

		require_once dirname(__FILE__) . '/Snoopy.class.php';

		$snoopy = new Snoopy;
		$snoopy->fetch($url);
		
		return $snoopy->results;
	}
	
	/**
	 * Call generator directly
	 * @private
	 * @return string XML
	 */
	function __getXML_Direct()
	{
		require_once 'config.php';
		require_once 'lib/mysql.class.php';
		require_once 'lib/php_crossword.class.php';
		
		$pc =& new PHP_Crossword($this->cols, $this->rows);
		
		$pc->setGroupID($this->groupid);
		$pc->setMaxWords($this->words);
		$pc->setMaxFullTries($this->max_tries);
		
		$pc->generate();
		
		$xml = $pc->getXML();
		
		unset($pc);
		
		return $xml;
	}

	/**
	 * Load generator result
	 * @private
	 * @param array $tree
	 * @return boolean TRUE - if success
	 */
	function __load(&$tree)
	{
		$this->items = array();
		$this->_map = array();
		$this->_letters = array();
		$this->_nr = 0;

		foreach ((array)$tree as $item)
		{
			switch ($item['tag'])
			{
				case 'ITEMS':
                    $this->__loadItems($item['children']);
					break;
					
				case 'HTML':
					$this->html = $item['value'];
					break;
			}
		}
		
		return TRUE;
	}

	/**
	 * Load generator result items
	 * @private
	 * @param array $tree
	 */
    function __loadItems(&$tree)
    {
		foreach ((array)$tree as $item)
		{
			switch ($item['tag'])
			{
				case 'ITEM':
                    $this->__loadItem($item['children']);
					break;
			}
		}
	}

	/**
	 * Load generator result item
	 * @private
	 * @param array $tree
	 */
    function __loadItem(&$tree)
    {
    	$item = array();

		foreach ((array)$tree as $tmp)
			$item[$tmp['tag']] = $tmp['value'];

		$x = $item['X'];
        $y = $item['Y'];

		if (!isset($this->_map[$x][$y]))
			$this->_map[$x][$y] = ++$this->_nr;

		$item['NR'] =& $this->_map[$x][$y];

		$tx = $x;
		$ty = $y;

		if ($item['AXIS'] == PC_AXIS_H) {
			$t =& $tx;
		} else {
			$t =& $ty;
		}

        for ($i=0; $i<strlen($item['WORD']); $i++)
        {
        	$this->_letters[$tx][$ty] = $item['WORD'][$i];
        	$t++;
		}

		$this->items[] = $item;
	}

	/**
	 * Get letter 
	 * @param int $x
	 * @param int $y
	 * @return char|NULL
	 */
	function getLetter($x, $y)
	{
		return $this->_letters[$x][$y];
	}

	/**
	 * Get question number
	 * @param int $x
	 * @param int $y
	 * @return int|NULL
	 */
	function getQuestionNr($x, $y)
	{
		if (isset($this->_map[$x][$y]))
			return $this->_map[$x][$y];
		else
			return NULL;
	}

	/**
	 * Get horizontal questions
	 * @return array
	 */
	function getQuestionsH()
	{
        return $this->getQuestions(PC_AXIS_H);
	}

	/**
	 * Get vertical questions
	 * @return array
	 */
   	function getQuestionsV()
	{
        return $this->getQuestions(PC_AXIS_V);
	}

	/**
	 * Get questions
	 * @param int $axis
	 * @return array
	 */
	function getQuestions($axis)
	{
		$items = array();

		for ($i=0; $i<count($this->items); $i++)
			if ($this->items[$i]['AXIS'] == $axis)
				$items[] = $this->items[$i];

		return $items;
	}

}
?>