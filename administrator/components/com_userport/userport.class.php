<?php
/*
 * @package userport
 * @copyright 2008-2012 Parvus
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

require_once( JApplicationHelper::getPath( 'helper' ) );
jimport( 'joomla.user.helper' );
jimport( 'joomla.mail.helper' );

/**
 * Groups all functions that add a specific part of the user information.
 * Static function calls may not be made.
 */
class JUserportAdd
{
  private $_allowedGroups = array();
  private $_lowestGroup = array();
  private $_defaultParams = null;
  private $_registerDate = "0000-00-00 00:00:00";

  function __construct( $defaultParamsString )
  {
    $groups = JUserportHelper::UserGroups();
    $this->_allowedGroups = $groups[ 'front' ];
    $this->_lowestGroup = JUserportHelper::LowestGroup();
    $this->_defaultParams = JUserportHelper::ConvertStringToRegistryObject( $defaultParamsString );
    $date =& JFactory::getDate();
    $this->_registerDate = $date->toMySQL();
  }

  public function block( & $user, & $userData, $settings, & $userLog )
  {
    $success = true;

    /* Use the override value as default value. */
    switch ( $settings[ 'block_activate_override_value' ] )
    {
      case 'yes_yes':
        $user->block = 1;
        $user->activation = "";
        break;

      case 'yes_no':
        $user->block = 1;
        $user->activation = JUtility::getHash( JUserHelper::genRandomPassword() );
        break;

      case 'no_yes':
      default:
        $user->block = 0;
        $user->activation = "";
        break;
    }

    if ( $settings[ 'add_block_activate_handling_origin' ] == 'use_override_value' )
    {
      /* Do not consider the import text. */
    }
    else
    {
      if ( isset( $userData->block ) and ( strlen( $userData->block ) > 0 ) )
      {
        $user->block = ( $userData->block ) ? 1 : 0;
      }
      else
      {
        /* Retain the default value set. */
      }
      if ( isset( $userData->activation ) and ( strlen( $userData->activation ) > 0 ) )
      {
        if ( strlen( $userData->activation ) == 32 )
        {
          $user->activation = $userData->activation;
        }
        else
        {
          $string = JText::sprintf( 'COM_USERPORT_ERROR_WRONG_ACTIVATION_STRING_LENGTH', $userData->activation );
          $userLog[] = array( 'field_error', $string );
          $success = false;
        }
      }
      else
      {
        /* Retain the default value set. */
      }
    }

    if ( $user->activation )
    {
      $string = JText::sprintf( 'COM_USERPORT_INFO_ACTIVATION_SET', $user->activation );
      $userLog[] = array( 'info', $string );
      if ( !/*NOT*/ $user->block )
      {
        $user->block = 1;
        $string = JText::sprintf( 'COM_USERPORT_WARNING_FIELD_CHANGED_TO_MATCH_ACTIVATION', 'block' );
        $userLog[] = array( 'field_warning', $string );
      }
    }

    return $success;
  }

  public function email( & $user, & $userData, $settings, & $userLog )
  {
    $success = true;


    if ( !/*NOT*/isset( $userData->email ) || ( !/*NOT*/$userData->email ) )
    {
      $string = JText::_( 'COM_USERPORT_ERROR_NO_EMAIL_FOUND_FOR_NEW_USER' );
      $userLog[] = array( 'field_error', $string );
      $success = false;
    }
    else if ( !/*NOT*/ JMailHelper::isEmailAddress( $userData->email ) )
    {
      $string = JText::sprintf( 'COM_USERPORT_ERROR_EMAIL_INVALID', $userData->email );
      $userLog[] = array( 'field_error', $string );
      $success = false;
    }

    if ( $success )
    {
      $user->email = $userData->email;
    }

    return $success;
  }

  public function groups( & $user, & $userData, $settings, & $userLog )
  {
    /* Use the override value as default value. */
    $user->groups = array_intersect( $this->_allowedGroups, $settings[ 'groups_override_value' ] );

    if ( $settings[ 'add_groups_handling_origin' ] == 'use_override_value' )
    {
      /* Do not consider the import text. */
    }
    else if ( isset( $userData->groups ) and ( strlen( $userData->groups ) > 0 ) )
    {
      $user->groups = JUserportHelper::ConvertStringToGroups( $userData->groups );
    }
    else
    {
      /* Retain the default value set. */
    }

    $retained = array_intersect( $user->groups, $this->_allowedGroups );
    $excluded = array_diff( $user->groups, $this->_allowedGroups );

    if ( count( $retained ) == 0 )
    {
      $retained = $this->_lowestGroup;
      $string = JText::sprintf( 'COM_USERPORT_WARNING_ALL_GROUPS_INVALID_DEFAULT_USED',
          implode( ', ', array_keys( $user->groups ) ),
          $userData->username,
          implode( ', ', array_keys( $retained ) ) );
      $userLog[] = array( 'field_warning', $string );
    }
    else if ( count( $excluded ) > 0 )
    {
      $string = JText::sprintf( 'COM_USERPORT_WARNING_INVALID_GROUPS_EXCLUDED',
          implode( ', ', array_keys( $excluded ) ),
          $userData->username );
      $userLog[] = array( 'field_warning', $string );
    }

    $user->groups = $retained;

    return true;
  }

  public function lastvisitDate( & $user, & $userData, $settings, & $userLog )
  {
    if ( isset( $userData->lastvisitDate ) and ( strlen( $userData->lastvisitDate ) > 0 ) )
    {
      $user->lastvisitDate = $userData->lastvisitDate;
    }
    else
    {
      $user->lastvisitDate = "0000-00-00 00:00:00";
    }
    return true;
  }

  public function name( & $user, & $userData, $settings, & $userLog )
  {
    $success = true;

    $nameOk = ( isset( $userData->name ) and ( strlen( $userData->name ) > 1 ) );
    $usernameOk = ( isset( $userData->username ) and ( strlen( $userData->username ) > 0 ) );
    $emailOk = ( isset( $userData->email ) and ( strlen( $userData->email ) > 0 ) );

    if ( $nameOk )
    {
      if ( $usernameOk )
      {
        /* Nothing to do */
      }
      else
      {
        $userData->username = $userData->name;
      }
    }
    else
    {
      if ( $usernameOk )
      {
        $userData->name = $userData->username;
      }
      else if ( $emailOk )
      {
        $userData->name = $userData->email;
        $userData->username = $userData->email;
      }
      else
      {
        $string = JText::_( 'COM_USERPORT_ERROR_NO_NAME_FOUND_FOR_NEW_USER' );
        $userLog[] = array( 'field_error', $string );
        $success = false;
      }
    }

    if ( $success )
    {
      $user->name = $userData->name;
      $user->username = $userData->username;
    }

    /* This test is placed after the assignment, so that the error message
     * given is more descriptive.
     * Copied from /libraries/joomla/database/table/user.php:
     * JTableUser::check()
     */
    if ( preg_match( "#[<>\"'%;()&]#i", $userData->username)
        or ( strlen( utf8_decode( $userData->username ) ) < 2 ) )
    {
      $string = JText::sprintf( 'JLIB_DATABASE_ERROR_VALID_AZ09', 2 );
      $userLog[] = array( 'field_error', $string );
      $success = false;
    }

    return $success;
  }

  public function params( & $user, & $userData, $settings, & $userLog )
  {
    /* Use the override value as default value. */
    $user->setParameters( $this->_defaultParams );

    if ( $settings[ 'add_params_handling_origin' ] == 'use_override_value' )
    {
      /* Do not consider the import text. */
    }
    else if ( isset( $userData->params ) and ( strlen( $userData->params ) > 0 ) )
    {
      $params = JUserportHelper::ConvertStringToRegistryObject( $userData->params );
      $user->setParameters( $params );
    }
    else
    {
      /* Retain the default value set. */
    }

    return true;
  }

  public function password( & $user, & $userData, $settings, & $userLog )
  {
    $success = true;

    /* Fetch it early, value may be overwritten below. */
    $passwordIsEncoded = $settings[ 'passwords_are_encrypted' ];

    if ( $settings[ 'add_password_handling_origin' ] == 'use_override_value' )
    {
      /* Do not consider the import text. */
      $userData->password = $settings[ 'password_override_value' ];
    }
    else if ( !/*NOT*/isset( $userData->password ) || strlen( $userData->password ) == 0 )
    {
      if ( $settings[ 'add_password_handling_origin' ] == 'use_import_value_else_random' )
      {
        $userData->password = JUserHelper::genRandomPassword();
        $passwordIsEncoded = false;
        $string = JText::sprintf( 'COM_USERPORT_INFO_PASSWORD_GENERATED_FOR_NEW_USER', $userData->password, '[' . $userData->username . '] ' . $userData->name );
        $userLog[] = array( 'field_info', $string );
      }
      else if ( $settings[ 'add_password_handling_origin' ] == 'use_import_value_else_override' )
      {
        $userData->password = $settings[ 'password_override_value' ];
      }
      else
      {
        /* Error. Handled below. */
      }
    }

    if ( isset( $userData->password ) and strlen( $userData->password ) > 0 )
    {
      if ( $passwordIsEncoded )
      {
        $user->password_clear = '********';
        $user->password = $userData->password;
      }
      else
      {
        $user->password_clear = JString::trim( $userData->password );
        if ( $user->password_clear != $userData->password )
        {
          $string = JText::_( 'COM_USERPORT_WARNING_PASSWORD_TRIMMED' );
          $userLog[] = array( 'field_warning', $string );
        }

        $salt = JUserHelper::genRandomPassword( 32 );
        $crypt = JUserHelper::getCryptedPassword( $user->password_clear, $salt );
        $user->password = $crypt . ':' . $salt;
      }
    }
    else
    {
      $string = JText::sprintf( 'COM_USERPORT_ERROR_NO_PASSWORD_FOUND_FOR_NEW_USER', '[' . $userData->username . '] ' . $userData->name );
      $userLog[] = array( 'field_error', $string );
      $success = false;
    }

    return $success;
  }

  public function registerDate( & $user, & $userData, $settings, & $userLog )
  {
    if ( isset( $userData->registerDate ) and ( strlen( $userData->registerDate ) > 0 ) )
    {
      $user->registerDate = $userData->registerDate;
    }
    else
    {
	    $user->registerDate = $this->_registerDate;
    }

    return true;
  }

  public function sendEmail( & $user, & $userData, $settings, & $userLog )
  {
    $user->sendEmail = ( isset( $userData->sendEmail ) and $userData->sendEmail ) ? 1 : 0;
    return true;
  }
}

/**
 * Groups all functions that update a specific part of the user information.
 * Static function calls may not be made.
 */
class JUserportUpdate
{
  private $_allGroups = array();
  private $_allowedGroups = array();
  private $_lowestGroup = array();

  function __construct()
  {
    $groups = JUserportHelper::UserGroups();
    $this->_allGroups = $groups[ 'all' ];
    $this->_allowedGroups = $groups[ 'front' ];
    $this->_lowestGroup = JUserportHelper::LowestGroup();
  }

  /**
   * Also updates activation.
   */
  public function block( & $user, & $userData, $settings, & $userLog )
  {
    $updated = false;
    $success = true;

    if ( $settings[ 'update_block_activate_handling' ] == '1' )
    {
      if ( $settings[ 'update_block_activate_handling_origin' ] == 'use_override_value' )
      {
        /* An override value is set. Do not consider the import text. */
        switch ( $settings[ 'block_activate_override_value' ] )
        {
          case 'yes_yes':
            $block = 1;
            $explicitlySetActivationString = false;
            $explicitlyClearActivationString = true;
            break;

          case 'yes_no':
            $block = 1;
            $explicitlySetActivationString = true;
            $explicitlyClearActivationString = false;
            break;

          case 'no_yes':
            $block = 0;
            $explicitlySetActivationString = false;
            $explicitlyClearActivationString = true;
            break;

          default:
            $block = $user->block;
            $explicitlySetActivationString = false;
            $explicitlyClearActivationString = false;
            break;
        }
      }
      else
      {
        if ( isset( $userData->block ) and ( strlen( $userData->block ) > 0 ) )
        {
          $block = $userData->block;
        }
        else
        {
          /* The override value may not be used and no new value is given. */
          $block = $user->block;
        }
        if ( isset( $userData->activation ) )
        {
          if ( strlen( $userData->activation ) > 0 )
          {
            $explicitlySetActivationString = true;
            $explicitlyClearActivationString = false;
          }
          else
          {
            $explicitlySetActivationString = false;
            $explicitlyClearActivationString = true;
          }
        }
        else
        {
          /* The override value may not be used and no new value is given. */
          $explicitlySetActivationString = false;
          $explicitlyClearActivationString = false;
        }
      }

      /*
       * ActivationString:
       *
       * Option:    |  New UserData:                |   Existing Data:    |
       * Clear Set  |  Not-Given  Non-Empty  Empty  |   Non-Empty  Empty  |  What to do?
       * -----------+-------------------------------+---------------------+-------------------------
       *   Y    N   |   -          -          -     |    -          -     |   Clear (A)
       * -----------+-------------------------------+---------------------+-------------------------
       *   N    Y   |   N          Y          N     |    -          -     |   Set, use UserData (B)
       *   N    Y   |   -          N          -     |    Y          N     |   Retain current status (C)
       *   N    Y   |   -          N          -     |    N          Y     |   Set, generate (D)
       * -----------+-------------------------------+---------------------+-------------------------
       *   N    N   |   Y          N          N     |    -          -     |   Retain current status (E)
       *   N    N   |   N          Y          N     |    -          -     |   Set, use UserData (F)
       *   N    N   |   N          N          Y     |    -          -     |   Clear (G)
       */

      if ( $explicitlyClearActivationString )
      {
        /* Clear: Y - (A) */
        $activation = '';
      }
      else if ( $explicitlySetActivationString )
      {
        if ( isset( $userData->activation ) and ( strlen( $userData->activation ) > 0 ) )
        {
          /* Set: Y, UserData Non-Empty: Y - (B) */
          if ( strlen( $userData->activation ) == 32 )
          {
            $activation = $userData->activation;
          }
          else
          {
            $string = JText::sprintf( 'COM_USERPORT_ERROR_WRONG_ACTIVATION_STRING_LENGTH', $userData->activation );
            $userLog[] = array( 'field_error', $string );
            /* Fall back to: Y, UserData Empty: Y, Existing Data: Y - (C) */
            $activation = $user->activation;
            $success = false;
          }
        }
        else if ( strlen( $user->activation ) > 0 )
        {
          /* Set: Y, UserData Empty: Y, Existing Data: Y - (C) */
          $activation = $user->activation;
        }
        else
        {
          /* Set: Y, Non-Empty: N - (D) */
          $activation = JUtility::getHash( JUserHelper::genRandomPassword() );
        }
      }
      else if ( isset( $userData->activation ) )
      {
        if ( strlen( $userData->activation ) > 0 )
        {
          /* Clear: N, Set: N, Non-Empty: Y - (F) */
          if ( strlen( $userData->activation ) == 32 )
          {
            $activation = $userData->activation;
          }
          else
          {
            $string = JText::sprintf( 'COM_USERPORT_ERROR_WRONG_ACTIVATION_STRING_LENGTH', $userData->activation );
            $userLog[] = array( 'field_error', $string );
            /* Fall back to: Y, UserData Empty: Y, Existing Data: Y - (C) */
            $activation = $user->activation;
            $success = false;
          }
        }
        else
        {
          /* Clear: N, Set: N, Empty: Y - (G) */
          $activation = '';
        }
      }
      else
      {
        /* Clear: N, Set: N, Not-Given: Y - (E) */
        $activation = $user->activation;
      }
      if ( ( !/*NOT*/$block ) and ( strlen( $activation ) > 0 ) )
      {
        $block = 1;
        $string = JText::sprintf( 'COM_USERPORT_WARNING_FIELD_CHANGED_TO_MATCH_ACTIVATION', 'block' );
        $userLog[] = array( 'field_warning', $string );
        $success = false;
      }

      $explicitlyLogBlockedStatus = false;
      if ( $user->activation != $activation )
      {
        if ( $activation )
        {
          if ( $user->activation )
          {
            $string = JText::sprintf( 'COM_USERPORT_INFO_ACTIVATION_UPDATED', $activation );
            $userLog[] = array( 'field_info', $string );
          }
          else
          {
            $string = JText::sprintf( 'COM_USERPORT_INFO_ACTIVATION_SET', $activation );
            $userLog[] = array( 'field_info', $string );
          }
        }
        else
        {
          $string = JText::_( 'COM_USERPORT_INFO_ACTIVATED' );
          $userLog[] = array( 'field_info', $string );
          $explicitlyLogBlockedStatus = true;
        }
        $user->activation = $activation;
      }

      $user->block = (int)$user->block;
      $block = (int)$block;
      if ( ( $user->block != $block ) or $explicitlyLogBlockedStatus )
      {
        if ( strlen( $user->activation ) == 0 )
        {
          $string = $block ? JText::_( 'COM_USERPORT_INFO_BLOCKED' ) : JText::_( 'COM_USERPORT_INFO_UNBLOCKED' );
          $userLog[] = array( 'field_info', $string );
        }
        else
        {
          /* No need to tell the user is blocked. An activation string is set;
           * being blocked now is implied with that.
          */
        }
        $user->block = $block;
        $updated = true;
      }
    }

    return array( $updated, $success );
  }

  public function email( & $user, & $userData, $settings, & $userLog )
  {
    $updated = false;
    $success = true;

    if ( ( !/*NOT*/isset( $userData->email ) ) || ( strlen( $userData->email ) == 0 ) )
    {
      /* No update */
    }
    else if ( !/*NOT*/ JMailHelper::isEmailAddress( $userData->email ) )
    {
      $string = JText::sprintf( 'COM_USERPORT_ERROR_EMAIL_INVALID', $userData->email );
      $userLog[] = array( 'field_error', $string );
      $success = false;
    }
    else if ( $user->email != $userData->email )
    {
      $string = JText::sprintf( 'COM_USERPORT_INFO_EMAIL_UPDATED', $user->email, $userData->email );
      $userLog[] = array( 'field_info', $string );
      $user->email = $userData->email;
      $updated = true;
    }
    return array( $updated, $success );
  }

  public function groups( & $user, & $userData, $settings, & $userLog )
  {
    $updated = false;
    $success = true;

    /* In 1.7, keys are group names.
     * In 2.5 beta 2, keys are group id's.
     * In both, value are groups id's.
     * Fix 2.5 beta 2 behavior to match that of 1.7
     */
    $user->groups = array_intersect( $this->_allGroups, $user->groups );

    if ( $settings[ 'update_groups_handling_origin' ] == 'use_override_value' )
    {
      /* An override value is set. Do not consider the import text. */
      $userData->groups = array_intersect( $this->_allowedGroups, $settings[ 'groups_override_value' ] );
    }
    else if ( isset( $userData->groups ) and ( strlen( $userData->groups ) > 0 ) )
    {
      $userData->groups = JUserportHelper::ConvertStringToGroups( $userData->groups );
    }
    else
    {
      /* The override value may not be used and no new value is given. */
      $userData->groups = array();
    }

    if ( ( count( $userData->groups ) > 0 )
        and ( $settings[ 'update_groups_handling' ] != '0' ) )
    {
      $retained = array_intersect( $userData->groups, $this->_allowedGroups );
      $excluded = array_diff( $userData->groups, $this->_allowedGroups );

      if ( $settings[ 'update_groups_handling' ] == 'merge' )
      {
        if ( count( $retained ) == 0 )
        {
          $string = JText::sprintf( 'COM_USERPORT_WARNING_ALL_GROUPS_INVALID_NONE_USED',
              implode( ', ', array_keys( $userData->groups ) ),
              $userData->username );
          $userLog[] = array( 'field_warning', $string );
          $success = false;
        }
        else
        {
          if ( count( $excluded ) > 0 )
          {
            $string = JText::sprintf( 'COM_USERPORT_WARNING_INVALID_GROUPS_EXCLUDED',
                implode( ', ', array_keys( $excluded ) ),
                $userData->username );
            $userLog[] = array( 'field_warning', $string );
            $success = false;
          }
          $retained = array_diff( $userData->groups, $user->groups );
          if ( count( $retained ) == 0 )
          {
            /* No new information is given. Nothing to do. */
          }
          else
          {
            $string = JText::sprintf( 'COM_USERPORT_INFO_USERGROUPS_ADDED',
                implode( ', ', array_keys( $retained ) ) );
            $user->groups = array_merge( $user->groups, $retained );
            $userLog[] = array( 'field_info', $string );
            $updated = true;
          }
        }
      }
      else /* 'replace' */
      {
        if ( count( $retained ) == 0 )
        {
          $retained = $this->_lowestGroup;
          $string = JText::sprintf( 'COM_USERPORT_WARNING_ALL_GROUPS_INVALID_DEFAULT_USED',
              implode( ', ', array_keys( $user->groups ) ),
              $userData->username,
              implode( ', ', array_keys( $retained ) ) );
          $userLog[] = array( 'field_warning', $string );
          $success = false;
        }
        else if ( count( $excluded ) > 0 )
        {
          $string = JText::sprintf( 'COM_USERPORT_WARNING_INVALID_GROUPS_EXCLUDED',
              implode( ', ', array_keys( $excluded ) ),
              $userData->username );
          $userLog[] = array( 'field_warning', $string );
          $success = false;
        }

        if ( $user->groups != $retained )
        {
          $string = JText::sprintf( 'COM_USERPORT_INFO_USERGROUPS_REPLACED',
              implode( ', ', array_keys( $user->groups ) ),
              implode( ', ', array_keys( $retained ) ) );
          $userLog[] = array( 'field_info', $string );
          $user->groups = $retained;
          $updated = true;
        }
      }
    }

    return array( $updated, $success );
  }

  public function lastvisitDate( & $user, & $userData, $settings, & $userLog )
  {
    $updated = false;
    $success = true;

    if ( strlen( $user->activation ) > 0 )
    {
      /* No matter what the value of lastvisitDate is in $user or $userData,
       * it must become the reset value.
      */
      $lastvisitDate = "0000-00-00 00:00:00";

      /* lastvisitDate can not yet (?) be overriden. I don't see a use. */
//       if ( ( $settings[ 'lastvisitDate' ] != $lastvisitDate )
//           or ( isset( $userData->lastvisitDate ) and ( $userData->lastvisitDate != $lastvisitDate ) ) )
      if ( isset( $userData->lastvisitDate ) and ( $userData->lastvisitDate != $lastvisitDate ) )
      {
        $string = JText::sprintf( 'COM_USERPORT_WARNING_FIELD_CHANGED_TO_MATCH_ACTIVATION', 'lastvisitDate' );
        $userLog[] = array( 'field_warning', $string );
        $success = false;
      }
      $user->lastvisitDate = $lastvisitDate;
    }
    else
    {
      /* lastvisitDate can not yet (?) be overriden. I don't see a use. */
//       if ( strlen( $settings[ 'lastvisitDate' ] ) > 0 )
//       {
//         $lastvisitDate = $settings[ 'lastvisitDate' ];
//       }
//       else
      if ( isset( $userData->lastvisitDate ) and ( strlen( $userData->lastvisitDate ) > 0 ) )
      {
        $lastvisitDate = $userData->lastvisitDate;
      }
      else
      {
        $lastvisitDate = $user->lastvisitDate;
      }

      /* Be sure the string is properly formatted. */
      if ( $lastvisitDate != "0000-00-00 00:00:00" )
      {
        try
        {
          $d =& JFactory::getDate( $lastvisitDate );
          $lastvisitDate = $d->toFormat();
        }
        catch ( Exception $e )
        {
          $string = JText::sprintf( 'COM_USERPORT_ERROR_DATE_INVALID', $lastvisitDate );
          $userLog[] = array( 'field_error', $string );
          $lastvisitDate = $user->lastvisitDate;
          $success = false;
        }
      }

      if ( $success and ( $user->lastvisitDate != $lastvisitDate ) )
      {
        $string = JText::sprintf( 'COM_USERPORT_INFO_LAST_VISIT_DATE_UPDATED', $user->lastvisitDate, $lastvisitDate );
        $userLog[] = array( 'field_info', $string );
        $user->lastvisitDate = $lastvisitDate;
        $updated = true;
      }
    }

    return array( $updated, $success );
  }

  public function name( & $user, & $userData, $settings, & $userLog )
  {
    $updated = false;
    $success = true;

    if ( ( !/*NOT*/isset( $userData->name ) ) || ( strlen( $userData->name ) == 0 ) )
    {
      $userData->name = $user->name;
    }
    if ( $user->name != $userData->name )
    {
      $string = JText::sprintf( 'COM_USERPORT_INFO_NAME_UPDATED', $user->name, $userData->name );
      $userLog[] = array( 'field_info', $string );
      $user->name = $userData->name;
      $updated = true;
    }

    return array( $updated, $success );
  }

  public function params( & $user, & $userData, $settings, & $userLog )
  {
    $updated = false;
    $success = true;

    if ( ( $settings[ 'update_params_handling_origin' ] == 'use_override_value' )
        and ( strlen( $settings[ 'params_override_value' ] ) > 0 ) )
    {
      /* An override value is set. Do not consider the import text. */
      $string = $settings[ 'params_override_value' ];
    }
    else if ( isset( $userData->params ) and ( strlen( $userData->params ) > 0 ) )
    {
      $string = $userData->params;
    }
    else
    {
      /* The override value may not be used and no new value is given.
       * Leave $string undefined.
       */
    }

    if ( isset( $string ) and ( $string != $user->params )
        and ( $settings[ 'update_groups_handling' ] != '0' ) )
    {
      $userData->params = $string;
      $updateParams = JUserportHelper::ConvertStringToRegistryObject( $string );
      $params = $user->getParameters();

      if ( $settings[ 'update_params_handling' ] == 'merge' )
      {
        $params->merge( $updateParams );
        $user->setParameters( $params );
        $string = JText::sprintf( 'COM_USERPORT_INFO_PARAMS_UPDATED', $params->toString() );
        $userLog[] = array( 'field_info', $string );
        $updated = true;
      }
      else /* 'replace' */
      {
        $user->setParameters( $updateParams );
        $string = JText::sprintf( 'COM_USERPORT_INFO_PARAMS_REPLACED', $updateParams->toString() );
        $userLog[] = array( 'field_info', $string );
        $updated = true;
      }
    }

    return array( $updated, $success );
  }

  public function password( & $user, & $userData, $settings, & $userLog )
  {
    $updated = false;
    $success = true;

    $user->password_clear = '********';
    if ( $settings[ 'update_password_handling_origin' ] == 'use_override_value' )
    {
      /* Do not consider the import text. */
      $userData->password = $settings[ 'password_override_value' ];
    }

    if ( isset( $userData->password ) and ( strlen( $userData->password ) > 0 )
        and ( $settings[ 'update_password_handling' ] == '1' ) )
    {
      if ( $settings[ 'passwords_are_encrypted' ] )
      {
        /* $userData->password is already encrypted. */
      }
      else
      {
        $user->password_clear = JString::trim( $userData->password );
        if ( $user->password_clear != $userData->password )
        {
          $string = JText::_( 'COM_USERPORT_WARNING_PASSWORD_TRIMMED' );
          $userLog[] = array( 'field_warning', $string );
        }
        /* Change the password, but try to retain the salt value of the existing
         * password. It might be that the same password is given as new value.
         * When the same salt is used, the resulting hashed password string will
         * be exactly the same.
         */
        $existingPasswordParts = explode( ':', $user->password );
        if ( count( $existingPasswordParts ) == 2 )
        {
          $salt = $existingPasswordParts[1];
        }
        else
        {
          $salt = JUserHelper::genRandomPassword( 32 );
        }
        $crypt = JUserHelper::getCryptedPassword( $user->password_clear, $salt );
        $userData->password = $crypt . ':' . $salt;
      }

      if ( $user->password != $userData->password )
      {
        $string = JText::sprintf( 'COM_USERPORT_INFO_PASSWORD_UPDATED', $user->password_clear );
        $userLog[] = array( 'field_info', $string );
        $user->password = $userData->password;
        $updated = true;
      }
    }

    return array( $updated, $success );
  }

  public function registerDate( & $user, & $userData, $settings, & $userLog )
  {
    $updated = false;
    $success = true;

    /* registerDate can not yet (?) be overriden. I don't see a use. */
//     if ( strlen( $settings[ 'registerDate' ] ) > 0 )
//     {
//       $registerDate = $settings[ 'registerDate' ];
//     }
//     else
    if ( isset( $userData->registerDate ) and ( strlen( $userData->registerDate ) > 0 ) )
    {
      /* Be sure the string is properly formatted. */
      try
      {
        $d =& JFactory::getDate( $userData->registerDate );
        $registerDate = $d->toFormat();
      }
      catch ( Exception $e )
      {
        $string = JText::sprintf( 'COM_USERPORT_ERROR_DATE_INVALID', $registerDate );
        $userLog[] = array( 'field_error', $string );
        $success = false;
      }

      if ( $success and ( $user->registerDate != $registerDate ) )
      {
        $string = JText::sprintf( 'COM_USERPORT_INFO_REGISTRATION_DATE_UPDATED', $user->registerDate, $registerDate );
        $userLog[] = array( 'field_info', $string );
        $user->registerDate = $registerDate;
        $updated = true;
      }
    }
    else
    {
      /* Nothing to do */
    }


    return array( $updated, $success );
  }

  public function sendEmail( & $user, & $userData, $settings, & $userLog )
  {
    $updated = false;
    $success = true;

    /* sendEmail can not yet (?) be overriden. I don't see a use. */
//     if ( ( $settings[ 'sendEmail' ] != 'default' )
//         and ( ( $settings[ 'sendEmail' ] == 0 ) or ( $settings[ 'sendEmail' ] == 1 ) ) )
//     {
//       $sendEmail = $settings[ 'sendEmail' ];
//     }
//     else
    if ( isset( $userData->sendEmail ) and ( strlen( $userData->sendEmail ) > 0 ) )
    {
      $sendEmail = $userData->sendEmail;
    }
    else
    {
      $sendEmail = $user->sendEmail;
    }
    $user->sendEmail = (int)$user->sendEmail;
    $sendEmail = (int)$sendEmail;
    if ( $user->sendEmail != $sendEmail )
    {
      $string = $sendEmail ? JText::_( 'COM_USERPORT_INFO_SENDEMAIL_ON' ) : JText::_( 'COM_USERPORT_INFO_SENDEMAIL_OFF' );
      $userLog[] = array( 'field_info', $string );
      $user->sendEmail = (int)$sendEmail;
      $updated = true;
    }

    return array( $updated, $success );
  }
}
