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

$user = & JFactory::getUser();
$user->authorize( 'com_users', 'manage' ) or die( JText::_( 'ALERTNOTAUTH' ) );

require_once( JApplicationHelper::getPath( 'admin_html' ) );
require_once( JApplicationHelper::getPath( 'class' ) );
require_once( JApplicationHelper::getPath( 'helper' ) );
jimport( 'joomla.user.helper' );

function _UpdateUser( $task, $userId, $userData, $settings, & $log )
{
  $userUpdated = false;
  $userSuccess = true;
  $userLog = array();

  $user = JUser::getInstance( $userId );
  $groups = JUserportHelper::UserGroups();
  if ( count( array_intersect( $user->groups, $groups[ 'front' ] ) ) == count( $user->groups ) )
  {
    /* Update the existing information where possible and allowed. */

    foreach ( JUserportHelper::Fields( 'import' ) as $field )
    {
      $func = $field;
      if ( method_exists( $settings[ 'update_class' ], $func ) )
      {
        /* $fieldUpdated must become true when the field is changed.
         * $fieldSuccess must become false when any warning or error prevents the usage of the given data. Think of it
         * as a everything-went-smoothly boolean.
         */
        $r = call_user_func_array( array( $settings[ 'update_class' ], $func ), array( & $user, & $userData, $settings, & $userLog ) );
        $fieldUpdated = (bool)$r[0];
        $fieldSuccess = (bool)$r[1];
//         if (!(bool)$r[1])
//         {
//           echo "<br>Func " . $func . " gave error on " . print_r( $userData, 0 ) . "<br/>";
//         }

        /* $userUpdated must become true when any field has been updated.
         * $userSuccess must becoem false when there is any warning or error that has been raised to attent the user
         * on partiallty ignored data, inconsistent data, erroneous data. Think of it as a everythin-went-smoothly
         * boolean.
         */
        if ( $fieldUpdated )
        {
          $userUpdated = true;
        }
        if ( !/*NOT*/$fieldSuccess )
        {
          $userSuccess = false;
        }
      }
    }

    if ( ( JString::strlen( $user->activation ) > 0 )
        and ( JString::strlen( $userData->lastvisitDate ) > 0 )
        and ( $userData->lastvisitDate != "0000-00-00 00:00:00") )
    {
      /* An activation string can only be used if the lastvisitDate is not yet set.
       * We can set or override the lastvisitDate here, ensuring the activation url will work.
       */
      $userData->lastvisitDate = "0000-00-00 00:00:00";
      $string = JText::sprintf( 'COM_USERPORT_WARNING_FIELD_CHANGED_TO_MATCH_ACTIVATION', 'lastvisitDate' );
      $userLog[] = array( 'field_warning', $string );

      $userUpdated = true;
      $userSuccess = false;
    }
    if ( isset( $userData->superfluous ) and ( JString::strlen( $userData->superfluous ) > 0 ) )
    {
      $string = JText::sprintf( 'COM_USERPORT_WARNING_SUPERFLUOUS_FIELDS', $userData->superfluous );
      $userLog[] = array( 'field_warning', $string );

      /* $userUpdated retains its value: true */
      $userSuccess = false;
    }

    /* Store the updated information in the database. */
    if ( $userUpdated )
    {
      if ( $settings[ 'dry_run' ] )
      {
        /* Do not make any modification to the database. */
        $userSaved = true;
      }
      else
      {
        $userSaved = $user->save( false );
      }

      if ( $userSaved )
      {
        $notified = false;
        if ( $settings[ 'notify_user' ] )
        {
          $notified = JUserportHelper::NotifyUser( $task, $user, $settings );
        }
        if ( $notified === true )
        {
          $string = JText::_( 'COM_USERPORT_INFO_USER_NOTIFIED' );
          $userLog[] = array( 'field_info', $string );
        }
        else
        {
          $string = JText::_( 'COM_USERPORT_WARNING_USER_COULD_NOT_BE_NOTIFIED' ) . ' ' . (string)$notified;
          $userLog[] = array( 'field_warning', $string );
        }
      }
      else
      {
        $string = JText::_( 'COM_USERPORT_ERROR_COULD_NOT_UPDATE_EXISTING_USER' ) . ' ' . JText::_( $user->getError() );
        $userLog[] = array( 'field_error', $string );

        $userUpdated = false;
        $userSuccess = false;
      }
    }
    else
    {
      /* $userUpdated retains its value: false */
      /* $userSuccess retains its value */
    }
  }
  else
  {
    /* Only allow changes to users who have front-end-only rights. */
    $string = JText::sprintf( 'COM_USERPORT_WARNING_USER_RIGHTS_PROHIBIT_CHANGE', '['.$userData->username.'] '.$userData->name  );
    $userLog[] = array( 'field_warning', $string );

    $userUpdated = false;
    $userSuccess = false;
  }

  if ( $userUpdated )
  {
    if ( $userSuccess )
    {
      $string = JText::sprintf( 'COM_USERPORT_INFO_EXISTING_USER_UPDATED', '['.$userData->username.'] '.$userData->name );
      $log[] = array( 'line_success', $string, $userLog );
    }
    else
    {
      $string = JText::sprintf( 'COM_USERPORT_INFO_EXISTING_USER_UPDATED', '['.$userData->username.'] '.$userData->name );
      $log[] = array( 'line_warning', $string, $userLog );
    }
  }
  else
  {
    if ( $userSuccess )
    {
      $string = JText::sprintf( 'COM_USERPORT_INFO_EXISTING_USER_WAS_ALREADY_UP_TO_DATE', '['.$userData->username.'] '.$userData->name );
      $log[] = array( 'line_ignored', $string, $userLog );
    }
    else
    {
      $string = JText::sprintf( 'COM_USERPORT_WARNING_EXISTING_USER_NOT_UPDATED', '['.$userData->username.'] '.$userData->name );
      $log[] = array( 'line_error', $string, $userLog );
    }
  }
}

/**
 * @param $task
 * @param $userData $userData->username must exist, this is not checked for.
 * @param $settings
 * @param $log
 */
function _AddUser($task, $userData, $settings, & $log)
{
  $userLog = array();
  $user = new JUser();
  $success = true;

  if ( isset( $userData->superfluous ) and ( strlen( $userData->superfluous ) > 0 ) )
  {
    $string = JText::sprintf( 'COM_USERPORT_WARNING_SUPERFLUOUS_FIELDS', $userData->superfluous );
    $userLog[] = array( 'field_warning', $string );
  }

  foreach ( JUserportHelper::Fields( 'import' ) as $field )
  {
    $func = $field;
    if ( $success and method_exists( $settings[ 'add_class' ], $func ) )
    {
      $success = call_user_func_array( array( $settings[ 'add_class' ], $func ), array( & $user, & $userData, $settings, & $userLog ) );
    }
  }

  if ( $success )
  {
    if ( strlen( $user->activation ) > 0 )
    {
      if ( $user->lastvisitDate != "0000-00-00 00:00:00" )
      {
        $user->lastvisitDate = "0000-00-00 00:00:00";
        $string = JText::sprintf( 'COM_USERPORT_WARNING_FIELD_CHANGED_TO_MATCH_ACTIVATION', 'lastvisitDate' );
        $userLog[] = array( 'field_warning', $string );
      }
    }

    if ( $settings[ 'dry_run' ] )
    {
      /* Do not make any modification to the database. */
    }
    else
    {
      $success = $user->save( false );
    }
  }

  if ( $success )
  {
    $notified = false;
    if ( $settings[ 'notify_user' ] )
    {
      $notified = JUserportHelper::NotifyUser( $task, $user, $settings );
    }
    $string = JText::sprintf( 'COM_USERPORT_INFO_NEW_USER_ADDED', '['.$user->username.'] '.$user->name );
    if ( $notified === true )
    {
      $string .= ' ' . JText::_( 'COM_USERPORT_INFO_USER_NOTIFIED' );
    }
    else
    {
      $string .= ' ' . JText::_( 'COM_USERPORT_WARNING_USER_COULD_NOT_BE_NOTIFIED' ) . ' ' . (string)$notified;
    }
    $log[] = array( 'line_success', $string, $userLog );
  }
  else
  {
    $string = JText::sprintf( 'COM_USERPORT_ERROR_COULD_NOT_ADD_NEW_USER', '[' . $user->username . '] ' . $user->name ) . ' ' . JText::_( $user->getError() );
    $log[] = array( 'line_error', $string, $userLog );
  }
}

function _DeleteUser( $task, $userId, $userData, $settings, & $log )
{
  $user = JUser::getInstance( $userId );
  $groups = JUserportHelper::UserGroups();
  if ( count( array_intersect( $user->groups, $groups[ 'front' ] ) ) == count( $user->groups ) )
  {
    /* Check the name and email. */
    $userLog = array();
    if ( isset( $userData->name ) and JString::strlen( $userData->name ) > 0 )
    {
      $name = $user->name;
      if ( $name != $userData->name)
      {
        $string = JText::sprintf( 'COM_USERPORT_WARNING_NAME_MISMATCH', $userData->name, $name );
        $userLog[] = array( 'field_warning', $string );
      }
    }
    if ( isset( $userData->email ) and JString::strlen( $userData->email ) > 0 )
    {
      $email = $user->email;
      if ( $email != $userData->email )
      {
        $string = JText::sprintf( 'COM_USERPORT_WARNING_EMAIL_MISMATCH', (string)$userData->email, $email);
        $userLog[] = array( 'field_warning', $string );
      }
    }

    /* If userLog is still empty, no mismatches have been discovered.
     * Only in that case we proceed and delete.
     */
    if ( count( $userLog ) == 0)
    {
      if ( $settings[ 'dry_run' ] )
      {
        /* Do not make any modification to the database. */
        $success = true;
      }
      else
      {
        $success = $user->delete();
      }

      if ( $success )
      {
        $notified = false;
        if ( $settings[ 'notify_user' ] )
        {
          $notified = JUserportHelper::NotifyUser( $task, $user, $settings );
        }
        $string = JText::sprintf( 'COM_USERPORT_INFO_EXISTING_USER_DELETED', '['.$userData->username.'] ' . $userData->name );
        if ( $notified === true )
        {
          $string .= ' ' . JText::_( 'COM_USERPORT_INFO_USER_NOTIFIED' );
        }
        else
        {
          $string .= ' ' . JText::_( 'COM_USERPORT_WARNING_USER_COULD_NOT_BE_NOTIFIED' ) . ' ' . (string)$notified;
        }
        $log[] = array( 'line_success', $string );
      }
      else
      {
        $string = JText::sprintf( 'COM_USERPORT_ERROR_COULD_NOT_DELETE_EXISTING_USER', '['.$userData->username.'] '.$userData->name ) . ' ' . JText::_( $user->getError() );
        $log[] = array( 'line_error', $string );
      }
    }
    else
    {
      $string = JText::sprintf( 'COM_USERPORT_WARNING_USER_NOT_DELETED', '['.$user->username.'] ' . $user->name);
      $log[] = array( 'line_error', $string, $userLog );
    }
  }
  else
  {
    /* Only allow changes to users who have front-end-only rights. */
    $string = JText::sprintf( 'COM_USERPORT_WARNING_USER_RIGHTS_PROHIBIT_CHANGE', '['.$userData->username.'] '.$userData->name  );
    $log[] = array( 'line_ignored', $string );
  }
}

/******************************************************************************/


function _Userport_ShowAbortedWindow( $task, $settings, & $log )
{
  $logTitle = JText::_( 'COM_USERPORT_FORM_ABORTED' ).' '.JText::_( 'COM_USERPORT_FORM_LOG' );
  JUserportHtml::PrintLog( $logTitle, $log );
  JUserportHtml::DummyForm( $task );
}

/**
* Groups all functions that start executing a specific task.
* Only static function calls are made.
*/
class JUserportTask
{
  protected function _chooseinitialtext( $task, $settings, & $log )
  {
    JUserportHtml::ChooseInitialText( $task, JTEXT::_( 'COM_USERPORT_LABEL_CHOOSE_INITIAL_TEXT' ) );
  }
  public function chooseinitialtextforexport( $task, $settings, & $log )
  {
    self::_chooseinitialtext( $task, $settings, $log );
  }
  public function chooseinitialtextforimport( $task, $settings, & $log )
  {
    self::_chooseinitialtext( $task, $settings, $log );
  }

  public function add( $task, $settings, & $log )
  {
    $settings[ 'add_class' ] = new JUserportAdd( $settings[ 'params_override_value' ] );
    self::_import( $task, $settings, $log );
  }
  public function update( $task, $settings, & $log )
  {
    $settings[ 'update_class' ] = new JUserportUpdate();
    self::_import( $task, $settings, $log );
  }
  public function delete( $task, $settings, & $log )
  {
    self::_import( $task, $settings, $log );
  }
  protected function _import( $task, $settings, & $log )
  {
    if ( $settings[ 'dry_run' ] )
    {
      $log[] = array( 'warning', JText::_( 'COM_USERPORT_INFO_DRY_RUN_MODE' ) );
    }

    $startTime = microtime( true );
    $maxExecutionTime = ini_get( 'max_execution_time' );
    if ( ( $maxExecutionTime <= 0 ) or ( $maxExecutionTime > 30 ) )
    {
      /* Even when execution time is unrestricted or very large, userport
       * restricts itself. This way, massive imports don't take minutes
       * during which no feedback is given to the user.
       * Another reason is that I don't trust this value, and that 30
       * seconds is the default for Apache servers, which - afaik - rarely
       * gets changed.
       */
      $maxExecutionTime = 30;
    }
    /* Also, ensure execution may last at least 5 seconds. Again, I don't
     * trust this value.
     */
    $latestEndTime = max( $startTime + 5/* seconds */, $startTime + $maxExecutionTime - 5/* seconds */ );

    if ( isset( $settings[ 'parsing_index' ] ) and intval( $settings[ 'parsing_index' ] ) > 0 )
    {
      $log[] = array( 'info', JText::sprintf( 'COM_USERPORT_INFO_CONTINUED_PARSING',  $settings[ 'parsing_index' ] ) );
    }

    $userStrings = explode( "\n", $settings[ 'user_list' ] );

    /* Unset the next-batch-marker.
     * It can be set again below if further batch processing is needed.
     */
    unset( $GLOBALS[ 'userport_next_batch_needs_processing' ] );

    if ( count( $userStrings ) >=2 )
    {
      /* Parse the header */
      $headerString = $userStrings[ 0 ];
      $settings[ 'field_separator' ] = JUserportHelper::DetermineFieldSeparator( $headerString );
      $settings[ 'field_enclosure' ] = JUserportHelper::DetermineFieldEnclosure( $settings[ 'user_list' ] );
      $log[] = array( 'info', JText::sprintf( 'COM_USERPORT_INFO_DETERMINED_CSV_FORMAT', $settings[ 'field_separator' ], $settings[ 'field_enclosure' ] ) );

      /* Beware: str_getcsv apparently also retains spaces before a field separator, after a field enclosure. */

      $headerData = JUserportHelper::CsvStringToObject( $headerString, null, $settings[ 'field_separator' ], $settings[ 'field_enclosure' ], $log );
      $headerData = array_values( (array) $headerData );

      $nrOfValidHeaders = 0;
      foreach ( $headerData as $field )
      {
        if ( in_array( $field, JUserportHelper::Fields( 'import' ) ) )
        {
          $nrOfValidHeaders += 1;
        }
        else
        {
          $log[] = array( 'warning', JText::sprintf( 'COM_USERPORT_WARNING_UNKNOWN_HEADER_FIELD', $field ) );
        }
      }
      if ( $nrOfValidHeaders == 0 )
      {
        $log[] = array( 'error', JText::_( 'COM_USERPORT_ERROR_NO_HEADER_FIELD_RECOGNIZED' ) );
      }
      else
      {
        /* Parse as much of the remainder of the lines as is allowed
         * in this execution block (limited by time).
         */
        $count = count( $userStrings );
        for ( $i = 1; $i < $count; $i++ )
        {
          $userString = $userStrings[ $i ];
          $userData = JUserportHelper::CsvStringToObject( $userString, $headerData, $settings[ 'field_separator' ], $settings[ 'field_enclosure' ], $log );
          /* Camel-case the groups. */
          if ( isset( $userData->groups ) and ( JString::strlen( $userData->groups ) > 0 ) )
          {
            $userData->groups = JString::ucfirst( JString::strtolower( $userData->groups ) );
          }
          switch( $task )
          {
            case 'add':
              if ( isset( $userData->email ) and ( JString::strlen( $userData->email ) > 0 ) )
              {
                if ( isset( $userData->username ) and ( JString::strlen( $userData->username ) > 0 ) )
                {
                  $userId = JUserHelper::getUserId( $userData->username );
                }
                else
                {
                  $userId = 0;
                }
                if ( $userId > 0 )
                {
                  /* Updating or deleting existing users is not requested. */
                  $string = JText::sprintf( 'COM_USERPORT_WARNING_USER_ALREADY_EXISTS', $userData->username );
                  $log[] = array( 'line_ignored', $string );
                }
                else
                {
                  _AddUser( 'add', $userData, $settings, $log );
                }
              }
              else
              {
                $string = JText::sprintf( 'COM_USERPORT_ERROR_FIELD_NOT_SET', $userString, 'email' );
                $log[] = array( 'line_error', $string );
              }
              break;

            case 'update':
              if ( isset( $userData->username ) and ( JString::strlen( $userData->username ) > 0 ) )
              {
                $userId = JUserHelper::getUserId( $userData->username );
                if ( $userId > 0 )
                {
                  _UpdateUser( 'update', $userId, $userData, $settings, $log );
                }
                else
                {
                  /* Adding new users is not requested. */
                  $string = JText::sprintf( 'COM_USERPORT_WARNING_USER_DOES_NOT_EXIST', $userData->username );
                  $log[] = array( 'line_ignored', $string );
                }
              }
              else
              {
                $string = JText::sprintf( 'COM_USERPORT_ERROR_FIELD_NOT_SET', $userString, 'username' );
                $log[] = array( 'line_error', $string );
              }
              break;

            case 'delete':
              if ( isset( $userData->username ) and ( JString::strlen( $userData->username ) > 0 ) )
              {
                $userId = JUserHelper::getUserId( $userData->username );
                if ( $userId > 0 )
                {
                  _DeleteUser( 'delete', $userId, $userData, $settings, $log );
                }
                else
                {
                  /* Adding new users is not requested. */
                  $string = JText::sprintf( 'COM_USERPORT_WARNING_USER_DOES_NOT_EXIST', $userData->username );
                  $log[] = array( 'line_ignored', $string );
                }
              }
              else
              {
                $string = JText::sprintf( 'COM_USERPORT_ERROR_FIELD_NOT_SET', $userString, 'username' );
                $log[] = array( 'line_error', $string );
              }
              break;

            default:
              $string = JText::sprintf( 'COM_USERPORT_ERROR_INVALID_TASK', $task );
              $log[] = array( 'line_error', $string );
              break;
          }

          $currentTime = microtime( true );
          if ( $currentTime >= $latestEndTime )
          {
            /* Further batch processing is needed. Set the needed variables correctly. */
            $log[] = array( 'warning', JText::_( 'COM_USERPORT_WARNING_MAXIMUM_EXECUTION_TIME_REACHED' ) );
            $log[] = array( 'extra', JText::_( 'COM_USERPORT_WARNING_MAXIMUM_EXECUTION_TIME_REACHED' ) );
            $userStrings = array_slice( $userStrings, $i + 1 );
            if ( isset( $settings[ 'parsing_index' ] ) )
            {
              $settings[ 'parsing_index' ] += $i;
            }
            else
            {
              $settings[ 'parsing_index' ] = $i;
            }
            $settings[ 'user_list' ] = $headerString . "\n" . implode( "\n", $userStrings );
            $GLOBALS[ 'userport_next_batch_needs_processing' ] = true;
            break; /* Exit the for loop. */
          }
        }
      }
    }
    else
    {
      $log[] = array( 'error', JText::_( 'COM_USERPORT_ERROR_NO_IMPORT_TEXT' ) );
    }

    $title = array(
    		'add' => JText::_( 'COM_USERPORT_FORM_USERS_ADDED' ).' ',
        'update' => JText::_( 'COM_USERPORT_FORM_USERS_UPDATED' ).' ',
        'delete' => JText::_( 'COM_USERPORT_FORM_USERS_DELETED' ).' ' );
    JUserportHtml::PrintLog( $title[ $task ] . JText::_( 'COM_USERPORT_FORM_LOG' ), $log );
    JUserportHtml::ShowEndResult( $task, $settings );
  }

  protected function _download( $task, $settings, & $log )
  {
    $app = JFactory::getApplication();
    switch ( $task )
    {
      case 'downloadexportedusers':
        $filename = $app->getCfg( 'sitename' ) . '-users-' . date( 'Ymd-Hi' ) . '.csv';
        break;

      case 'downloadimporttext':
      default:
        $filename = $app->getCfg( 'sitename' ) . '-import-' . date( 'Ymd-Hi' ) . '.csv';
        break;
    }
    $filename = JFilterInput::getInstance()->clean( $filename, 'cmd' );

    JUserportHelper::CreateDownloadFile( $settings[ 'user_list' ], $filename, $log );
    /* If this point is reached, the download could not be created and presented to the user.
     * Likely some message is recorded in $log. Display that.
    */
    JUserportHtml::PrintLog( JText::_( 'COM_USERPORT_FORM_LOG' ), $log );
    JUserportHtml::DummyForm( 'export' );
  }
  public function downloadexportedusers( $task, $settings, & $log )
  {
    self::_download( $task, $settings, $log );
  }
  function downloadimporttext( $task, $settings, & $log )
  {
    self::_download( $task, $settings, $log );
  }

  function export( $task, $settings, & $log )
  {
    $userList = JUserportHelper::GetUserList( $task, $settings[ 'groups_override_value' ], $log,
        $settings[ 'filter_combination' ], $settings[ 'block' ], $settings[ 'non_activated' ] );
    $fields = JUserportHelper::Fields( $settings[ 'which_fields' ] );
    $csvExportString = JUserportHelper::ObjectToCsvString( null,
        $settings[ 'field_separator' ], $settings[ 'field_enclosure' ], $fields, $log );
    if ( count( $userList ) > 0 )
    {
      foreach ( $userList as $userData )
      {
        $csvExportString .= JUserportHelper::ObjectToCsvString( $userData,
            $settings[ 'field_separator' ], $settings[ 'field_enclosure' ], $fields, $log );
      }
    }
    $logTitle = JText::_( 'COM_USERPORT_FORM_EXPORTED_USERS' ).' '.JText::_( 'COM_USERPORT_FORM_LOG' );

    JUserportHtml::PrintLog( $logTitle, $log );
    JUserportHtml::ShowExportedUsers( $task, $csvExportString );
  }

  function showeditwindow( $task, $settings, & $log )
  {
    /* First ensure a header line is present by default. */
    $fields = JUserportHelper::Fields( $settings[ 'which_fields' ] );
    $defaultHeader = JUserportHelper::ObjectToCsvString( null,
        $settings[ 'field_separator' ], $settings[ 'field_enclosure' ], $fields, $log );

    switch ( $settings[ 'initial_text' ] )
    {
      case 'empty':
        $initialText = $defaultHeader;
        break;

      case 'file':
        $initialText = JUserportHelper::GetUploadedFile( $log );
        if ( !/*NOT*/ $initialText )
        {
          $initialText = $defaultHeader;
        }
        break;

      case 'current_users':
      default:
        $initialText = $defaultHeader;
        $userList = JUserportHelper::GetUserList( $task, $settings[ 'groups_override_value' ], $log,
            $settings[ 'filter_combination' ], $settings[ 'block' ], $settings[ 'non_activated' ] );
        if ( count( $userList ) > 0 )
        {
          foreach ( $userList as $userData )
          {
            $initialText .= JUserportHelper::ObjectToCsvString( $userData,
                $settings[ 'field_separator' ], $settings[ 'field_enclosure' ], $fields, $log );
          }
        }
        break;
    }

    JUserportHtml::PrintLog( JText::_( 'COM_USERPORT_FORM_LOG' ), $log );
    JUserportHtml::ShowTextarea( $task, JText::_( 'COM_USERPORT_LABEL_USERS_TO_IMPORT' ), $initialText );
  }

  public function showoptionsforadd( $task, $settings, & $log )
  {
    self::_showoptions( $task, $settings, $log );
  }
  public function showoptionsforupdate( $task, $settings, & $log )
  {
    self::_showoptions( $task, $settings, $log );
  }
  public function showoptionsfordelete( $task, $settings, & $log )
  {
    self::_showoptions( $task, $settings, $log );
  }
  protected function _showoptions( $task, $settings, & $log )
  {
    /* - Strip away leading and trailing whitespace, on each line, so that it
     *   doesn't need to be done later any more. This may be done: if
     *   whitespace _is_ the value, the user should enclose it.
     * - remove all empty lines. It makes the code a tiny bit simpler when
     *   importing, and we can then also be sure the header line should be the
     *   first line of the string.
     * - Now parse the header string and present the results to the user, so
     *   he knows immediately how the text is preceived. It helps in catching
     *   errors early.
     * - remove duplicates: check on usernames and emails, or - if that is
     *   absent - names.
     */

    $headerString = '';
    $stat = array();
    $userStrings = explode( "\n", $settings[ 'user_list' ] );
    $count = count( $userStrings );
    for ( $i = 0; $i < $count; $i++ )
    {
      /* Strip whitespace. */
      $userStrings[ $i ] = trim( $userStrings[ $i ] );
      if ( $userStrings[ $i ] )
      {
        if ( !/*NOT*/$headerString )
        {
          $headerString = $userStrings[ $i ];
          $settings[ 'field_separator' ] = JUserportHelper::DetermineFieldSeparator( $headerString );
          $settings[ 'field_enclosure' ] = JUserportHelper::DetermineFieldEnclosure( $settings[ 'user_list' ] );
          $log[] = array( 'info', JText::sprintf( 'COM_USERPORT_INFO_DETERMINED_CSV_FORMAT', $settings[ 'field_separator' ], $settings[ 'field_enclosure' ] ) );

          /* Beware: str_getcsv apparently can't handle spaces before a field separator, after a field enclosure. */

          $headerData = JUserportHelper::CsvStringToObject( $headerString, null, $settings[ 'field_separator' ], $settings[ 'field_enclosure' ], $log );
          $headerData = array_values( (array) $headerData );
          $nrOfValidHeaders = 0;
          $usefulFields = JUserportHelper::Fields( 'import' );
          foreach ( $headerData as $field )
          {
            if ( in_array( $field, $usefulFields ) )
            {
              $nrOfValidHeaders += 1;
            }
            else
            {
              $log[] = array( 'warning', JText::sprintf( 'COM_USERPORT_WARNING_UNKNOWN_HEADER_FIELD', $field ) );
            }
          }
          if ( $nrOfValidHeaders == 0 )
          {
            $log[] = array( 'error', JText::_( 'COM_USERPORT_ERROR_NO_HEADER_FIELD_RECOGNIZED' ) );
            $headerData = array();
          }

          /* Prepare the statistics. */
          foreach ( array( 'username', 'email' ) as $fieldToCheck )
          {
            if ( in_array( $fieldToCheck, $headerData ) )
            {
              $stat[ $fieldToCheck ] = array();
              $stat[ $fieldToCheck ][ 'used' ] = array();
              $stat[ $fieldToCheck ][ 'duplicate' ] = array();
            }
          }
        }
        else
        {
          /* remove duplicates */
          $userData = JUserportHelper::CsvStringToObject( $userStrings[ $i ], $headerData, $settings[ 'field_separator' ], $settings[ 'field_enclosure' ], $log );
          $userData = get_object_vars( $userData );
          foreach( array_keys( $stat ) as $fieldToCheck )
          {
            if ( $userData[ $fieldToCheck ] )
            {
              if ( in_array( $userData[ $fieldToCheck ], $stat[ $fieldToCheck ][ 'used' ] ) )
              {
                if ( !/*NOT*/in_array( $userData[ $fieldToCheck ], $stat[ $fieldToCheck ][ 'duplicate' ] ) )
                {
                  $stat[ $fieldToCheck ][ 'duplicate' ][] = $userData[ $fieldToCheck ];
                }
                unset( $userStrings[ $i ] );
                break; /* Stop looping over array keys of $stat */
              }
              else
              {
                $stat[ $fieldToCheck ][ 'used' ][] = $userData[ $fieldToCheck ];
              }
            }
          }
        }
      }
      else
      {
        unset( $userStrings[ $i ] );
      }
    }
    foreach( array_keys( $stat ) as $fieldToCheck )
    {
      if ( count( $stat[ $fieldToCheck ][ 'duplicate' ] ) > 0 )
      {
        $log[] = array( 'error', JText::sprintf( 'COM_USERPORT_ERROR_DUPLICATES_REMOVED', $fieldToCheck, implode( ', ', $stat[ $fieldToCheck ][ 'duplicate' ] ) ) );
      }
    }
    /// @todo filter on database contents.
    if ( !/*NOT*/ $headerString )
    {
      $log[] = array( 'error', JText::_( 'COM_USERPORT_ERROR_NO_IMPORT_TEXT' ) );
    }

    /* A re-indexing is needed to close the holes created. */
    $userStrings = array_values( $userStrings );
    $settings[ 'user_list' ] = implode( "\n", $userStrings );

    /* Finally build up the screen: display the options, and display the findings. */
    $title = array
    ( 'showoptionsforadd' => JTEXT::_( 'COM_USERPORT_LABEL_ADD_OPTIONS' ),
      'showoptionsforupdate' => JTEXT::_( 'COM_USERPORT_LABEL_UPDATE_OPTIONS' ),
      'showoptionsfordelete' => JTEXT::_( 'COM_USERPORT_LABEL_DELETE_OPTIONS' ) );
    JUserportHtml::PrintLog( JText::_( 'COM_USERPORT_FORM_LOG' ), $log );
    JUserportHtml::ShowOptions( $task, $title[ $task ], $settings, array( 'user_list' ) );
  }

  public function start( $task, $settings, & $log )
  {
    JUserportHelper::CheckPhpFunctions( $log );
    JUserportHelper::CheckUserPlugins( $log );

    JUserportHtml::PrintLog( JText::_( 'COM_USERPORT_FORM_LOG' ), $log );
    JUserportHtml::PrintHelp();
    JUserportHtml::DummyForm( $task );
  }
}

/* ********************************************************************** */

/**
 * userport's 'main function'. Each time this component is loaded, this
 * function is the first function called. It transfers control to a specific
 * task handling function
 * @param $task A string. The task to execute.
 * @param $log An array of arrays. Each unnamed index is an array with two
 * enforced unnamed indices: the first (index 0) leads to a string indicating
 * the type, the second (index 1) leads to a string with the actual message.
 * @return void
 */
function _Userport_HandleTask( $task, & $log )
{
  /*
   * Task connections
   * --
   * start > chooseinitialtextforexport > export > download
   *       > chooseinitialtextforimport > showeditwindow > download
   *                                                     > showoptionsforadd > add
   *                                                     > showoptionsforupdate > update
   *                                                     > showoptionsfordelete > delete
   */

  /*
   * Task > forms
   * --
   * start > dummyForm
   * chooseinitialtextforexport > initialText
   * chooseinitialtextforimport > initialText
   * export > ShowTextarea
   * showeditwindow > userList
   * download > nvt
   * showoptionsforadd > changeOptions
   * showoptionsforupdate > changeOptions
   * showoptionsfordelete > changeOptions
   * add > ShowTextarea
   * update > ShowTextarea
   * delete > ShowTextarea
   */

//   echo "<br />Task: " . $task . "<br />";
  $settings = array();
  $previousForm = JRequest::getVar( 'form' );
  if ( $previousForm )
  {
    foreach ( JRequest::getVar( $previousForm, array(), 'post', 'array') as $key => $value )
    {
      $settings[ $key ] = $value;
    }
  }
  if ( !/*NOT*/ isset( $settings[ 'user_list' ] ) or !/*NOT*/ $settings[ 'user_list' ] )
  {
    $settings[ 'user_list' ] = urldecode( JRequest::getVar( 'user_list' ) );
  }
  /* Avoid a PHP notice when this variable is accessed. It is undefined if the
   * user does not change the default selection (i.e. no selection).
   */
  if ( !/*NOT*/isset( $settings[ 'groups_override_value' ] ) )
  {
    $settings[ 'groups_override_value' ] = null;
  }
  $json = urldecode( JRequest::getVar( 'userport_settings' ) );
  $array = json_decode( $json );
  if ( $array )
  {
    foreach( $array as $key => $value )
    {
      $settings[ $key ] = $value;
    }
  }

  if ( method_exists( 'JUserportTask', $task ) )
  {
    call_user_func_array( array( 'JUserportTask', $task ), array( $task, $settings, & $log ) );
  }
  else
  {
    JUserportTask::start( $task, $settings, $log );
  }
}

/******************************************************************************/

$_userport_log = array();
_Userport_HandleTask( JRequest::getWord( 'task' ), $_userport_log );

//     $user =& JFactory::getUser();
//     $authorisedViewLevels = $user->getAuthorisedViewLevels();
//     $aid = max( $authorisedViewLevels );
//     echo "access levels";
//     foreach ( $authorisedViewLevels as $n => $s )
//     {
//       echo "<br/>". $n . "--" . $s;
//     }
//     echo "<br/>++$aid++";
?>
