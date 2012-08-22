<?php
/*
 * @package userport
 \* @copyright 2008-2012 Parvus
 * @license http://www.gnu.org/licenses/gpl-3.0.html
 * @link http://joomlacode.org/gf/project/userport/
 * @author Parvus
 *
 * userport is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free
 * Software Foundation, either version 3 of the License, or (at your option)
 * any later version.
 *
 * userport is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with userport. If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');
require_once( JApplicationHelper::getPath( 'toolbar_html' ) );

if ( method_exists( 'JUserportToolbar', $task ) )
{
  call_user_func( array( 'JUserportToolbar', $task ) );
}
else
{
	JUserportToolbar::start();
}

?>
