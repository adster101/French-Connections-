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

jimport( 'joomla.filesystem.file' );
require_once( JPATH_LIBRARIES . DS . 'phputf8' . DS . 'utils' . DS . 'validation.php' );

$user = &JFactory::getUser();
$user->authorize( 'com_users', 'manage' ) or die( JText::_( 'ALERTNOTAUTH' ) );

class JUserportHelper
{
  /**
   * Sends an email to the affected user. Given the just performed task, a
   * user will be sent an email, based upon the templates as set in the
   * preferences.
   * @param $task A string. May be one of 'add', 'update' or 'delete'. Other
   * values will not trigger an email.
   * @param $user a JUser instance. References the user that was affected.
   * May not be Null.
   * @param $userLog An array of strings. Gives detailed information what has
   * been changed, and can get inserted in the email.
   * @return mixed True when an email was successfully sent, an error string
   * when a failure occurred.
   */
  public function NotifyUser( $task, $user, $settings, $userLog = array() )
  {
    $result = '?';
    $app = JFactory::getApplication();

    $cc = null;
    $bcc = null;
    if ( $settings[ 'dry_run' ] )
    {
      /* Only send out emails to the 'dry run' email address, leave cc and bcc empty. */
    }
    else
    {
      if ( isset( $settings[ 'cc' ] ) and $settings[ 'cc' ] )
      {
        $cc = $settings[ 'cc' ];
      }
      if ( isset( $settings[ 'bcc' ] ) and $settings[ 'bcc' ] )
      {
        $bcc = $settings[ 'bcc' ];
      }
    }

    $userLogString = '';
    foreach ( $userLog as $logLine )
    {
      $userLogString .= "\n- " . $userLog[1];
    }

    $activationUrl = JURI::root();
    if ( strlen( $user->activation ) > 0 )
    {
      $activationUrl .= "index.php?option=com_user&task=activate&activation=" . $user->activation;
    }
    $token = array
    ( '{user_name}' => $user->name,
    	'{user_email}' => $user->email,
      '{user_username}' => $user->username,
      '{user_login}' => $user->username,
      '{user_password}' => $user->password_clear,
      '{user_groups}' => implode( ', ', $user->groups ),
      '{user_log}' => $userLogString,
      '{site_name}' => $app->getCfg( 'sitename' ),
      '{site_url}' => JURI::root(),
    	'{activation_url}' => $activationUrl );

    $email = array( '{email_subject}' => $settings[ 'email_subject' ], '{email_body}' => $settings[ 'email_body' ] );
    foreach( array_keys( $email ) as $e )
    {
      foreach( array_keys( $token ) as $t )
      {
        $email[$e] = JString::str_ireplace( $t, $token[$t], $email[$e] );
      }
      $email[$e] = html_entity_decode( $email[$e], ENT_QUOTES );
    }

    if ( $settings[ 'dry_run' ] )
    {
      /* Only send out emails to the 'dry run' email address. */
      $recipient = $settings[ 'dry_run_email' ];
    }
    else
    {
      $recipient = $user->email;
    }
    $recipient = JString::trim( $recipient );

    if ( ( JString::strlen( $email[ '{email_subject}' ] ) > 0 )
        and ( JString::strlen( $email[ '{email_body}' ] ) > 0 )
        and ( JString::strlen( $recipient ) > 0 ) )
    {
      $config =& JFactory::getConfig();
      $sender = array( $config->getValue( 'config.mailfrom' ), $config->getValue( 'config.fromname' ) );

      $mailer =& JFactory::getMailer();
      $mailer->setSender( $sender );
      $mailer->addRecipient( $recipient );
      $mailer->setSubject( $email[ '{email_subject}' ] );
      $mailer->setBody( $email[ '{email_body}' ] );
      $mailer->isHTML( true );
      $mailer->Encoding = 'base64';
      $mailer->setBody( $email[ '{email_body}' ] );

      if ( $cc );
      {
        $mailer->addCC( $cc );
      }
      if ( $bcc );
      {
        $mailer->addBCC( $bcc );
      }

      $result =& $mailer->Send();
      if ( $result !== true )
      {
        $result = $result->toString();
      }
      else
      {
        $result = true;
      }
    }

    return $result;
  }

  /**
   * Converts a CSV string to an array.
   * The given string is split in fields. An entry 'superfluous' will be
   * created iff there are more fields than headers; if there are less fields
   * than headers, empty fields are added.
   * @param $string A string. One line of text from which the fields should be
   * extracted.
   * @param $headerData An ordered unnamed array. It is assumed that the field
   * types correspond (this is not checked here).
   * If this value is @c null, the string data is also used as variable names
   * in the resulting object.
   * @param $fieldSeparator A string. The character that is used separating
   * two fields.
   * @param $fieldEnclosure A string. The character that is used to mark separating two fields.
   * @param $log An array of arrays of strings. Used for logging information
   * messages, warnings and errors, interesting for the user of userport.
   * @return An object, with the headerdData as variable names, and the string
   * data as the variable values. The field values in the array are trimmed.
   */
  public function CsvStringToObject( $string, $headerData, $fieldSeparator, $fieldEnclosure, & $log )
  {
    switch ( $fieldSeparator )
    {
      case 'tab':
        $fieldSeparator = "\t";
        break;
      case 'dollar':
        $fieldSeparator = '$';
        break;
      case 'ampersand':
        $fieldSeparator = '&';
        break;
      default:
        /* Retain its value. */
        break;
    }
    switch ( $fieldEnclosure )
    {
      case 'single':
        $fieldEnclosure = "'";
        break;
      case 'double':
        $fieldEnclosure = '"';
        break;
      default:
        /* Retain its value. */
        break;
    }

    if ( function_exists( 'str_getcsv' ) )
    {
      $fields = str_getcsv( $string, $fieldSeparator, $fieldEnclosure );
      /* I must trim whitespace:
       * - The extra space on the line "name; email" is not stripped away on PHP 5.3.10
       * - The extra space on the line "name ;email" is not stripped away on PHP 5.3.6
       * - Others?
       *
       * This is not a nightmare: passwords may not start nor end with whitespace,
       * no field name contains a space, and I have yet to find a case where name,
       * parameters, or any other field value makes sense when it starts or ends with
       * whitespace.
       */
      $count = count( $fields );
      for ( $i = 0; $i < $count; $i++ )
      {
        $fields[ $i ] = JString::trim( $fields[ $i ] );
      }
    }
    else
    {
      /* Do not complain on each and every line about the missing function
       * str_getcsv. A warning message has already been given once to the user.
       */
      $fields = explode( $fieldSeparator, $string );
      $count = count( $fields );
      for ( $i = 0; $i < $count; $i++ )
      {
        $fields[ $i ] = JString::trim( $fields[ $i ] );
        $length = strlen( $fields[ $i ] );
        if ( ( $length >= 2 ) and ( $fields[ $i ][ 0 ] == $fieldEnclosure )
            and ( $fields[ $i ][ $length - 1 ] == $fieldEnclosure ) )
        {
          $fields[ $i ] = substr( $fields[ $i ], 1, -1 );
        }
      }
    }
    if ( !/*NOT*/ $headerData )
    {
      /* Use the $fields array for $headerData.
       * First check that no field value is empty: this can not be used as key in
       * an associative array (as used later below).
       */
      $count = count( $fields );
      for ( $i = 0; $i < $count; $i++ )
      {
        if ( $fields[ $i ] == 'usertype' )
        {
          $fields[ $i ] = 'groups';
        }
        else if ( strlen( $fields[ $i ] ) == 0 )
        {
          $fields[ $i ] = '-nameless-field-' . $i . '-';
        }
      }
      $headerData = $fields;
    }
    while ( count( $fields ) < count( $headerData) )
    {
      $fields[] = '';
    }
    $userData = array();
    $headerCount = count( $headerData );
    for ( $i = 0; $i < $headerCount; $i++ )
    {
      if ( array_key_exists( $headerData[ $i ], $userData ) )
      {
        /* A duplicate field name is present. Report this as a warning. */
        $log[] = array( 'warning', JText::sprintf( 'COM_USERPORT_WARNING_DUPLICATE_FIELD' , $headerData[ $i ] ) );
      }
      else
      {
        $userData[ $headerData[ $i ] ] = $fields[ $i ];
      }
    }
    if ( count( $fields ) > $headerCount )
    {
      $userData[ 'superfluous' ] = implode( $fieldSeparator, array_slice( $fields, $headerCount ) );
    }
    return (object)$userData;
  }

  /**
   * Converts an object to a CSV string. The given object fields are
   * concatenated into a string.
   * @param $userData An object containing the user's data, which must be
   * transformed to a string. If @c null, a line with the field names used
   * will be constructed, which can serve as a header line.
   * @param $fieldSeparator A string. A single character that is used
   * separating two fields.
   * @param $fieldEnclosure A string. A single character that is used to mark
   * the start and the end of a field.
   * @param $userFields Array of string. A list of fields to retain; all other
   * fields are discarded.
   * @param $log An array of arrays of strings. Used for logging information
   * messages, warnings and errors, interesting for the user of userport.
   * @return A string. Contains the csv version of the given object.
   */
  public function ObjectToCsvString( $userData, $fieldSeparator, $fieldEnclosure, $userFields, & $log )
  {
    $data = array();
    foreach ( $userFields as $userField )
    {
      if ( !/*NOT*/isset( $userData ) )
      {
        $data[] = $userField;
      }
      else if ( isset( $userData->$userField ) )
      {
        $data[] = $userData->$userField;
      }
      else
      {
        $data[] = '';
      }
    }

    switch ( $fieldSeparator )
    {
      case 'tab':
        $fieldSeparator = "\t";
        break;
      case 'dollar':
        $fieldSeparator = '$';
        break;
      case 'ampersand':
        $fieldSeparator = '&';
        break;
      default:
        /* Retain its value. */
        break;
    }
    switch ( $fieldEnclosure )
    {
      case 'single':
        $fieldEnclosure = "'";
        break;
      case 'double':
        $fieldEnclosure = '"';
        break;
      default:
        /* Retain its value. */
        break;
    }

    if ( function_exists( 'fputcsv' ) )
    {
      $fp = fopen( "php://temp", 'r+');
      if ( fputcsv( $fp, $data, $fieldSeparator, $fieldEnclosure ) === FALSE )
      {
        $string = '';
        $log[] = array( 'warning', JText::_( 'COM_USERPORT_WARNING_UNKNOWN_ERROR_CONVERTING_TO_CSV' ) );
      }
      else
      {
        rewind( $fp );
        $string = stream_get_contents($fp);
      }
      fclose( $fp );
    }
    else
    {
      /* Do not complain for each and every user about the missing function
       * fputcsv. A warning message has already been given to the user.
       */
      if ( /*NOT*/ $fieldEnclosure )
      {
        $fieldEnclosure = '"';
      }
      $i;
      $count = count( $data );
      for ( $i = 0; $i < $count; $i++ )
      {
        $data[ $i ] = $fieldEnclosure
            . JString::str_ireplace( $fieldSeparator, "\\" . $fieldSeparator, $data[ $i ] )
            . $fieldEnclosure;
      }
      $string = implode( $fieldSeparator, $data ) . "\n";
    }
    return $string;
  }

  /**
   * Determines the most likely field separator, given a string.
   * @param $string @pre The string is assumed to be the header of an import string.
   * This matters: the header can only contain azAZ-_ and the simple logic employed
   * exploits this.
   * @return A character: the assumed field separaror.
   */
  public function DetermineFieldSeparator( $string )
  {
    /* This test will fail on valid csv import strings like
     *   "a,b";"c,d"
     * But those strings can't possibly form a valid header anyway.
     */

    $fieldSeparator = ',';
    if ( JString::strpos( $string, "\t" ) !== false )
    {
      $fieldSeparator = "tab";
    }
    else if ( JString::strpos( $string, ';' ) !== false )
    {
      $fieldSeparator = ';';
    }
    else if ( JString::strpos( $string, ',' ) !== false )
    {
      $fieldSeparator = ',';
    }
    else if ( JString::strpos( $string, '#' ) !== false )
    {
      $fieldSeparator = '#';
    }
    else if ( JString::strpos( $string, '$' ) !== false )
    {
      $fieldSeparator = 'dollar';
    }
    else if ( JString::strpos( $string, '&' ) !== false )
    {
      $fieldSeparator = 'ampersand';
    }
    return $fieldSeparator;
  }

  /**
  * Determines the most likely field enclosure, given a string.
  * @param string $string The string is assumed to contain the field enclosure.
  * Multiple lines may be given.
  * @return A character: the assumed field enclosure.
  */
  public function DetermineFieldEnclosure( $string )
  {
    /* Keep the list of possible field separators limited - what else
     * can one use besides a single or a double quote?
     */
    $singleQuotePos = JString::strpos( $string, "'" );
    $doubleQuotePos = JString::strpos( $string, '"' );

    $fieldEnclosure = "'"; /* Default value */
    if ( $doubleQuotePos !== FALSE )
    {
      if ( $singleQuotePos !== FALSE )
      {
        if ( $singleQuotePos < $doubleQuotePos )
        {
          $fieldEnclosure = "'";
        }
        else /* > */
        {
          $fieldEnclosure = '"';
        }
      }
      else
      {
        $fieldEnclosure = '"';
      }
    }
    return $fieldEnclosure;
  }

  /**
   * Gets a list of one element: the key is the group title, the value is the
   * group id of the group which does not inherit rights from another group.
   * When more than one such groups exist, the group with the lowest id is
   * retained.
   * @return An array with one element. The key is the group name, the value
   * is the group id.
   */
  public function LowestGroup()
  {
    $db = JFactory::getDBO();
    $query = "SELECT g.title, g.id
              FROM #__usergroups g
              ORDER BY g.parent_id ASC
    					LIMIT 1";
    $db->setQuery( $query );
    $row = $db->loadAssoc();
    return array( $row[ 'title' ] => $row[ 'id' ]);
  }

  /**
   * Get a list of all the user groups that have been created.
   * @return array. Keys are 'front', 'back', 'both', 'disallowed' and 'all',
   *   values are an array of groups that do not have access to the back end
   *   ('front'), a list of groups that have access to the back-end ('back'),
   *   all retained groups ('both'), all excluded groups ('disallowed'), and
   *   all groups ('all').
   *   In each array, the keys are the group titles, values are the group ids.
   * @note Only those groups the current user is part of (recursively) are
   *   retained, except for 'all' and superusers: for them all groups are
   *   retained.
   */
  public function UserGroups()
  {
    $db = JFactory::getDBO();
    $query = "SELECT g.title, g.id
          		FROM #__usergroups g
          		ORDER BY g.lft ASC";
    $db->setQuery( $query );
    $allRows = $db->loadAssocList();

    $user = &JFactory::getUser();
    if ( $user->authorise( 'core.admin' ) )
    {
      /* When the current user is a super user, users from all groups may be included. */
      $allowedRows = $allRows;
    }
    else
    {
      $user = &JFactory::getUser();
      $groups = JAccess::getGroupsByUser($user->id, true);
      $query = "SELECT g.title, g.id
            		FROM #__usergroups g
            		WHERE g.id IN (" . implode( ", ", $groups ) . ")
            		ORDER BY g.lft ASC";
      $db->setQuery( $query );
      $allowedRows = $db->loadAssocList();
    }

    $groups = array( 'front' => array(), 'back' => array(), 'both' => array(), 'disallowed' => array(), 'all' => array() );
    foreach( $allRows as $row)
    {
      $groups[ 'all' ][ $row[ 'title' ] ] = $row[ 'id' ];
    }
    foreach( $allowedRows as $row )
    {
      if ( ( JAccess::checkGroup( $row[ 'id' ], 'core.login.admin' ) )
          or ( JAccess::checkGroup( $row[ 'id' ], 'core.admin' ) ) )
      {
        $groups[ 'back' ][ $row[ 'title' ] ] = $row[ 'id' ];
      }
      else
      {
        $groups[ 'front' ][ $row[ 'title' ] ] = $row[ 'id' ];
      }
      $groups[ 'both' ][ $row[ 'title' ] ] = $row[ 'id' ];
    }
    foreach( array_diff_key( $allRows, $allowedRows ) as $row )
    {
      $groups[ 'disallowed' ][ $row[ 'title' ] ] = $row[ 'id' ];
    }
    return $groups;
  }

  /**
   * Splits a string and converts each part to a usergroup.
   * @param $separator The character/string may not appear in the group names.
   * @return array. Keys are the group titles, values are the group ids
   * or -1 when they are not found in the database.
   */
  public function ConvertStringToGroups( $string, $separator = '|' )
  {
    $groups = array();

    $db = JFactory::getDBO();
    $query = "SELECT g.title, g.id
          		FROM #__usergroups g
          		WHERE LOWER(g.title) IN ('" . JString::strtolower( JString::str_ireplace( $separator, "','", $string ) ) . "')
          		ORDER BY g.lft ASC";
    $db->setQuery( $query );
    $rows = $db->loadAssocList();

    $titles = array();
    foreach( $rows as $row )
    {
      $groups[ $row[ 'title' ] ] = $row[ 'id' ];
      $titles[] = JString::strtolower( $row[ 'title' ] );
    }
    foreach( explode( $separator, $string ) as $title )
    {
      $title = JString::trim( $title );
      if ( ( JString::strlen( $title ) > 0 ) and !/*NOT*/in_array( JString::strtolower( $title ), $titles ) )
      {
        $groups[ $title ] = -1;
      }
    }

    return $groups;
  }

  /**
   * Returns a list of user table fields.
   * @param string $which Either 'basic' to include only the most relevant information;
   * either 'import' to include all fields needed for a successful import in another database.
   * or 'all' to include all fields.
   * @return An array.
   */
  public function Fields( $which )
  {
    switch ( $which )
    {
      case 'basic':
        $fields = array( 'name', 'username', 'email', 'password', 'groups' );
        break;

      case 'import':
        $fields = array( 'name', 'username', 'email', 'password', 'groups', 'block', 'sendEmail', 'registerDate', 'lastvisitDate', 'activation', 'params' );
        break;

      case 'all':
      default:
        if ( count( self::$_sAllUserFields ) == 0 )
        {
          $db = JFactory::getDBO();
          $query = "SELECT u.*
                    FROM #__users u
          					LIMIT 1";
          $db->setQuery( $query );
          $row = $db->loadAssoc();
          self::$_sAllUserFields[] = array_keys( $row );
/**/self::$_sAllUserFields[] = array( 'id', 'name', 'username', 'email', 'password', 'groups', 'block', 'sendEmail', 'registerDate', 'lastvisitDate', 'activation', 'params' );
        }
        $fields = self::$_sAllUserFields;
        break;
    }

    return $fields;
  }

  /**
   * Queries the database to retrieve a listing of (a subset of) the userlist
   * @param string $task The name of the task for which users are requested.
   *   The task determines which user groups are allowed to be fetched, and
   *   which not.
   * @param array $groupsFilter List of groups id's. If non-emoty, only the given
   *   groups are matched. In combination with $task this limits which user
   *   groups are fetched. May be an associative array: keys are not looked
   *   at.
   * @param $log
   * @param string $filterCombination
   *   'and' to only retain users matching all criteria
   *   any other value to retain users matching any criterium.
   * @param tri-state $block
   *   0 to match non-blocked users
   *   1 to match blocked users
   *   any other value to not filter on blocked status.
   * @param tri-state $nonActivation
   *   0 to match activated users
   *   1 to match non-activated users
   *   any other value to not filter on activation status.
   * @param string $fieldname
   *   The empty string to not filter on a specific field value
   *   A valid field name to filter on specific vbalues in that field
   * @param string $fieldMatchType
   *   'equals' to filter on exact (case-insensitive) matches
   *   'contains' to filter on (case-insensitive) partial matches
   *   any other value to not filter on a specific field value
   * @param array $fieldValue
   *   A list of strings to filter on one of the specified field values
   *   null to not filter on a specific field value
   * @return An array of objects; each object represents a user. All
   *   table fields are available.
   */
  public function GetUserList( $task, $groupsFilter, & $log, $filterCombination, $block = 'do-not-filter',
      $nonActivation = 'do-not-filter', $fieldname = '', $fieldMatchType = '', $fieldValue = null )
  {
    /* Determine the where clause for blocked and activation status */

    $userStateWhereClause = array(
      'nonblocked-and-nonactivated' => "((u.block = '0') AND ((u.activation != '') AND (u.activation != '0')))",
      'nonblocked-and-   activated' => "((u.block = '0') AND ((u.activation = '') OR (u.activation = '0')))",
      'nonblocked-and-            ' => "(u.block = '0')",
      'nonblocked- or-nonactivated' => "((u.block = '0') OR ((u.activation != '') AND (u.activation != '0')))",
      'nonblocked- or-   activated' => "((u.block = '0') OR ((u.activation = '') OR (u.activation = '0')))",
      'nonblocked- or-            ' => "(u.block = '0')",
      '   blocked-and-nonactivated' => "((u.block = '1') AND ((u.activation != '') AND (u.activation != '0')))",
      '   blocked-and-   activated' => "((u.block = '1') AND ((u.activation = '') OR (u.activation = '0')))",
      '   blocked-and-            ' => "(u.block = '1')",
      '   blocked- or-nonactivated' => "((u.block = '1') OR ((u.activation != '') AND (u.activation != '0')))",
      '   blocked- or-   activated' => "((u.block = '1') OR ((u.activation = '') OR (u.activation = '0')))",
      '   blocked- or-            ' => "(u.block = '1')",
      '          -and-nonactivated' => "((u.activation != '') AND (u.activation != '0'))",
      '          -and-   activated' => "((u.activation = '') OR (u.activation = '0'))",
      '          -and-            ' => "1",
      '          - or-nonactivated' => "((u.activation != '') AND (u.activation != '0'))",
      '          - or-   activated' => "((u.activation = '') OR (u.activation = '0'))",
      '          - or-            ' => "1");
    $userStateLogs = array(
      'nonblocked-and-nonactivated' => array( 'COM_USERPORT_INFO_ONLY_NON_BLOCKED_USERS_ARE_INCLUDED', 'COM_USERPORT_INFO_ONLY_NON_ACTIVATED_USERS_ARE_INCLUDED' ),
      'nonblocked-and-   activated' => array( 'COM_USERPORT_INFO_ONLY_NON_BLOCKED_USERS_ARE_INCLUDED', 'COM_USERPORT_INFO_ONLY_ACTIVATED_USERS_ARE_INCLUDED' ),
      'nonblocked-and-            ' => array( 'COM_USERPORT_INFO_ONLY_NON_BLOCKED_USERS_ARE_INCLUDED' ),
      'nonblocked- or-nonactivated' => array( 'COM_USERPORT_INFO_ONLY_NON_BLOCKED_OR_NON_ACTIVATED_USERS_ARE_INCLUDED' ),
      'nonblocked- or-   activated' => array( 'COM_USERPORT_INFO_ONLY_NON_BLOCKED_OR_ACTIVATED_USERS_ARE_INCLUDED' ),
      'nonblocked- or-            ' => array( 'COM_USERPORT_INFO_ONLY_NON_BLOCKED_USERS_ARE_INCLUDED' ),
      '   blocked-and-nonactivated' => array( 'COM_USERPORT_INFO_ONLY_BLOCKED_USERS_ARE_INCLUDED', 'COM_USERPORT_INFO_ONLY_NON_ACTIVATED_USERS_ARE_INCLUDED' ),
      '   blocked-and-   activated' => array( 'COM_USERPORT_INFO_ONLY_BLOCKED_USERS_ARE_INCLUDED', 'COM_USERPORT_INFO_ONLY_ACTIVATED_USERS_ARE_INCLUDED' ),
      '   blocked-and-            ' => array( 'COM_USERPORT_INFO_ONLY_BLOCKED_USERS_ARE_INCLUDED' ),
      '   blocked- or-nonactivated' => array( 'COM_USERPORT_INFO_ONLY_BLOCKED_OR_NON_ACTIVATED_USERS_ARE_INCLUDED' ),
      '   blocked- or-   activated' => array( 'COM_USERPORT_INFO_ONLY_BLOCKED_OR_ACTIVATED_USERS_ARE_INCLUDED' ),
      '   blocked- or-            ' => array( 'COM_USERPORT_INFO_ONLY_BLOCKED_USERS_ARE_INCLUDED' ),
      '          -and-nonactivated' => array( 'COM_USERPORT_INFO_ONLY_NON_ACTIVATED_USERS_ARE_INCLUDED' ),
      '          -and-   activated' => array( 'COM_USERPORT_INFO_ONLY_ACTIVATED_USERS_ARE_INCLUDED' ),
      '          -and-            ' => array(),
      '          - or-nonactivated' => array( 'COM_USERPORT_INFO_ONLY_NON_ACTIVATED_USERS_ARE_INCLUDED' ),
      '          - or-   activated' => array( 'COM_USERPORT_INFO_ONLY_ACTIVATED_USERS_ARE_INCLUDED' ),
      '          - or-            ' => array() );
    $condition = '';
    switch ( $block )
    {
      case '0': $condition .= 'nonblocked'; break;
      case '1': $condition .= '   blocked'; break;
      default : $condition .= '          '; break;
    }
    $condition .= '-';
    switch ( $filterCombination )
    {
      case 'and': $condition .= 'and'; break;
      default : $condition .= ' or'; break;
    }
    $condition .= '-';
    switch ( $nonActivation )
    {
      case '0': $condition .= '   activated'; break;
      case '1': $condition .= 'nonactivated'; break;
      default : $condition .= '            '; break;
    }


    /* Determine the where clause for matching field values */

    $fieldmatchWhereClause = 1;
    if ( $fieldname and $fieldMatchType and count( $fieldValue ) )
    {
      /// @todo yet to finish, test and debug
      if ( $fieldMatchType == 'equals' )
      {
        $fieldmatchWhereClause = "LOWER(u." . $fieldname . ") IN (" . strtolower( implode( ',', $fieldValue ) ) . ")";
      }
      else
      {
        $fieldmatchWhereClause = "LOWER(u." . $fieldname . ") IN (" . strtolower( implode( ',', $fieldValue ) ) . ")";
      }
    }


    /* Determine the where clause for matching usergroups */

    $groups = self::UserGroups();
    if ( ( $task == 'export' ) or ( $task == 'match' ) )
    {
      $includedGroups = $groups[ 'both' ];
      $excludedGroups = $groups[ 'disallowed' ];
    }
    else
    {
      $includedGroups = $groups[ 'front' ];
      $excludedGroups = $groups[ 'back' ] + $groups[ 'disallowed' ];
    }
    if ( $groupsFilter != null )
    {
      $excludedGroups = $excludedGroups + array_diff( $includedGroups, $groupsFilter );
      $includedGroups = array_intersect( $includedGroups, $groupsFilter );
    }
    if ( count( $excludedGroups ) == 0 )
    {
      $log[] = array( 'info', JText::_( 'COM_USERPORT_INFO_ALL_USERS_ARE_INCLUDED' ) );
    }
    else
    {
      $log[] = array( 'info', JText::sprintf( 'COM_USERPORT_INFO_USERS_FROM_SPECIFIC_GROUPS_ARE_INCLUDED', implode( ', ', array_keys( $includedGroups ) ) ) );
      $log[] = array( 'info', JText::sprintf( 'COM_USERPORT_INFO_USERS_FROM_SPECIFIC_GROUPS_ARE_EXCLUDED', implode( ', ', array_keys( $excludedGroups ) ) ) );
    }
    /* Ensure the groups list is not empty, so that the sql query built up
     * below remains equal both when all users and only some users may eb retrieved.
     */
    $excludedGroups = $excludedGroups + array( '0' );
    $includedGroups = $includedGroups + array( '0' );


    /* Query the database: return all user information and a string containing
     * all the user groups that user is assigned to, with the condition that
     * there is no assigned usergroup that is also on the disallowed usergroups
     * array.
     */
    $query = "SELECT u.*, GROUP_CONCAT(g.title SEPARATOR '|') groups
        		  FROM #__users u
			     		INNER JOIN #__user_usergroup_map m ON u.id = m.user_id
      			  INNER JOIN #__usergroups g on m.group_id = g.id
        		  WHERE " . $userStateWhereClause[ $condition ] . "
        		  	AND ".  $fieldmatchWhereClause . "
        		    AND u.id NOT IN (SELECT uu.id
                                 FROM #__users uu
                                 INNER JOIN #__user_usergroup_map mm ON uu.id = mm.user_id
                                 WHERE mm.group_id IN (" . implode( ',', $excludedGroups ) . "))
    					GROUP BY u.id
    					ORDER BY u.registerDate DESC";
    $query = "SELECT u.*, GROUP_CONCAT(g.title SEPARATOR '|') groups
        		  FROM #__users u
			     		INNER JOIN #__user_usergroup_map m ON u.id = m.user_id
      			  INNER JOIN #__usergroups g on m.group_id = g.id
        		  WHERE " . $userStateWhereClause[ $condition ] . "
        		  	AND ".  $fieldmatchWhereClause . "
        		    AND g.id IN (" . implode( ',', $includedGroups ) . ")
    					GROUP BY u.id
    					ORDER BY u.registerDate DESC";
    $db = & JFactory::getDBO();
    $db->setQuery( $query );
    $rows = $db->loadObjectList();

    if ( $db->getErrorNum() )
    {
      $log[] = array( 'error', JText::sprintf( 'COM_USERPORT_ERROR_COULD_NOT_ACCESS_DATABASE', JText::_( $db->getErrorMsg() ) ) );
    }

    if ( $task != 'match' )
    {
      $log[] = array( 'info', JText::sprintf( 'COM_USERPORT_INFO_RETRIEVED_USER_COUNT', count( $rows ) ) );
    }
    foreach ( $userStateLogs[ $condition ] as $userStateLog )
    {
      $log[] = array( 'info', JText::_( $userStateLog ) );
    }

    return $rows;
  }

  /**
   * Presents a download file to the user, so he can store the given text
   * under the suggested filename locally on his PC.
   * @param $text String. The contents of the file to download.
   * @param $filename String. The suggested filename.
   * @param $log An array of arrays of strings. Used for logging information
   * messages, warnings and errors, interesting for the user of userport.
   */
  public function CreateDownloadFile( $text, $filename, & $log )
  {
    if ( $text )
    {
      $mimeType = 'text/x-csv';
      if ( ini_get( 'zlib.output_compression' ) )
      {
        /* Required for IE. Content-Disposition may get ignored otherwise. */
        ini_set( 'zlib.output_compression', 'Off' );
      }

      header( 'Content-Disposition: attachment; filename=' . $filename );
      header( 'Content-Transfer-Encoding: UTF-8' );
      header( 'Content-Type: ' . $mimeType );

      /* Make the download non-cacheable. */
      header( 'Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT' );
      header( 'Cache-control: private' );
      header( 'Pragma: private' );

      echo $text;
      flush();

      $app = JFactory::getApplication();
      $app->close();
    }
    else
    {
      $log[] = array( 'error', JText::_( 'COM_USERPORT_ERROR_TEXT_EMPTY' ) );
    }
  }


  /**
   * Reads out the entire contents of the file on the server. Performs some basic checks to ensure
   * the text is read well.
   * @param $filenameOnServer The file to read out
   * @param $log An array of arrays of strings. Used for logging information
   * messages, warnings and errors, interesting for the user of userport.
   * @return A string. The contents of the file when all went succesfull.
   */
  protected function ReadFile( $filename, & $log )
  {
    $text = JFile::read( $filename );
    if ( $text === false )
    {
      $log[] = array( 'error', JText::_( 'COM_USERPORT_ERROR_READING_UPLOADED_FILE' ) );
      $text = '';
    }
    else if ( JString::strlen( $text ) == 0 )
    {
      $log[] = array( 'error', JText::_( 'COM_USERPORT_ERROR_FILE_EMPTY' ) );
      $text = '';
    }
    else if ( function_exists( 'mb_detect_encoding' )
        and function_exists( 'mb_convert_encoding' ) )
    {
      if ( !/*NOT*/utf8_is_valid( $text ) )
      {
        $encoding = mb_detect_encoding( $text . "a" );
        /* The encoding detection can get it wrong. It can report that
         * the text is UTF-8 even when we know @c utf8_is_valid() fails.
         */
        if ( !/*NOT*/$encoding or ( $encoding == 'UTF-8' ) )
        {
          $text = utf8_encode( $text );
        }
        else
        {
          $text = mb_convert_encoding( $text, 'UTF-8', $encoding );
        }
      }
    }
    else
    {
      /* Assume all is OK. */
    }

    return $text;
  }

  /**
   * Reads out the entire contents of the uploaded file.
   * @param $log An array of arrays of strings. Used for logging information
   * messages, warnings and errors, interesting for the user of userport.
   * @return A string. The contents of the file when all went succesfull.
   */
  public function GetUploadedFile( & $log )
  {
    $text = '';

    $files = JRequest::getVar( 'initialText', array(), 'files', 'array');
    if ( array_key_exists( 'name', $files ) and array_key_exists( 'file', $files[ 'name' ] ) )
    {
      $filenameOnClientPc = $files[ 'name' ][ 'file' ];
      $filenameOnServer = $files[ 'tmp_name' ][ 'file' ];
    }

    if ( isset( $filenameOnClientPc ) and $filenameOnClientPc )
    {
      if ( isset( $filenameOnServer ) and $filenameOnServer )
      {
        $log[] = array( 'info', JText::sprintf( 'COM_USERPORT_INFO_SELECTED_FILE_TO_UPLOAD', $filenameOnClientPc ) );

        if ( function_exists( 'finfo_open' ) and function_exists( 'finfo_file' )
            and function_exists( 'finfo_close' ) )
        {
          $finfo = finfo_open( FILEINFO_MIME );
          $mime = finfo_file( $finfo, $filenameOnServer );
          finfo_close( $finfo );
          $isTextFile = ( JString::strpos( $mime, 'text/plain' ) !== FALSE );
        }
        else
        {
          /* Assume all is OK. */
          $isTextFile = true;
        }

        if ( $isTextFile )
        {
          $text = JUserportHelper::ReadFile( $filenameOnServer, $log );
        }
        else
        {
          $log[] = array( 'error', JText::_( 'COM_USERPORT_ERROR_FILE_INVALID_TYPE' ) );
        }
      }
      else
      {
        $log[] = array( 'error', JText::sprintf( 'COM_USERPORT_ERROR_UPLOADING_FILE', $filenameOnClientPc ) );
      }
    }
    else
    {
      $log[] = array( 'error', JText::_( 'COM_USERPORT_ERROR_NO_FILE_TO_UPLOAD' ) );
    }

    return $text;
  }

  /**
  * Converts a string to a registry object.
  * The string may be a JSON, XML or INI string. vertical bars | in INI strings are replaced with line endings.
  * @param $string The string version of a registry object
  * @return JRegistry
  */
  public function ConvertStringToRegistryObject( $string )
  {
    $string = JString::trim( $string );

    if ( ( JString::strpos( $string, '{' ) == 0 )
        and ( JString::strpos( $string, '}' ) == JString::strlen( $string ) - 1 ) )
    {
      $format = 'JSON';
    }
    else if ( ( JString::strpos( $string, '<' ) == 0 )
        and ( JString::strpos( $string, '>' ) == JString::strlen( $string ) - 1 ) )
    {
      $format = 'XML';
    }
    else
    {
      $format = 'INI';
      $string = JString::str_ireplace( '|', "\n", $string );
    }

    $params = new JRegistry();
    $params->loadString( $string, $format );
    return $params;
  }

  public function CheckPhpFunctions( & $log )
  {
    $checks = array(
          		'COM_USERPORT_WARNING_PHP_FUNCTIONS_UNAVAILABLE_AFFECTING_IMPORT_TEXT' => array( 'str_getcsv' ),
          		'COM_USERPORT_WARNING_PHP_FUNCTIONS_UNAVAILABLE_AFFECTING_FILE_UPLOAD' => array( 'mb_detect_encoding', 'mb_convert_encoding', 'finfo_open', 'finfo_file', 'finfo_close' ),
          		'COM_USERPORT_WARNING_PHP_FUNCTIONS_UNAVAILABLE_AFFECTING_EXPORT_TEXT' => array( 'fputcsv' ) );
    $nrOfchecksFailed = 0;
    foreach ( $checks as $warning => $funcs )
    {
      $missing = array();
      foreach ( $funcs as $func )
      {
        if ( !/*NOT*/ function_exists( $func ) )
        {
          $missing[] = $func;
        }
      }
      if ( count( $missing ) > 0 )
      {
        if ( $nrOfchecksFailed == 0 )
        {
          /* It was 0, but will become 1 */
          $log[] = array( 'info', JText::_( 'COM_USERPORT_WARNING_PHP_FUNCTIONS_UNAVAILABLE' ) );
        }
        $log[] = array( 'info', JText::sprintf( $warning, implode( ', ', $missing ) ) );
        $nrOfchecksFailed += 1;
      }
    }
  }

  /**
   * @param $log An array of arrays of strings. Used for logging information
   * messages, warnings and errors, interesting for the user of userport.
   */
  public function CheckUserPlugins( & $log )
  {
    $userPlugins = JPluginHelper::getPlugin( 'user' );
    foreach ( $userPlugins as $userPlugin )
    {
      $userPluginHtml = '<em><a href="index.php?option=com_plugins&view=plugins&filter_search=user">user - ' . $userPlugin->name . '</a></em>';

      $userPluginParams = JUserportHelper::ConvertStringToRegistryObject( $userPlugin->params );
      $mailToUser = $userPluginParams->get( 'mail_to_user' );

      if ( $mailToUser === '1' )
      {
        /*
         * The option exists and is configured as such that emails will get
         * sent. Perhaps this is intended, perhaps not.
         * Warn the user!
         */
        $log[] = array( 'warning', JText::sprintf( 'COM_USERPORT_WARNING_USER_PLUGIN_IS_RESPONSIBLE_FOR_SENDING_EMAILS' , $userPluginHtml ) );
      }
      else if ( $mailToUser === '0' )
      {
        /* This plugin will not send emails (we think). Ok. */
      }
      else
      {
        if ( in_array( $userPlugin->name, array( 'profile', 'contactcreator' ) ) )
        {
          /* This plugin does not send out emails. Ok. */
        }
        else if ( $userPlugin->name == 'joomla' )
        {
          if ( JVersion::isCompatible( '2.5.0_Beta2') )
          {
            /* The option exists, but is not yet explicitly set. Its default
             * is configured such that emails will get sent. Perhaps this is
             * intended, perhaps not.
             * Warn the user!
             */
            $log[] = array( 'warning', JText::sprintf( 'COM_USERPORT_WARNING_USER_PLUGIN_IS_RESPONSIBLE_FOR_SENDING_EMAILS' , $userPluginHtml ) );
          }
          else
          {
            /* The option does not exist.
             * That means that sending of emails is hard coded. Warn the user!
             */
            $log[] = array( 'error', JText::sprintf( 'COM_USERPORT_ERROR_USER_PLUGIN_IS_RESPONSIBLE_FOR_SENDING_EMAILS' , $userPluginHtml ) );
          }
        }
        else
        {
          /* The option does not exist. Likely there is no option for that,
           * or the option is differently named. We don't know the plugin,
           * we don't know whether it sends emails, so lets warn the user:
           * he can then check for himself.
           */
          $log[] = array( 'warning', JText::sprintf( 'COM_USERPORT_WARNING_USER_PLUGIN_MAY_BE_RESPONSIBLE_FOR_SENDING_EMAILS' , $userPluginHtml ) );
        }
      }
    }
  }

  /**
   * Numeric array.
   * Holds a list of all user fields, fetched from the database.
   * Filled in and used only in the function @c Fields
   */
  static private $_sAllUserFields = array();
}

?>
