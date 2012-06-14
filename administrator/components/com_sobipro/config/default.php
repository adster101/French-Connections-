<?php
/**
 * @version: $Id: default.php 551 2011-01-11 14:34:26Z Radek Suski $
 * @package: SobiPro Template
 * ===================================================
 * @author
 * Name: Sigrid Suski & Radek Suski, Sigsiu.NET GmbH
 * Email: sobi[at]sigsiu.net
 * Url: http://www.Sigsiu.NET
 * ===================================================
 * @copyright Copyright (C) 2006 - 2011 Sigsiu.NET GmbH (http://www.sigsiu.net). All rights reserved.
 * @license see http://www.gnu.org/licenses/gpl.html GNU/GPL Version 3.
 * You can use, redistribute this file and/or modify it under the terms of the GNU General Public License version 3
 * ===================================================
 * $Date: 2011-01-11 15:34:26 +0100 (Tue, 11 Jan 2011) $
 * $Revision: 551 $
 * $Author: Radek Suski $
 * File location: administrator/components/com_sobipro/config/default.php $
 */
defined( 'SOBIPRO' ) || exit( 'Restricted access' );
?>
<table style="width: 100%; vertical-align: top;">
	<tr>
		<td style="vertical-align: top;"><?php $this->menu(); ?></td>
		<td style="width: 100%; vertical-align: top;"><?php $this->fields(); ?>
		</td>
	</tr>
</table>
