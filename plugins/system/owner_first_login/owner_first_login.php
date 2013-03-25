<?php

/* ------------------------------------------------------------------------
  # Modal syslem messages
  # ------------------------------------------------------------------------
  # The Krotek
  # Copyright (C) 2011 Provitiligo.com. All Rights Reserved.
  # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Website: http://www.thekrotek.com
  # Support:  support@thekrotek.com
  ------------------------------------------------------------------------- */

// no direct access

defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');

$lang = JFactory::getLanguage();
$lang->load('plg_system_Owner_first_login');

class plgSystemOwner_first_login extends JPlugin {

  public function __construct(& $subject, $config) {
    parent::__construct($subject, $config);
  }

  function onAfterInitialise() {
    if (!JFactory::getApplication()->isAdmin())
      return true;

    $document = JFactory::getDocument();

    $user = JFactory::getUser();

    $userid = $user->id;

    // For Guest, do nothing and just return, let the joomla handle it
    if (!$userid)
      return;

    $lastvisitdate = JFactory::getUser($userid)->lastvisitDate;
    $block = JFactory::getUser($userid)->block;

    //Check for first login
    if ($lastvisitdate == "0000-00-00 00:00:00" && $block == 0) {


      $script = "	jQuery.noConflict();
					
					jQuery(window).load(function()
					{	
						jQuery('#myModal').modal()";




      $script .= "});";

      $document->addScriptDeclaration($script);
    }
  }

  function onAfterRender() {
    if (!JFactory::getApplication()->isAdmin())
      return true;

    $user = JFactory::getUser();

    $userid = $user->id;

    // For Guest, do nothing and just return, let the joomla handle it
    if (!$userid)
      return;

    $lastvisitdate = JFactory::getUser($userid)->lastvisitDate;
    $block = JFactory::getUser($userid)->block;

    //Check for first login
    if ($lastvisitdate == "0000-00-00 00:00:00" && $block == 0) {



      $output = JResponse::getBody();
      $pattern = '/<\/body>/';

      $replacement = '
          <div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h3 id="myModalLabel">'. JText::_('PLG_OWNER_FIRST_LOGIN_WELCOME') .'</h3>
            </div>
            <div class="modal-body">'.
              JText::_('PLG_OWNER_FIRST_LOGIN_INTRO_MESSAGE')
            .'</div>
            <div class="modal-footer">
              <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
              <a class="btn btn-primary" aria-hidden="true" href="/administrator/index.php?option=com_helloworld">'.JText::_('PLG_OWNER_FIRST_LOGIN_PROCEED_TO_PROPERTY_MANAGER').'</a>
            </div>
          </div>
        </body>';

      $output = preg_replace($pattern, $replacement, $output);

      JResponse::setBody($output);
      
      // Okay, so now we update the lastvisited date
      $now = date('Y-m-d');

      
     
        
      // Set the users session scope accordingly
      $user->set('lastvisitDate', $now);
    }






    return true;
  }

}

?>