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
class AccommodationControllerProperty extends JControllerForm
{
	public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}
  
  public function viewsite() 
  {    
		// Check for request forgeries.
		JSession::checkToken('GET') or jexit(JText::_('JINVALID_TOKEN'));

    $stub   = $this->input->get('id','','int');
    
		$id     = (int) $stub;
    
    // Prepare a db query so we can get the website address
    $db = JFactory::getDbo();
    
    $query = $db->getQuery(true);
    
    $query->select('upf.website')
            ->from('#__helloworld hw')
            ->leftJoin('#__user_profile_fc upf on upf.user_id = hw.created_by')
            ->where('hw.id = ' . $id)
            ->where('upf.website !=\'\'');
    
    $db->setQuery($query);
    
    try {
      
      $result = $db->loadRow();
      
      if ( parse_url( $result[0] )) { // We have a valid web address 

        $website = $result[0];
        
        // Log the view
        $query->getQuery(true);
        
        $columns = array('property_id','date');
       
        $query->insert('#__website_views');
        $query->columns($columns);
        
        // Get the date
        $date	= JFactory::getDate()->toSql();
        
        // Update the value in the db        
        $query->values("$id,'$date'");
        
        $db->setQuery($query);
              
        $db->execute();
        
        // Redirect the user to the actual flippin' website
        $this->setRedirect(JRoute::_($website, false));
        
      } 
       
      
      
    } catch (Exception $e) {
      // Log error
      print_r($e->getMessage());
    }
      
    
    
    
    
    
  }

	public function enquiry()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app    = JFactory::getApplication();
		$model  = $this->getModel('property');
		$params = JComponentHelper::getParams('com_enquiries');
		$stub   = $this->input->get('id','','int');
		$id     = (int) $stub;

		// Get the data from POST
		$data  = $this->input->post->get('jform', array(), 'array');
               
    // Get the property details we are adding an enquiry for.
    $property = $model->getItem($id);
        
		// Check for a valid session cookie
		if($params->get('validate_session', 0)) {
      if(JFactory::getSession()->getState() != 'active'){
				JError::raiseWarning(403, JText::_('COM_CONTACT_SESSION_INVALID'));

				// Save the data in the session.
				$app->setUserState('com_enquiry.enquiry.data', $data);

				// Redirect back to the contact form.
				$this->setRedirect(JRoute::_('index.php?option=com_reviews&view=reviews&Itemid=167&id='.$stub, false));
				return false;
			}
		}

		// Validate the posted data.
		$form = $model->getForm();
		if (!$form) {
			JError::raiseError(500, $model->getError());
			return false;
		}

		$validate = $model->validate($form, $data);
    
	
    if ($validate === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();
			// Push up to five validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 5; $i++) {
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'error');
				} else {
					$app->enqueueMessage($errors[$i], 'error');
				}
			}

      // Trap any errors 
      $errors = $app->getMessageQueue();
      
			// Save the data in the session.
			$app->setUserState('com_accommodation.enquiry.data', $data);
			$app->setUserState('com_accommodation.enquiry.messages', $errors);

			// Redirect back to the contact form.
			$this->setRedirect(JRoute::_('index.php?option=com_accommodation&view=property&id='.$stub.'#email', false));
			return false;
		}
    
   
    // Write the review into the reviews table...
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_enquiries/tables');
    
    $table = JTable::getInstance('Enquiry', 'EnquiriesTable');
    
    if (!$table) {
  		JError::raiseWarning(403, JText::_('COM_ENQUIRY_TABLE_NOT_FOUND'));

    	// Save the data in the session.
			$app->setUserState('com_accommodation.enquiry.data', $data);

			// Redirect back to the contact form.
			$this->setRedirect(JRoute::_('index.php?option=com_accommodation&view=property&id='.$stub.'#email', false));
     	return false;    
    }
    
    // Set propertyID to same as ID
    $data['property_id'] = $data['id'];
    
    // And unset id incase it gets bound somehow...
    unset($data['id']);
    
    // Get the date
    $date	= JFactory::getDate();

    // Set the date created timestamp
    $data['date_created'] = $date->toSql();
    
    // Check that we can save the data.
    if(!$table->save($data)){
      
      $errors	= $table->getErrors();
      
			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'error');
				} else {
					$app->enqueueMessage($errors[$i], 'error');
				}
			}
      
      // Save the data in the session.
			$app->setUserState('com_accommodation.enquiry.data', $data);  	
			$this->setRedirect(JRoute::_('index.php?option=com_accommodation&view=property&id='.$stub.'#email', false));
  
      return false;
    }
    
  
		// Send the email
		$sent = false;
		
		$sent = $this->_sendEmail($data, $params, $property);
		
    // Also need to send a notification email to the holiday maker?
    
		// Set the success message if it was a success
		if (!($sent instanceof Exception)) {
			$msg = JText::_('COM_REVIEWS_EMAIL_THANKS');
		} else {
			$msg = '';
		}
    
		// Flush the data from the session
		$app->setUserState('com_accommodation.enquiry.data', null);

		// Redirect if it is set in the parameters, otherwise redirect back to where we came from
		if ($params->get('redirect')) {
			$this->setRedirect($params->get('redirect'), $msg);
		} else {
			$this->setRedirect(JRoute::_('index.php?option=com_accommodation&view=property&id='.$stub.'#email', true));
		}

		return true;
	}

	private function _sendEmail($data, $params, $property)
	{
    
			$app		= JFactory::getApplication();
      
      // If there is a valid user for this property then get the email address
			if ($property->created_by != 0) {
				$property_user = JUser::getInstance($property->created_by);

        $property->email = $property_user->get('email');
        $property->name = $property_user->get('name');
        
        // Also need to get the user profile details here (for SMS prefs etc) 
        
			}
      
      
      // The details of where who is sending the email (e.g. FC in this case).
      $mailfrom	= $app->getCfg('mailfrom');
			$fromname	= $app->getCfg('fromname');
			$sitename	= $app->getCfg('sitename');

      
      // The details of the enquiry as submitted by the holiday maker
			$firstname		= $data['forename'];
      $surname      = $data['surname'];
      $email        = $data['email'];
      $phone        = $data['phone'];
      $body         = $data['message'];
      $arrival      = $data['start_date'];
      $end          = $data['end_date'];
      $adults       = $data['adults'];
      $children     = $data['children'];
      
			// Prepare email body
			$body = JText::sprintf($params->get('owner_email_enquiry_template'), $firstname, $surname, $email, $phone, stripslashes($body), $arrival,$end,$adults, $children);
			
			$mail = JFactory::getMailer();
      
			$mail->addRecipient($property->email, $property->name);
			$mail->addReplyTo(array($mailfrom, $fromname));
			$mail->setSender(array($mailfrom, $fromname));
      $mail->addBCC($mailfrom, $fromname);
			$mail->setSubject($sitename.': '.JText::sprintf('COM_ENQUIRIES_NEW_ENQUIRY_RECEIVED', $property->title));
			$mail->setBody($body);
			$sent = $mail->Send();

			return $sent;
	}
}
