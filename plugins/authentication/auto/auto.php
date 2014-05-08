<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Authentication.cookie
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Joomla Authentication plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Authentication.cookie
 * @since       3.2
 * @note        Code based on http://jaspan.com/improved_persistent_login_cookie_best_practice
 *              and http://fishbowl.pastiche.org/2004/01/19/persistent_login_cookie_best_practice/
 */
class PlgAuthenticationAuto extends JPlugin {

  /**
   * Application object
   *
   * @var    JApplicationCms
   * @since  3.2
   */
  protected $app;

  /**
   * Database object
   *
   * @var    JDatabaseDriver
   * @since  3.2
   */
  protected $db;

  /**
   * This method should handle any authentication and report back to the subject
   *
   * @param   array   $credentials  Array holding the user credentials
   * @param   array   $options      Array of extra options
   * @param   object  &$response    Authentication response object
   *
   * @return  boolean
   *
   * @since   3.2
   */
  public function onUserAuthenticate($credentials, $options, &$response) {

    // No remember me for admin
    if (!$this->app->isAdmin()) {
      return false;
    }

    $response->type = 'Cookie';

    // Get cookie
    $cookieName = md5('autologin');
    $cookieValue = $this->app->input->cookie->get($cookieName);

    if (!$cookieValue) {
      return;
    }

    $cookieArray = explode('.', $cookieValue);

    // Check for valid cookie value
    if (count($cookieArray) != 2) {
      // Destroy the cookie in the browser.
      $this->app->input->cookie->set($cookieName, false, time() - 42000, $this->app->get('cookie_path', '/'), $this->app->get('cookie_domain'));
      JLog::add('Invalid cookie detected.', JLog::WARNING, 'error');

      return false;
    }

    // Filter series since we're going to use it in the query
    $filter = new JFilterInput;
    $series = $filter->clean($cookieArray[1], 'ALNUM');

    // Remove expired tokens
    $query = $this->db->getQuery(true)
            ->delete('#__user_keys')
            ->where($this->db->quoteName('time') . ' < ' . $this->db->quote(time()));
    $this->db->setQuery($query)->execute();

    // Find the matching record if it exists.
    $query = $this->db->getQuery(true)
            ->select($this->db->quoteName(array('user_id', 'token', 'series', 'time')))
            ->from($this->db->quoteName('#__user_keys'))
            ->where($this->db->quoteName('series') . ' = ' . $this->db->quote($series))
            ->where($this->db->quoteName('uastring') . ' = ' . $this->db->quote($cookieName))
            ->order($this->db->quoteName('time') . ' DESC');
    $results = $this->db->setQuery($query)->loadObjectList();

    if (count($results) !== 1) {
      // Destroy the cookie in the browser.
      $this->app->input->cookie->set($cookieName, false, time() - 42000, $this->app->get('cookie_path', '/'), $this->app->get('cookie_domain'));
      $response->status = JAuthentication::STATUS_FAILURE;

      return;
    }

    // We have a user with one cookie with a valid series and a corresponding record in the database.
    else {
      $token = JUserHelper::hashPassword($cookieArray[0]);

      if (!JUserHelper::verifyPassword($cookieArray[0], $results[0]->token)) {
        // This is a real attack! Either the series was guessed correctly or a cookie was stolen and used twice (once by attacker and once by victim).
        // Delete all tokens for this user!
        $query = $this->db->getQuery(true)
                ->delete('#__user_keys')
                ->where($this->db->quoteName('user_id') . ' = ' . $this->db->quote($results[0]->user_id));
        $this->db->setQuery($query)->execute();

        // Destroy the cookie in the browser.
        $this->app->input->cookie->set($cookieName, false, time() - 42000, $this->app->get('cookie_path', '/'), $this->app->get('cookie_domain'));

        // Issue warning by email to user and/or admin?
        JLog::add(JText::sprintf('PLG_AUTH_COOKIE_ERROR_LOG_LOGIN_FAILED', $results[0]->user_id), JLog::WARNING, 'security');
        $response->status = JAuthentication::STATUS_FAILURE;

        return false;
      }
    }

    // Make sure there really is a user with this name and get the data for the session.
    $query = $this->db->getQuery(true)
            ->select($this->db->quoteName(array('id', 'username', 'password')))
            ->from($this->db->quoteName('#__users'))
            ->where($this->db->quoteName('username') . ' = ' . $this->db->quote($results[0]->user_id))
            ->where($this->db->quoteName('requireReset') . ' = 0');
    $result = $this->db->setQuery($query)->loadObject();

    if ($result) {
      // Bring this in line with the rest of the system
      $user = JUser::getInstance($result->id);

      // Set response data.
      $response->username = $result->username;
      $response->email = $user->email;
      $response->fullname = $user->name;
      $response->password = $result->password;
      $response->language = $user->getParam('language');

      // Set response status.
      $response->status = JAuthentication::STATUS_SUCCESS;
      $response->error_message = '';
    } else {
      $response->status = JAuthentication::STATUS_FAILURE;
      $response->error_message = JText::_('JGLOBAL_AUTH_NO_USER');
    }
  }

  /**
   * This is where we delete any authentication cookie when a user logs out
   *
   * @param   array  $options  Array holding options (length, timeToExpiration)
   *
   * @return  boolean  True on success
   *
   * @since   3.2
   */
  public function onUserAfterLogout($options) {
    // No remember me for admin
    if ($this->app->isAdmin()) {
      return false;
    }

    $cookieName = JUserHelper::getShortHashedUserAgent();
    $cookieValue = $this->app->input->cookie->get($cookieName);

    // There are no cookies to delete.
    if (!$cookieValue) {
      return true;
    }

    $cookieArray = explode('.', $cookieValue);

    // Filter series since we're going to use it in the query
    $filter = new JFilterInput;
    $series = $filter->clean($cookieArray[1], 'ALNUM');

    // Remove the record from the database
    $query = $this->db->getQuery(true);
    $query
            ->delete('#__user_keys')
            ->where($this->db->quoteName('series') . ' = ' . $this->db->quote($series));
    $this->db->setQuery($query)->execute();

    // Destroy the cookie
    $this->app->input->cookie->set($cookieName, false, time() - 42000, $this->app->get('cookie_path', '/'), $this->app->get('cookie_domain'));

    return true;
  }

}
