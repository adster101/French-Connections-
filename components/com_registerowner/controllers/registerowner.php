<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 */
class RegisterownerControllerRegisterowner extends JControllerForm
{

  public function autologin()
  {

    $model = $this->getModel();

    $user = JFactory::getUser(9009);

    $model->setLoginCookie($user);

    // Auto login cookie has been set and record created in the db
    $this->setRedirect(
            JRoute::_('administrator/index.php', false)
    );


    return true;
  }

  public function register()
  {
    // Check for request forgeries.
    JSession::checkToken('POST') or jexit(JText::_('JINVALID_TOKEN'));

    $app = JFactory::getApplication();
    $model = $this->getModel();
    $state = $model->get('state');
    $params = $state->get('parameters.menu');
    $success_article = $params->get('success_article');

    // Include the content helper so we can get the route of the success article
    require_once JPATH_SITE . '/components/com_content/helpers/route.php';

    // Get the data from POST
    $data = $this->input->post->get('jform', array(), 'array');

    // Validate the posted data.
    $form = $model->getForm();
    if (!$form)
    {
      JError::raiseError(500, $model->getError());
      return false;
    }

    $validate = $model->validate($form, $data);

    if ($validate === false)
    {
      // Get the validation messages.
      $errors = $model->getErrors();
      // Push up to three validation messages out to the user.
      for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
      {
        if ($errors[$i] instanceof Exception)
        {
          $app->enqueueMessage($errors[$i]->getMessage(), 'error');
        }
        else
        {
          $app->enqueueMessage($errors[$i], 'error');
        }
      }

      // Save the data in the session.
      $app->setUserState('com_registerowner.register.data', $data);

      // Redirect back to the contact form.
      $this->setRedirect(JRoute::_('index.php?option=com_registerowner', false));
      return false;
    }

    // Hand the data into the model, create the new user
    $user = $model->save($validate);

    if (!$user)
    {

      $app->setUserState('com_registerowner.register.data', $data);

      $this->setRedirect(
              JRoute::_('index.php?option=com_registerowner', false), $this->getError(), 'error'
      );

      return false;
    }

    // Flush the data from the session
    $app->setUserState('com_registerowner.register.data', null);

    $msg = JText::_('COM_REGISTER_SUCCESS');

    // Redirect if it is set in the parameters, otherwise redirect back to where we came from
    // $this->setRedirect(JRoute::_(ContentHelperRoute::getArticleRoute($success_article), false));
    // This sets a login cookie containing the username and password details
    // These are then picked up by the autologin plugin 
    // which in turn are authed and the user logged in...
    $model->setLoginCookie($user);

    // Auto login cookie has been set and record created in the db
    $this->setRedirect(
            JRoute::_('administrator', false)
    );

    return true;
  }

  public function resetpassword()
  {

    // Check for request forgeries.
    JSession::checkToken('POST') or jexit(JText::_('JINVALID_TOKEN'));

    $app = JFactory::getApplication();
    $model = $this->getModel('resetpassword');
    // Get the data from POST
    $data = $this->input->post->get('jform', array(), 'array');

    // Validate the posted data.
    $form = $model->getForm();

    if (!$form)
    {
      JError::raiseError(500, $model->getError());
      return false;
    }

    $validate = $model->validate($form, $data);

    if ($validate === false)
    {
      // Get the validation messages.
      $errors = $model->getErrors();
      // Push up to three validation messages out to the user.
      for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
      {
        if ($errors[$i] instanceof Exception)
        {
          $app->enqueueMessage($errors[$i]->getMessage(), 'error');
        }
        else
        {
          $app->enqueueMessage($errors[$i], 'error');
        }
      }

      // Save the data in the session.
      $app->setUserState('com_registerowner.resetpassword.data', $data);

      // Redirect back to the contact form.
      $this->setRedirect(JRoute::_('/advertise/register?view=resetpassword', false));
      return false;
    }

    if (!$model->save($validate))
    {
      $app->setUserState('com_registerowner.resetpassword.data', $data);

      $this->setRedirect(
              JRoute::_('/advertise/register?view=resetpassword', false), $this->getError(), 'error'
      );

      return false;
    }

    // Flush the data from the session
    $app->setUserState('com_registerowner.resetpassword.data', null);

    // Auto login cookie has been set and record created in the db
    $this->setRedirect(
            JRoute::_('/advertise/register?view=resetpassword&layout=requested', false)
    );

    return true;
  }

}
