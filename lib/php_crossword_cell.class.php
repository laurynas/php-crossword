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
 * PHP Crossword Cell
 *
 * @package		PHP_Crossword 
 * @copyright	Laurynas Butkus, 2004
 * @license		http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version		0.2
 */
 
class PHP_Crossword_Cell 
{
    var $x;
    var $y;
    var $letter;
    var $crossed = 0;
    
    var $number; # sandy addition

    var $can_cross = array(
                     PC_AXIS_H => TRUE,
                     PC_AXIS_V => TRUE 
					 );


	/**
	 * Constructor
	 * @param int $x
	 * @param int $y
	 */
    function &PHP_Crossword_Cell($x, $y)
    {
        $this->x = (int)$x;
        $this->y = (int)$y;
    }

	/**
	 * Set letter to the cell
	 * @param char $letter
	 * @param int $axis
	 * @param object $grid
	 */
    function setLetter($letter, $axis, &$grid)
    {
        if (!$this->canSetLetter($letter, $axis))
        {
            echo "ERROR IN GRID:";
            echo $grid->getHtml();
            die("Can't place letter '{$letter}' to cell [{$this->x}x{$this->y}]");
        }

        $this->letter = $letter;
        $this->crossed++;

        $this->can_cross[$axis] = FALSE;

        $this->__updateNeighbours($axis, $grid);
    }

	/**
	 * Update neigbhour cells
	 * @private
	 * @param int $axis
	 * @param object $grid
	 */
    function __updateNeighbours($axis, &$grid)
    {
        $x = $this->x;
        $y = $this->y;

        if ($axis == PC_AXIS_H)
            $n =& $y;
        else
            $n =& $x;

        $n-= 1;

        if ($n >= 0)
            $grid->cells[$x][$y]->setCanCross($axis, FALSE);

        $n+= 2;

        if (is_object($grid->cells[$x][$y]))
            $grid->cells[$x][$y]->setCanCross($axis, FALSE);

    }

	/**
	 * Check if the cell can cross
	 * @param int $axis
	 * @return boolean
	 */
    function canCross($axis)
    {
        return $this->can_cross[$axis];
    }

	/**
	 * Set crossing possiblities
	 * @param int $axis
	 * @param boolean $can
	 */
    function setCanCross($axis, $can)
    {
        switch ($axis)
        {
            case PC_AXIS_H:
				$this->can_cross[PC_AXIS_H] = $can;
				break;

            case PC_AXIS_V:
				$this->can_cross[PC_AXIS_V] = $can;
				break;

            case PC_AXIS_BOTH:
				$this->can_cross[PC_AXIS_H] = $can;
				$this->can_cross[PC_AXIS_V] = $can;
				break;

            default:
				die("INVALID AXIS FOR setCanCross");
        }
    }

	/**
	 * Check if it's possible to set letter
	 * @param char $letter
	 * @param int $axis
	 * @return boolean
	 */
    function canSetLetter($letter, $axis)
    {
        return !(!$this->can_cross[$axis] || ($this->crossed && $this->letter != $letter));
    }

	/** 
	 * Get available axis for crossing
	 * @return int
	 */
    function getCanCrossAxis()
    {
        if ($this->canCross(PC_AXIS_H) && $this->canCross(PC_AXIS_V)) return PC_AXIS_BOTH;

        elseif ($this->canCross(PC_AXIS_H)) return PC_AXIS_H;

        elseif ($this->canCross(PC_AXIS_V)) return PC_AXIS_V;

        else return PC_AXIS_NONE;
    }

}

?>