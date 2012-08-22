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

jimport('joomla.form.form');

$lang =& JFactory::getLanguage();
$lang->load( 'com_users', JPATH_ADMINISTRATOR );

$css = JURI::base().'components/com_userport/userport.css';
$document = &JFactory::getDocument();
$document->addStyleSheet( $css, 'text/css', null, array() );


class JUserportHtml
{
  function ShowExportedUsers( $task, & $userList )
  {
    JUserportHtml::ShowTextarea( $task, JText::_( 'COM_USERPORT_LABEL_EXPORTED_USERS' ), $userList );
  }

  function ShowTextarea( $task, $title, & $userList )
  {
    $form = &JForm::getInstance( 'ShowTextarea', JPATH_COMPONENT_ADMINISTRATOR . '/forms/userList.xml' );
    $formName = 'userList';
    ?>
    <form action="index.php" method="post" name="adminForm">
    <?php
      if ( JString::strpos( $task, 'export' ) === FALSE )
      {
        echo $form->getLabel( 'help_edit_5', $formName );
      }
    ?>
      <fieldset class="adminform">
        <legend><?php echo $title; ?></legend>

        <?php
				  $form->setValue( 'user_list', $formName, $userList );

          if ( JString::strpos( $task, 'export' ) === FALSE )
          {
            $helpEdit = $form->getFieldAttribute( 'help_edit_2', 'label', '', $formName );
            $helpEdit = JText::sprintf( $helpEdit, implode( ', ', JUserportHelper::Fields( 'import' ) ) );
            $form->setFieldAttribute( 'help_edit_2', 'label', $helpEdit, $formName );

            $groups = JUserportHelper::UserGroups();
            $helpEdit = $form->getFieldAttribute( 'help_edit_3', 'label', '', $formName );
            $helpEdit = JText::sprintf( $helpEdit, implode( ', ', array_keys( $groups[ 'front' ] ) ) );
            $form->setFieldAttribute( 'help_edit_3', 'label', $helpEdit, $formName );

            echo "<ul>";
            echo $form->getLabel( 'help_edit_1', $formName );
            //echo $form->getLabel( 'import_user_list', $formName );
            echo $form->getInput( 'user_list', $formName );
            echo $form->getLabel( 'help_edit_2', $formName );
            echo $form->getLabel( 'help_edit_3', $formName );
            echo $form->getLabel( 'help_edit_4', $formName );
            echo "</ul>";
          }
          else
          {
            echo $form->getLabel( 'export_user_list', $formName );
            echo $form->getInput( 'user_list', $formName );
          }
        ?>

        <input type="hidden" name="form" value="<?php echo $formName; ?>" />
        <input type="hidden" name="option" value="<?php echo JRequest::getWord( 'option' ); ?>" />
        <input type="hidden" name="task" value="" />
      </fieldset>
  	</form>
    <?php
  }

  function ShowEndResult( $task, $settings )
  {
    self::DummyForm( $task, $settings );
  }

  function DummyForm( $task, $settings = null )
  {
    /* If no form with the name adminForm is present, the toolbarbuttons do
     * not work - except for the back button, which is nothing more than
     * javascript:history.back();
     */
    /* All settings - except instances - are stored here, so that when a
     * next batch is requested, they are available again.
     */
    $formName = 'userport';

    $array = array();
    if ( $settings )
    {
      foreach( $settings as $key => $value )
      {
        if ( is_bool( $value ) or is_int( $value ) or is_string( $value ) or is_array( $value ) )
        {
          $array[ $key ] = $value;
        }
      }
    }
    $json = urlencode( json_encode( $array ) );

    ?>
    <form action="index.php" method="post" name="adminForm">
      <fieldset class="adminform">

        <input type="hidden" name="userport_settings" value="<?php echo $json; ?>" />
        <input type="hidden" name="form" value="<?php echo $formName; ?>" />
        <input type="hidden" name="option" value="<?php echo JRequest::getWord( 'option' ); ?>" />
        <input type="hidden" name="task" value="" />

      </fieldset>
    </form>
    <?php
  }

  function ChooseInitialText( $task, $title )
  {
    $form = &JForm::getInstance( 'ChooseInitialText', JPATH_COMPONENT_ADMINISTRATOR . '/forms/initialText.xml' );
    $formName = 'initialText';

    ?>
    	<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data">
        <fieldset class="adminform">
          <legend><?php echo $title; ?></legend>

          <?php
            if ( JString::strpos( $task, 'export' ) === FALSE )
            {
						  echo '<fieldset>';
              echo $form->getLabel( 'initial_text', $formName );
              echo $form->getInput( 'initial_text', $formName );
              echo '</fieldset>';
            }
            echo '<fieldset>';
            if ( JString::strpos( $task, 'export' ) === FALSE )
            {
              echo '<legend>';
              echo JText::_( 'COM_USERPORT_LABEL_SPECIFIC_FOR_A_AND_C' );
              echo '</legend>';
            }
            echo $form->getLabel( 'which_fields', $formName );
            echo $form->getInput( 'which_fields', $formName );
            echo $form->getLabel( 'field_separator', $formName );
            echo $form->getInput( 'field_separator', $formName );
            echo $form->getLabel( 'field_enclosure', $formName );
            echo $form->getInput( 'field_enclosure', $formName );
            echo '</fieldset>';

            if ( JString::strpos( $task, 'export' ) === FALSE )
            {
              echo '<fieldset>';
              echo '<legend>';
              echo JText::_( 'COM_USERPORT_LABEL_SPECIFIC_FOR_B' );
              echo '</legend>';
              echo $form->getLabel( 'file', $formName );
              echo $form->getInput( 'file', $formName );
            	echo '</fieldset>';
            }

            echo '<fieldset>';
            if ( JString::strpos( $task, 'export' ) === FALSE )
            {
              echo '<legend>';
              echo JText::_( 'COM_USERPORT_LABEL_SPECIFIC_FOR_C' );
          	  echo '</legend>';
            }
            echo $form->getLabel( 'block', $formName );
            echo $form->getInput( 'block', $formName );
            echo $form->getLabel( 'filter_combination', $formName );
            echo $form->getInput( 'filter_combination', $formName );
            echo $form->getLabel( 'non_activated', $formName );
            echo $form->getInput( 'non_activated', $formName );
            echo $form->getLabel( 'groups_override_value', $formName );
            echo $form->getInput( 'groups_override_value', $formName );
          	echo '</fieldset>';
 					?>

          <input type="hidden" name="form" value="<?php echo $formName; ?>" />
          <input type="hidden" name="option" value="<?php echo JRequest::getWord( 'option' ); ?>" />
      		<input type="hidden" name="task" value="" />
        </fieldset>
      </form>
    <?php
  }

  /**
   * Shows a form in which the user can select further options that affect
   * the pending task.
   * @param string $task The pending task.
   * @param string $title The title to use for the form.
   * @param dict $settings A named array, containing the useful settings;
   * including the contents of the csv editor on which the task
   * must be applied.
   * @return void
   */
  function ShowOptions( $task, $title, $settings, $hidden = array() )
  {
    $form = &JForm::getInstance( $task, JPATH_COMPONENT_ADMINISTRATOR . '/forms/changeOptions.xml' );
    $formName = 'changeOptions';

    ?>
    <form action="index.php" method="post" name="adminForm">
      <fieldset class="adminform">
        <legend><?php echo $title; ?></legend>

        <?php
        echo '<fieldset>';
        echo '<legend>';
        echo JText::_( 'COM_USERPORT_LABEL_GENERAL_OPTIONS_DRY_RUN' );
        echo '</legend>';
        echo $form->getLabel( 'help_dry_run', $formName );
        echo $form->getLabel( 'dry_run', $formName );
        echo $form->getInput( 'dry_run', $formName );
        echo $form->getLabel( 'dry_run_email', $formName );
        echo $form->getInput( 'dry_run_email', $formName );
        echo '</fieldset>';

        echo '<div style="color: rgb(204, 0, 0);background-color: rgb(239, 231, 184);">' . JText::_( 'COM_USERPORT_WARNING_USER_PLUGINS' ) . '</div>';

        switch ($task)
        {
          case 'showoptionsforadd':
            echo '<fieldset>';
            echo '<legend>';
            echo JText::_( 'COM_USERPORT_LABEL_SPECIFIC_ADD_OPTIONS' );
            echo '</legend>';
            echo $form->getLabel( 'passwords_are_encrypted', $formName );
            echo $form->getInput( 'passwords_are_encrypted', $formName );
            echo $form->getLabel( 'add_password_handling_origin', $formName );
            echo $form->getInput( 'add_password_handling_origin', $formName );
            echo $form->getLabel( 'password_override_value', $formName );
            echo $form->getInput( 'password_override_value', $formName );
            echo "<div class='clr'></div><hr/>";
            echo $form->getLabel( 'add_groups_handling_origin', $formName );
            echo $form->getInput( 'add_groups_handling_origin', $formName );
            echo $form->getLabel( 'groups_override_value', $formName );
            echo $form->getInput( 'groups_override_value', $formName );
            echo "<div class='clr'></div><hr/>";
            echo $form->getLabel( 'add_block_activate_handling_origin', $formName );
            echo $form->getInput( 'add_block_activate_handling_origin', $formName );
            echo $form->getLabel( 'block_activate_override_value', $formName );
            echo $form->getInput( 'block_activate_override_value', $formName );
            echo "<div class='clr'></div><hr/>";
            echo $form->getLabel( 'add_params_handling_origin', $formName );
            echo $form->getInput( 'add_params_handling_origin', $formName );
            echo $form->getLabel( 'params_override_value', $formName );
            echo $form->getInput( 'params_override_value', $formName );
            echo '</fieldset>';
            break;

          case 'showoptionsforupdate':
            echo '<fieldset>';
            echo '<legend>';
            echo JText::_( 'COM_USERPORT_LABEL_SPECIFIC_UPDATE_OPTIONS' );
            echo '</legend>';
            echo $form->getLabel( 'passwords_are_encrypted', $formName );
            echo $form->getInput( 'passwords_are_encrypted', $formName );
            echo $form->getLabel( 'update_password_handling', $formName );
            echo $form->getInput( 'update_password_handling', $formName );
            echo $form->getLabel( 'update_password_handling_origin', $formName );
            echo $form->getInput( 'update_password_handling_origin', $formName );
            echo $form->getLabel( 'password_override_value', $formName );
            echo $form->getInput( 'password_override_value', $formName );
            echo "<div class='clr'></div><hr/>";
            echo $form->getLabel( 'update_groups_handling', $formName );
            echo $form->getInput( 'update_groups_handling', $formName );
            echo $form->getLabel( 'update_groups_handling_origin', $formName );
            echo $form->getInput( 'update_groups_handling_origin', $formName );
            echo $form->getLabel( 'groups_override_value', $formName );
            echo $form->getInput( 'groups_override_value', $formName );
            echo "<div class='clr'></div><hr/>";
            echo $form->getLabel( 'update_block_activate_handling', $formName );
            echo $form->getInput( 'update_block_activate_handling', $formName );
            echo $form->getLabel( 'update_block_activate_handling_origin', $formName );
            echo $form->getInput( 'update_block_activate_handling_origin', $formName );
            echo $form->getLabel( 'block_activate_override_value', $formName );
            echo $form->getInput( 'block_activate_override_value', $formName );
            echo "<div class='clr'></div><hr/>";
            echo $form->getLabel( 'update_params_handling', $formName );
            echo $form->getInput( 'update_params_handling', $formName );
            echo $form->getLabel( 'update_params_handling_origin', $formName );
            echo $form->getInput( 'update_params_handling_origin', $formName );
            echo $form->getLabel( 'params_override_value', $formName );
            echo $form->getInput( 'params_override_value', $formName );
            echo '</fieldset>';
            break;

          case 'showoptionsfordelete':
          default:
            /* void */
            break;
        }

        echo '<fieldset>';
        echo '<legend>';
        echo JText::_( 'COM_USERPORT_LABEL_GENERAL_OPTIONS_NOTIFY_USER' );
        echo '</legend>';
        echo $form->getLabel( 'notify_user', $formName );
        echo $form->getInput( 'notify_user', $formName );
        echo $form->getLabel( 'help_email_1', $formName );
        echo $form->getLabel( 'help_email_2', $formName );
        echo $form->getLabel( 'help_email_4', $formName );
        echo '</fieldset>';

        $app = JFactory::getApplication();
        $config =& JFactory::getConfig();
        $activationUrl = JURI::root() . "index.php?option=com_users&task=registration.activate&token=";
        $groups = JUserportHelper::UserGroups();

        echo '<fieldset>';
        echo '<legend>';
        echo JText::_( 'COM_USERPORT_LABEL_GENERAL_OPTIONS_EMAIL' );
        echo '</legend>';
        echo $form->getLabel( 'help_email_5', $formName );
        echo $form->getLabel( 'email_subject', $formName );
        echo $form->getInput( 'email_subject', $formName );
        echo $form->getLabel( 'email_body', $formName );
        echo $form->getInput( 'email_body', $formName );

        $helpEmail6 = $form->getFieldAttribute( 'help_email_6', 'label', '', $formName );
        $helpEmail6 = JText::sprintf( $helpEmail6,
            implode( ', ', array_keys( $groups[ 'front' ] ) ),
            JUri::base() . 'index.php?option=com_users&task=registration.activate&token=',
            $app->getCfg( 'sitename' ),
            JURI::root(),
            $config->getValue( 'config.mailfrom' ),
            $config->getValue( 'config.fromname' ),
            $activationUrl );
        $form->setFieldAttribute( 'help_email_6', 'label', $helpEmail6, $formName );

        echo $form->getLabel( 'help_email_6', $formName );
        echo '</fieldset>';
        ?>

        <?php if ( $settings ): ?>
          <?php foreach( $hidden as $key ): ?>
            <input type="hidden" name="<?php echo $key; ?>" value="<?php echo urlencode( $settings[ $key ] ); ?>" />
          <?php endforeach; ?>
        <?php endif; ?>
        <input type="hidden" name="form" value="<?php echo $formName; ?>" />
        <input type="hidden" name="option" value="<?php echo JRequest::getWord( 'option' ); ?>" />
        <input type="hidden" name="task" value="" />
      </fieldset>
    </form>
    <?php
  }

  /**
   * Prints out all the informational messages that have been gathered thus far.
   * The output is presented as a list, with formatting to easily
   * distinguish the different types of messages.
   * Also, a summary is printed out, formatted by Joomla's Message Application
   * Queue.
   * It can be that this function does not print anything at all; or only some
   * application messages; or only a list. This depends on the
   * availability of the different accepted types. Note that this function will
   * never print a form.
   * @param $title string The title used just above the printout of all
   * log items.
   * @param $log array of arrays. Each unnamed index is an array with two or
   * three unnamed indices: the first (index 0) leads to a string indicating
   * the main type, the second leads to a string with the actual message,
   * the third may be an array with one or more unnames indices: the
   * first indicating the sub type, the second indicating an ectual message.
   * The type must be one of: info, warning, error, line_success, line_ignored,
   * line_error. The former three are only printed out in Joomla's Message
   * Application Queue. The latter three are printed out as a formatted
   * list, and a summary in Joomla's Message Application Queue.
   * Other types are ignored and not printed out.
   * A third index is only looked at when one of the latter three main types
   * are used: line_success, line_ignored, line_error
   * The subtype must be one of: line_info, line_warning, line_error. They are
   * printed out as a formatted sublist of the main message.
   * @return void
   */
  function PrintLog( $title, & $log )
  {
    $appMessage = array
    ( 'info' => array(),
      'warning' => array(),
      'error' => array(),
      'line_success' => array( 'count' => 0 ),
      'line_warning' => array( 'count' => 0 ),
      'line_ignored' => array( 'count' => 0 ),
      'line_error' => array( 'count' => 0 ));
    $mainTypes = array
    ( 'info' => array( 'font' => "<font color='gray'>" ),
      'warning' => array( 'font' => "<font color='black'>" ),
      'error' => array( 'font' => "<font color='red' size='+1'>" ),
      'line_success' => array( 'font' => "<font color='green'>" ),
      'line_warning' => array( 'font' => "<font color='brown'>"),
      'line_ignored' => array( 'font' => "<font color='black'>" ),
      'line_error' => array( 'font' => "<font color='red' size='+1'>") );
    $subTypes = array
    ( 'field_info' => array( 'font' => "<font>", 'string' => 'COM_USERPORT_INFO' ),
      'field_warning' => array( 'font' => "<font color='orange'>", 'string' => 'COM_USERPORT_WARNING' ),
      'field_error' => array( 'font' => "<font color='red'>", 'string' => 'COM_USERPORT_ERROR' ) );
    $printLogString = false;

    $logString = "";
    $logString .= "<ul>";
    foreach ( $log as $logItem )
    {
      if ( in_array( $logItem[0], array( 'info', 'warning', 'error' ) ) )
      {
        $appMessage[ $logItem[0] ][] = $logItem[1];
      }
      else if ( in_array( $logItem[0], array( 'line_success', 'line_warning', 'line_ignored', 'line_error' ) ) )
      {
        $appMessage[ $logItem[0] ][ 'count' ]++;
        $logString .= "<li>";
        $logString .= $mainTypes[ $logItem[0] ]['font'] . $logItem[1] . "</font>";
        if ( count( $logItem ) > 2 )
        {
          foreach ( $logItem[2] as $subItem)
          {
            $subType = $subItem[0];
            $subMessage = $subItem[1];
            if ( !/*NOT*/key_exists( $subType, $subTypes) )
            {
              $subType = 'field_warning';
            }
            $logString .= "<br/>&nbsp;" . $subTypes[ $subType ][ 'font' ] . JText::_( $subTypes[ $subType ][ 'string' ] ) . "</font> " . $subMessage;
          }
        }
        $logString .= "</li>";
        $printLogString = true;
      }
      else if ( $logItem[0] == 'extra' )
      {
        $maintype = 'line_error';
        $string = "<li>" . $mainTypes[ $maintype ]['font'] . $logItem[1] . "</font></li>";
        $logString = $string . $logString . $string;
        $printLogString = true;
      }
    }
    $logString .= "</ul>";

    JUserportHtml::_PrintApplicationMessages( $appMessage );

    if ( $printLogString == true )
    {
      ?>
      <fieldset class="adminForm">
        <legend><?php echo $title; ?></legend>
        <p><?php echo $logString; ?></p>
      </fieldset>
      <?php
    }
  }

  function PrintHelp()
  {
    $siteUrl = 'http://joomlacode.org/gf/project/userport/';
    $bugUrl = $siteUrl . 'tracker/?action=TrackerItemBrowse&tracker_id=7775';
    $featureUrl = $siteUrl . 'tracker/?action=TrackerItemBrowse&tracker_id=7774';
    $donateUrl = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=HJ3JXETP36JUC';

    $userPlugins = array (
        'K2 user sync for Add user frontend' => 'http://extensions.joomla.org/extensions/extension-specific/k2-extensions/16699',
        'Community Builder Plugin for Interspire Email Marketing Software' => 'http://extensions.joomla.org/extensions/extension-specific/community-builder-extensions/community-builder-ads/7493',
        'Auto Assign ACL Group for Community Builder' => 'http://extensions.joomla.org/extensions/extension-specific/community-builder-extensions/community-builder-profiles/18926',
        'Frontend-User-Access (pro)' => 'http://extensions.joomla.org/extensions/access-a-security/frontend-access-control/6874',
        'Multilingual registration approval' => 'http://extensions.joomla.org/extensions/access-a-security/site-access/authentication-management/9007',
        'Email Activation' => 'http://extensions.joomla.org/extensions/access-a-security/authentication/18139',
        'WHM (cPanel) Email Account User Plugin' => 'http://extensions.joomla.org/extensions/clients-a-communities/user-management/18227' );
    $userPluginsStrings = array();
    foreach ( $userPlugins as $key => $value )
    {
      $userPluginsStrings[] = '<a href="' . $value . '">' . $key . '</a>';
    }
    $userPluginsString = implode( ', ', $userPluginsStrings );
    $jed = '<a href="http://extensions.joomla.org/">JED</a>';

    echo JText::_( 'COM_USERPORT_HELP_ABOUT_1' );
    echo JText::_( 'COM_USERPORT_HELP_ABOUT_2' );
    echo JText::sprintf( 'COM_USERPORT_HELP_ABOUT_3', $userPluginsString, $jed );
    echo JText::sprintf( 'COM_USERPORT_HELP_ABOUT_4', $bugUrl, $siteUrl );
    echo JText::_( 'COM_USERPORT_HELP_TASK_OVERVIEW_BEGIN' );
    echo JText::sprintf( 'COM_USERPORT_HELP_TASK_OVERVIEW_VIEW', JText::_( 'COM_USERPORT_BUTTON_EXPORT') );
    echo JText::sprintf( 'COM_USERPORT_HELP_TASK_OVERVIEW_ADD', JText::_( 'COM_USERPORT_BUTTON_CHOOSE_INTIAL_TEXT' ), JText::_( 'COM_USERPORT_BUTTON_SHOW_EDIT_WINDOW' ), JText::_( 'COM_USERPORT_BUTTON_ADD' ), JText::_( 'COM_USERPORT_BUTTON_ADD' ) );
    echo JText::sprintf( 'COM_USERPORT_HELP_TASK_OVERVIEW_UPDATE', JText::_( 'COM_USERPORT_BUTTON_CHOOSE_INTIAL_TEXT' ), JText::_( 'COM_USERPORT_BUTTON_SHOW_EDIT_WINDOW' ), JText::_( 'COM_USERPORT_BUTTON_UPDATE' ), JText::_( 'COM_USERPORT_BUTTON_UPDATE' ) );
    echo JText::sprintf( 'COM_USERPORT_HELP_TASK_OVERVIEW_DELETE', JText::_( 'COM_USERPORT_BUTTON_CHOOSE_INTIAL_TEXT' ), JText::_( 'COM_USERPORT_BUTTON_DELETE' ) );
    echo JText::_( 'COM_USERPORT_HELP_TASK_OVERVIEW_END' );
    echo JText::sprintf( 'COM_USERPORT_HELP_CLOSURE', $bugUrl, $featureUrl, $donateUrl );
  }

  /**
   * Prints out application messages which appear on top of the page, rendered
   * by Joomla's framework.
   * @param $appMessage array of mixed type.
   * Acceptable indices are info, warning, error, line_success, line_ignored
   * and line_error.
   * Each element is
   * - either an array with one enforced index: count which must be an integer
   *   (line_success, line_ignored, line_error),
   * - either an unnamed array of strings
   *   (info, warning, error).
   * @return void
   * @access private
   */
  function _PrintApplicationMessages( & $appMessage )
  {
    $noticeFormat = array
    ( 'info' => array( 'type' => 'message' ),
      'warning' => array( 'type' => 'notice' ),
      'error' => array( 'type' => 'error' ) );

    $statFormat = array
    ( 'line_success' => array
      ( 1 => array
        ( 'text' => JText::_( 'COM_USERPORT_LOG_1_ACCEPTED_LINE' ),
          'type' => 'message' ),
        'N' => array
        ( 'text' => JText::sprintf( 'COM_USERPORT_LOG_N_ACCEPTED_LINES', $appMessage[ 'line_success' ][ 'count' ] ),
          'type' => 'message' ) ),
      'line_ignored' => array
      ( 1 => array
        ( 'text' => JText::_( 'COM_USERPORT_LOG_1_IGNORED_LINE' ),
          'type' => 'notice' ),
        'N' => array
        ( 'text' => JText::sprintf( 'COM_USERPORT_LOG_N_IGNORED_LINES', $appMessage[ 'line_ignored' ][ 'count' ] ),
          'type' => 'notice' ) ),
      'line_error' => array
      ( 1 => array
        ( 'text' => JText::_( 'COM_USERPORT_LOG_1_ERROR_LINE' ),
          'type' => 'error' ),
        'N' => array
        ( 'text' => JText::sprintf( 'COM_USERPORT_LOG_N_ERROR_LINES', $appMessage[ 'line_error' ][ 'count' ] ),
          'type' => 'error' ) ) );

    $app = JFactory::getApplication();

    foreach ( array( 'line_success', 'line_ignored', 'line_error' ) as $key )
    {
     if ( ( key_exists( $key, $appMessage ) )
       and ( key_exists( 'count', $appMessage[ $key ] ) ) )
     {
        if ( $appMessage[ $key ][ 'count' ] != 0 )
        {
          if ($appMessage[ $key ][ 'count' ] > 1)
          {
            $appMessage[ $key ][ 'count' ] = 'N';
          }
          $index = $appMessage[ $key ][ 'count' ];
          $message = $statFormat[ $key ][ $index ][ 'text' ];
          $type = $statFormat[ $key ][ $index ][ 'type' ];
          $app->enqueueMessage( $message, $type );
        }
      }
    }

    foreach ( array( 'info', 'warning', 'error' ) as $key )
    {
     if ( key_exists( $key, $appMessage ) )
     {
        foreach ( $appMessage[ $key ] as $message )
        {
          $type = $noticeFormat[ $key ][ 'type' ];
          $app->enqueueMessage( $message, $type );
        }
      }
    }
  }
}

?>
