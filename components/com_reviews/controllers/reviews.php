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
class ReviewsControllerReviews extends JControllerForm
{
	public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}

	public function submit()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app    = JFactory::getApplication();
		$model  = $this->getModel('reviews');
		$params = JComponentHelper::getParams('com_reviews');
		$stub   = $this->input->get('id','','int');
		$id     = (int) $stub;

		// Get the data from POST
		$data  = $this->input->post->get('jform', array(), 'array');
    
    // Set additional data fields 
    $data['published'] = -3; // Needs review
    
    
    $contact = $model->getItem($id);

		$params->merge($contact->params);

		// Check for a valid session cookie
		if($params->get('validate_session', 0)) {
			if(JFactory::getSession()->getState() != 'active'){
				JError::raiseWarning(403, JText::_('COM_CONTACT_SESSION_INVALID'));

				// Save the data in the session.
				$app->setUserState('com_reviews.review.data', $data);

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
			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'error');
				} else {
					$app->enqueueMessage($errors[$i], 'error');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_reviews.review.data', $data);

			// Redirect back to the contact form.
			$this->setRedirect(JRoute::_('index.php?option=com_reviews&view=reviews&Itemid=167&id='.$stub, false));
			return false;
		}
    
    // Write the review into the reviews table...
    
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_reviews/tables');
    
    $table = JTable::getInstance('Review', 'ReviewTable');
    
    if (!$table) {
  		JError::raiseWarning(403, JText::_('COM_REVIEWS_REVIEW_TABLE_NOT_FOUND'));

    	// Save the data in the session.
			$app->setUserState('com_reviews.review.data', $data);

			// Redirect back to the contact form.
			$this->setRedirect(JRoute::_('index.php?option=com_reviews&view=reviews&Itemid=167&id='.$stub, false));
     	return false;    
    }
    
    // Set propertyID to same as ID
    $data['property_id'] = $data['id'];
    
    // And unset id incase it gets bound somehow...
    unset($data['id']);
    
    
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
			$app->setUserState('com_reviews.review.data', $data);  	
      $this->setRedirect(JRoute::_('index.php?option=com_reviews&view=reviews&Itemid=167&id='.$stub, false));
  
      return false;
    }
            
		// Send the email
		$sent = false;
		if (!$params->get('custom_reply')) {
			$sent = $this->_sendEmail($data, $params);
		}

		// Set the success message if it was a success
		if (!($sent instanceof Exception)) {
			$msg = JText::_('COM_REVIEWS_EMAIL_THANKS');
		} else {
			$msg = '';
		}

		// Flush the data from the session
		$app->setUserState('com_reviews.review.data', null);

		// Redirect if it is set in the parameters, otherwise redirect back to where we came from
		if ($params->get('redirect')) {
			$this->setRedirect($params->get('redirect'), $msg);
		} else {
			$this->setRedirect(JRoute::_('index.php?option=com_reviews&view=reviews&Itemid=167&id='.$stub, false), $msg);
		}

		return true;
	}

	private function _sendEmail($data, $params)
	{
			$app		= JFactory::getApplication();
			$params 	= JComponentHelper::getParams('com_contact');
			if ($contact->email_to == '' && $contact->user_id != 0) {
				$contact_user = JUser::getInstance($contact->user_id);
				$contact->email_to = $contact_user->get('email');
			}
			$mailfrom	= $app->getCfg('mailfrom');
			$fromname	= $app->getCfg('fromname');
			$sitename	= $app->getCfg('sitename');
			$copytext 	= JText::sprintf('COM_CONTACT_COPYTEXT_OF', $contact->name, $sitename);

			$name		= $data['contact_name'];
			$email		= $data['contact_email'];
			$subject	= $data['contact_subject'];
			$body		= $data['contact_message'];

			// Prepare email body
			$prefix = JText::sprintf('COM_CONTACT_ENQUIRY_TEXT', JURI::base());
			$body	= $prefix."\n".$name.' <'.$email.'>'."\r\n\r\n".stripslashes($body);

			$mail = JFactory::getMailer();
			$mail->addRecipient('adam@littledonkey.net');
			$mail->addReplyTo(array($email, $name));
			$mail->setSender(array($mailfrom, $fromname));
			$mail->setSubject($sitename.': '.$subject);
			$mail->setBody($body);
			$sent = $mail->Send();

			return $sent;
	}
}
