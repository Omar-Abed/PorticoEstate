<?php
/**************************************************************************\
* phpGroupWare - Antispam                                                  *
* http://www.phpgroupware.org                                              *
* This application written by:                                             *
*                             Marco Andriolo-Stagno <stagno@prosa.it>      *
*                             PROSA <http://www.prosa.it>                  *
* -------------------------------------------------------------------------*
* Funding for this program was provided by http://www.seeweb.com           *
* -------------------------------------------------------------------------*
*  This program is free software; you can redistribute it and/or modify it *
*  under the terms of the GNU General Public License as published by the   *
*  Free Software Foundation; either version 2 of the License, or (at your  *
*  option) any later version.                                              *
\**************************************************************************/
 
 /* $Id$ */

	$phpgw_flags = array('currentapp' => 'manual');
	$phpgw_info['flags'] = $phpgw_flags;
	include('../../header.inc.php');
	$appname = 'antispam';
	include(PHPGW_SERVER_ROOT.'/'.$appname.'/setup/setup.inc.php');
?>

	<img src="<?php echo $phpgw->common->image($appname,'navbar.gif'); ?>" border="0"><p/>
	<font face="<?php echo $phpgw_info['theme']['font']; ?>" size="2">
	Version: <b><?php echo $setup_info[$appname]['version']; ?></b>
	</font>
	Module to set <a href='http://www.spamassassin.org'>SpamAssassin</a> preferences
	<?php $phpgw->common->phpgw_footer(); ?>
