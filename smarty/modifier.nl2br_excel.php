<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     modifier.nl2br_excel
 * Purpose:  converts newlines into an Excel-friendly newline <br /> containing
 *           Microsoft proprietary, inline styles.
 * -------------------------------------------------------------
 */
function smarty_modifier_nl2br_excel($string)
{
	return preg_replace("/(\n\r|\n)/", "<br style=\"mso-data-placement:same-cell;\" />", $string);
}

