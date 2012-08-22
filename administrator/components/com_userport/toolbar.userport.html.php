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
defined( '_JEXEC' ) or die( 'Restricted access' );

class JUserportToolbar
{
  /* task => ( text, icon ) */
  protected static $_buttonInfo = array(
  		'start' => array( 'text' => 'COM_USERPORT_BUTTON_START', 'icon' => 'start' ),
  		'chooseinitialtextforexport' => array( 'text' => 'COM_USERPORT_BUTTON_EXPORT', 'icon' => 'download' ),
      'chooseinitialtextforimport' => array( 'text' => 'COM_USERPORT_BUTTON_CHOOSE_INTIAL_TEXT', 'icon' => 'upload' ),
      'export' => array( 'text' => 'COM_USERPORT_BUTTON_EXPORT', 'icon' => 'download' ),
  		'showeditwindow' => array( 'text' => 'COM_USERPORT_BUTTON_SHOW_EDIT_WINDOW', 'icon' => 'upload' ),
  		'downloadexportedusers' => array( 'text' => 'COM_USERPORT_BUTTON_DOWNLOAD', 'icon' => 'download' ),
  		'downloadimporttext' => array( 'text' => 'COM_USERPORT_BUTTON_DOWNLOAD', 'icon' => 'download' ),
      'showoptionsforadd' => array( 'text' => 'COM_USERPORT_BUTTON_ADD', 'icon' => 'user-add' ),
      'showoptionsforupdate' => array( 'text' => 'COM_USERPORT_BUTTON_UPDATE', 'icon' => 'edit' ),
      'showoptionsfordelete' => array( 'text' => 'COM_USERPORT_BUTTON_DELETE', 'icon' => 'delete' ),
  		'add' => array( 'text' => 'COM_USERPORT_BUTTON_ADD', 'icon' => 'user-add', 'next_batch_text' => 'COM_USERPORT_BUTTON_ADD_NEXT' ),
      'update' => array( 'text' => 'COM_USERPORT_BUTTON_UPDATE', 'icon' => 'edit', 'next_batch_text' => 'COM_USERPORT_BUTTON_UPDATE_NEXT' ),
      'delete' => array( 'text' => 'COM_USERPORT_BUTTON_DELETE', 'icon' => 'delete', 'next_batch_text' => 'COM_USERPORT_BUTTON_DELETE_NEXT' ) );

  protected function _AddTaskButton( $task, $useNextBatchText = false )
  {
    if ( !/*NOT*/array_key_exists( $task, self::$_buttonInfo ) )
    {
      $task = 'start';
    }
    $icon = self::$_buttonInfo[ $task ][ 'icon' ];
    if ( $useNextBatchText )
    {
      $text = JText::_( self::$_buttonInfo[ $task ][ 'next_batch_text' ] );
    }
    else
    {
      $text = JText::_( self::$_buttonInfo[ $task ][ 'text' ] );
    }
    JToolBarHelper::custom( $task, $icon, $icon, $text, false, false );
  }

  protected function _Button_UserManager()
  {
    $bar = & JToolBar::getInstance('toolbar');
    $bar->appendButton( 'Link', 'user', 'User Manager', 'index.php?option=com_users' );
  }

  protected function _Title( $title )
  {
    JToolbarHelper::title( JText::_( $title ), 'userport' );
  }

  public function delete()
  {
    self::_Title( 'COM_USERPORT_TITLE_DELETE' );
    if ( isset( $GLOBALS[ 'userport_next_batch_needs_processing' ] ) )
    {
      self::_AddTaskButton( 'delete', true );
    }
    JToolBarHelper::divider();
    JToolBarHelper::back();
    self::_Button_UserManager();
    self::_AddTaskButton( 'start' );
  }

  public function update()
  {
    self::_Title( 'COM_USERPORT_TITLE_UPDATE' );
    if ( isset( $GLOBALS[ 'userport_next_batch_needs_processing' ] ) )
    {
      self::_AddTaskButton( 'update', true );
    }
    JToolBarHelper::divider();
    JToolBarHelper::back();
    self::_Button_UserManager();
    self::_AddTaskButton( 'start' );
  }

  public function add()
  {
    self::_Title( 'COM_USERPORT_TITLE_ADD' );
    if ( isset( $GLOBALS[ 'userport_next_batch_needs_processing' ] ) )
    {
      self::_AddTaskButton( 'add', true );
    }
    JToolBarHelper::divider();
    JToolBarHelper::back();
    self::_Button_UserManager();
    self::_AddTaskButton( 'start' );
  }

  public function showoptionsfordelete()
  {
    self::_Title( 'COM_USERPORT_TITLE_OPTIONS_FOR_DELETE' );
    self::_AddTaskButton( 'delete' );
    JToolBarHelper::divider();
    JToolBarHelper::back();
    self::_AddTaskButton( 'start' );
  }

  public function showoptionsforupdate()
  {
    self::_Title( 'COM_USERPORT_TITLE_OPTIONS_FOR_UPDATE' );
    self::_AddTaskButton( 'update' );
    JToolBarHelper::divider();
    JToolBarHelper::back();
    self::_AddTaskButton( 'start' );
  }

  public function showoptionsforadd()
  {
    self::_Title( 'COM_USERPORT_TITLE_OPTIONS_FOR_ADD' );
    self::_AddTaskButton( 'add' );
    JToolBarHelper::divider();
    JToolBarHelper::back();
    self::_AddTaskButton( 'start' );
  }

  public function showeditwindow()
  {
    self::_Title( 'COM_USERPORT_TITLE_SHOW_EDIT_WINDOW' );
    self::_AddTaskButton( 'downloadimporttext' );
    JToolBarHelper::divider();
    self::_AddTaskButton( 'showoptionsforadd' );
    self::_AddTaskButton( 'showoptionsforupdate' );
    self::_AddTaskButton( 'showoptionsfordelete' );
    JToolBarHelper::divider();
    JToolBarHelper::back();
    self::_AddTaskButton( 'start' );
  }

  public function export()
  {
    self::_Title( 'COM_USERPORT_TITLE_EXPORT' );
    self::_AddTaskButton( 'downloadexportedusers' );
    JToolBarHelper::divider();
    JToolBarHelper::back();
    self::_Button_UserManager();
    self::_AddTaskButton( 'start' );
  }

  public function chooseinitialtextforimport()
  {
    self::_Title( 'COM_USERPORT_TITLE_CHOOSE_INITIAL_TEXT' );
    self::_AddTaskButton( 'showeditwindow' );
    JToolBarHelper::divider();
    JToolBarHelper::back();
  }

  public static function chooseinitialtextforexport()
  {
    self::_Title( 'COM_USERPORT_TITLE_OPTIONS_FOR_EXPORT' );
    self::_AddTaskButton( 'export' );
    JToolBarHelper::divider();
    JToolBarHelper::back();
  }

  public function start()
  {
    self::_Title( 'COM_USERPORT_TITLE_START' );
    self::_AddTaskButton( 'chooseinitialtextforimport' );
    self::_AddTaskButton( 'chooseinitialtextforexport' );
    JToolBarHelper::divider();
    self::_Button_UserManager();
//     JToolBarHelper::preferences( 'com_userport', 400, 575 );
  }
}

?>
