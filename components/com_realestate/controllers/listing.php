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
class RealestateControllerListing extends JControllerForm {

  public function viewsite() {
    // Check for request forgeries.
    JSession::checkToken('GET') or jexit(JText::_('JINVALID_TOKEN'));

    $stub = $this->input->get('id', '', 'int');
    $ip = $_SERVER['REMOTE_ADDR'];
    $id = (int) $stub;

    // Prepare a db query so we can get the website address
    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    $query->select('a.website')
            ->from('#__realestate_property_versions a')
            ->where('a.realestate_property_id = ' . $id)
            ->where('a.website !=\'\'');

    $db->setQuery($query);

    try {

      $result = $db->loadRow();

      if (parse_url($result[0]))
      { // We have a valid web address 
        $website = $result[0];

        // Check that the http:// bit is present, if not add it. Should validate urls better 
        $website = (strpos($website, 'http://') === 0) ? $website : 'http://' . $website;

        // Log the view
        $query->getQuery(true);

        $columns = array('property_id', 'date_created', 'url', 'ip');

        $query->insert('#__website_views');
        $query->columns($columns);

        // Get the date
        $date = JFactory::getDate()->toSql();

        $data = array($db->quote($id), $db->quote($date), $db->quote($website), $db->quote($ip));

        // Update the value in the db        
        $query->values(implode(',', $data));

        $db->setQuery($query);

        $db->execute();

        // Redirect the user to the actual flippin' website
        $this->setRedirect(JRoute::_($website, false));
      }
    } catch (Exception $e) {

      // Log error   
      throw new Exception(JText::sprintf('COM_ACCOMMODATION_ERROR_FETCHING_WEBSITE_DETAILS_FOR', $id, $e->getMessage()), 500);
    }
  }

  public function enquiry() {

    // Check for request forgeries.
    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

    $app = JFactory::getApplication();
    JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/models');
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/tables');
    $model = $this->getModel();
    $params = JComponentHelper::getParams('com_enquiries');
    
    $id = $this->input->get('id', '', 'int');

    // Get the itemID of the accommodation component. 
    $Itemid = SearchHelper::getItemid(array('component', 'com_realestate'));
		$context = "$this->option.enquiry.data";

    // Get the data from POST
    $data = $this->input->post->get('jform', array(), 'array');

    // Get the property details we are adding an enquiry for.
    // Check for a valid session cookie
    if ($params->get('validate_session', 0))
    {
      if (JFactory::getSession()->getState() != 'active')
      {
        JError::raiseWarning(403, JText::_('COM_CONTACT_SESSION_INVALID'));

        // Save the data in the session.
        $app->setUserState($context, $data);

        // Redirect back to the contact form.
        $this->setRedirect(JRoute::_('index.php?option=com_realestate&Itemid=' . (int) $Itemid . '&id=' . (int) $id . '#email', false));
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

    // Validate the data. 
    // Returns either false or the validated, filtered data.
    $validate = $model->validate($form, $data);

    // TO DO - Possibly better to move save from model to here?
    if ($validate === false)
    {
      // Get the validation messages.
      $errors = $model->getErrors();
      // Push up to five validation messages out to the user.
      for ($i = 0, $n = count($errors); $i < $n && $i < 5; $i++) {
        if ($errors[$i] instanceof Exception)
        {
          $app->enqueueMessage($errors[$i]->getMessage(), 'error');
        }
        else
        {
          $app->enqueueMessage($errors[$i], 'error');
        }
      }

      // Trap any errors 
      $errors = $app->getMessageQueue();

      // Save the data in the session.
      $app->setUserState('com_realestate.enquiry.data', $data);
      $app->setUserState('com_realestate.enquiry.messages', $errors);

      // Redirect back to the contact form.
      $this->setRedirect(JRoute::_('index.php?option=com_realestate&Itemid=' . (int) $Itemid . '&id=' . (int) $id . '#email', false));
      return false;
    }

    // Write the enquiry into the enquiry table...
    if (!$model->processEnquiry($validate, $params, $id, ''))
    {

      // Set the message
      $msg = JText::_('COM_ENQUIRY_PROBLEM_SENDING_ENQUIRY');

      // Save the data in the session.
      $app->setUserState('com_realestate.enquiry.data', $data);

      // Redirect back to the contact form.
      $this->setRedirect(JRoute::_('index.php?option=com_realestate&Itemid=' . (int) $Itemid . '&id=' . (int) $id . '#email', false), $msg);

      return false;
    }

    // Save the user form data into the session
    $app->setUserState($context, $validate);

    // Redirect if it is set in the parameters, otherwise redirect back to where we came from
    if ($Itemid)
    {
      $this->setRedirect(
              JRoute::_('index.php?option=com_realestate&Itemid=' . (int) $Itemid . '&id=' . (int) $id . '&view=enquiry', false), $msg
      );
    }
    else
    {
      $this->setRedirect(JRoute::_('/'));
    }

    return true;
  }

}
