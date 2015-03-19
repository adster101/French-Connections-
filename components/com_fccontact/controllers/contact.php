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
class FcContactControllerContact extends JControllerForm
{

  public function send()
  {
    // Check for request forgeries.
    JSession::checkToken('POST') or jexit(JText::_('JINVALID_TOKEN'));

    $app = JFactory::getApplication();
    $model = $this->getModel();
    $state = $model->get('state');
    $params = $state->get('parameters.menu');


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
      $app->setUserState('com_fccontact.contact.data', $data);

      // Redirect back to the contact form.
      $this->setRedirect(JRoute::_('index.php?option=com_fccontact', false));
      return false;
    }

    // Hand the data into the model, create the new user
    $user = $model->save($validate);

    if (!$user)
    {

      $app->setUserState('com_fccontact.contact.data', $data);

      $this->setRedirect(
              JRoute::_('index.php?option=com_registerowner', false), $this->getError(), 'error'
      );

      return false;
    }

    // Flush the data from the session
    $app->setUserState('com_fccontact.contact.data', null);
    $this->setRedirect(JRoute::_('index.php?option=com_fccontact'));

    $this->setMessage(JText::_('COM_FCCONTACT_EMAIL_SUCCESS'),'success');

    return true;
  }

}
