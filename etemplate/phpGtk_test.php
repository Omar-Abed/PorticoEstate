#!/usr/bin/php -q

<?php
 /**********************************************************************\
 * phpGroupWare - eTemplate						*
 * http://www.phpgroupware.org						*
 * This program is part of the GNU project, see http://www.gnu.org/	*
 *									*
 * Copyright 2002, 2003 Free Software Foundation, Inc.			*
 *									*
 * Originally Written by Ralf Becker - <RalfBecker@outdoor-training.de>	*
 * --------------------------------------------				*
 * This program is Free Software; you can redistribute it and/or modify *
 * it under the terms of the GNU General Public License as published by *
 * the Free Software Foundation; either version 2 of the License, or 	*
 * at your option) any later version.					*
 \**********************************************************************/
 /* $Id$ */

//echo "Hello World!!!\n";

// To be able to test eTemplates with phpGtk you need a standalone/cgi php interpreter with compiled-in phpGtk installed
// (for instruction on how to do so look at the phpGtk website http://gtk.php.net/).

// Then start this script with the parameters <login> <passwd>

global $argv;

if ($argv[1] == '' || $argv[2] == '')
{
	echo "Usage: $argv[0] <login> <passwd>\n";
	exit;
}
$GLOBALS['phpgw_info']['flags'] = array(
	'currentapp'             => 'login',
	'noheader'               => True,
	'nonavbar'               => True
);
include('../header.inc.php');

$GLOBALS['phpgw']->session->create($argv[1],$argv[2],'text') || die("Can't create session !!!\n");

$GLOBALS['phpgw_info']['flags']['currentapp'] = 'etemplate';

ExecMethod('etemplate.db_tools.edit');

$GLOBALS['phpgw_info']['flags']['nodisplay'] = True;
exit;
