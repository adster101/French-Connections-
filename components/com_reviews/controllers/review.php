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
class ReviewsControllerReview extends JControllerForm
{

  /**
   * Method to add a new record.
   *
   * @return  mixed  True if the record can be added, a error object if not.
   *
   * @since   12.2
   */
  public function add()
  {

    $app = JFactory::getApplication();
    $context = "$this->option.edit.$this->context";

    // Access check.
    if (!$this->allowAdd())
    {
      // Set the internal error and also the redirect error.
      $this->setError(JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'));
      $this->setMessage($this->getError(), 'error');

      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option . '&view=' . $this->view_list
                      . $this->getRedirectToListAppend(), false
              )
      );

      return false;
    }

    // Clear the record edit information from the session.
    $app->setUserState($context . '.data', null);

    $input = $app->input;

    $unit_id = $input->get('unit_id', '', 'int');

    // Redirect to the edit screen.
    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($unit_id, 'unit_id'), false
            )
    );

    return true;
  }

  public function submit()
  {

    // Check for request forgeries.
    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

    $app = JFactory::getApplication();
    $user = JFactory::getUser();
    $model = $this->getModel('review');
    $params = JComponentHelper::getParams('com_reviews');

    $CurrentUser = & JFactory::getUser();
    // Get the data from POST
    $data = $this->input->post->get('jform', array(), 'array');

    // Set additional data fields 
    $data['published'] = 0; // Default to unpublish, user either publishes, or trashes and then delete the review
    $data['created'] = date('Y-m-d H:i:s');
    $data['created_by'] = $CurrentUser->id;

    // Check for a valid session cookie
    if ($params->get('validate_session', 0))
    {
      if (JFactory::getSession()->getState() != 'active')
      {
        JError::raiseWarning(403, JText::_('COM_CONTACT_SESSION_INVALID'));

        // Save the data in the session.
        $app->setUserState('com_reviews.review.data', $data);

        // Redirect back to the contact form.
        $this->setRedirect(JRoute::_('my-account/review?task=review.add&unit_id=' . (int) $data['unit_id'], false));
        return false;
      }
    }

    // Validate the posted data.
    $form = $model->getForm();
    if (!$form)
    {
      JError::raiseError(500, $model->getError());
      return false;
    }

    $validData = $model->validate($form, $data);

    if ($validData === false)
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
      $app->setUserState('com_reviews.review.data', $data);

      // Redirect back to the contact form.
      $this->setRedirect(JRoute::_('my-account/review?task=review.add&unit_id=' . (int) $data['unit_id'], false));
      return false;
    }

    // Write the review into the reviews table...
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_reviews/tables');

    $table = JTable::getInstance('Review', 'ReviewTable');

    if (!$table)
    {
      JError::raiseWarning(403, JText::_('COM_REVIEWS_REVIEW_TABLE_NOT_FOUND'));

      // Save the data in the session.
      $app->setUserState('com_reviews.review.data', $data);

      // Redirect back to the contact form.
      $this->setRedirect(JRoute::_('my-account/review?task=review.add&unit_id=' . (int) $data['unit_id'], false));
      return false;
    }

    // Set propertyID to same as ID
    $validData['guest_name'] = $user->name;
    $validData['created_by'] = $user->id;
    $validData['guest_email'] = $user->email;
    $validData['date_created'] = JFactory::getDate()->calendar('Y-m-d');
    $validData['date'] = JFactory::getDate($validData['date'])->calendar('Y-m-d');

    // Check that we can save the data.
    if (!$table->save($validData))
    {

      $errors = $table->getErrors();

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
      $app->setUserState('com_reviews.review.data', $data);
      $this->setRedirect(JRoute::_('my-account/review?task=review.add&unit_id=' . (int) $data['unit_id'], false));

      return false;
    }

    $msg = JText::_('COM_REVIEWS_EMAIL_THANKS');

    // Flush the data from the session
    $app->setUserState('com_reviews.review.data', null);

    // Redirect if it is set in the parameters, otherwise redirect back to where we came from
    if ($params->get('redirect'))
    {
      $this->setRedirect($params->get('redirect'), $msg);
    }
    else
    {
      $this->setRedirect(JRoute::_('index.php?option=com_reviews', false), $msg, 'success');
    }

    return true;
  }

}
