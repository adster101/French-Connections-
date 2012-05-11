<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * RSForm! Pro system plugin
 */

class plgSystemRSFPReCaptcha extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatibility we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param 	array   $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function plgSystemRSFPReCaptcha( &$subject, $config )
	{
		parent::__construct( $subject, $config );
		$this->newComponents = array(24);
	}
	
	function canRun()
	{
		if (class_exists('RSFormProHelper')) return true;
		
		$helper = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsform'.DS.'helpers'.DS.'rsform.php';
		if (file_exists($helper))
		{
			require_once($helper);
			RSFormProHelper::readConfig();
			return true;
		}
		
		return false;
	}
	
	function loadLibrary()
	{
		/* Get the recaptcha library */
		if (RSFormProHelper::isJ16())
			require_once (JPATH_SITE.DS.'plugins'.DS.'system'.DS.'rsfprecaptcha'.DS.'relib.php');
		else
			require_once (JPATH_SITE.DS.'plugins'.DS.'system'.DS.'relib.php');
	}
	
	function rsfp_bk_onAfterShowComponents()
	{
		if (!$this->canRun()) return;
		
		$lang =& JFactory::getLanguage();
		$lang->load('plg_system_rsfprecaptcha', JPATH_ADMINISTRATOR);
		
		$formId = JRequest::getInt('formId');
		$components = RSFormProHelper::componentExists($formId, 24);
		$link = "displayTemplate('24')";
		if (!empty($components))
			$link = "displayTemplate('24', '".$components[0]."')";
		
		?>
		<li class="rsform_navtitle"><?php echo JText::_('RSFP_RECAPTCHA_LABEL'); ?></li>
		<li><a href="javascript: void(0);" onclick="<?php echo $link;?>;return false;" id="rsfpc24"><span id="recaptcha"><?php echo JText::_('RSFP_RECAPTCHA_SPRODUCT'); ?></span></a></li>
		<?php
	}
	
	function rsfp_bk_onAfterCreateComponentPreview($args = array())
	{
		if (!$this->canRun()) return;
		
		if ($args['ComponentTypeName'] == 'recaptcha')
		{
			$args['out'] ='<td>'.$args['data']['CAPTION'].'</td>';
			$args['out'] .='<td>{reCAPTCHA field}</td>';
		}
	}
	
	function rsfp_bk_onAfterShowConfigurationTabs()
	{
		if (!$this->canRun()) return;
		
		$lang =& JFactory::getLanguage();
		$lang->load('plg_system_rsfprecaptcha', JPATH_ADMINISTRATOR);
		
		jimport('joomla.html.pane');
		$tabs =& JPane::getInstance('Tabs', array(), true);
		
		echo $tabs->startPanel(JText::_('RSFP_RECAPTCHA_LABEL'), 'form-recaptcha');
			$this->recaptchaConfigurationScreen();
		echo $tabs->endPanel();
	}
	
	function rsfp_bk_onAfterCreateFrontComponentBody($args)
	{
		if (!$this->canRun()) return;
		
		$lang =& JFactory::getLanguage();
		$lang->load('plg_system_rsfprecaptcha', JPATH_ADMINISTRATOR);
		
		$this->loadLibrary();
		
		RSFormProHelper::readConfig();
		$config  = JFactory::getConfig();
		$u 		 = JFactory::getURI();
		$use_ssl = $config->getValue('config.force_ssl') == 2 || $u->isSSL();
		
		if ($args['r']['ComponentTypeId'] == 24)
		{
			// Get a key from http://recaptcha.net/api/getkey
			$publickey = RSFormProHelper::getConfig('recaptcha.public.key');
			
			// the response from reCAPTCHA
			$resp = null;
			// the error code from reCAPTCHA, if any
			$error = null;
			$args['out']  = '<script type="text/javascript">'."\n";
			$args['out'] .= 'var RecaptchaOptions = {'."\n";
			$args['out'] .= "\t"."theme : '".RSFormProHelper::getConfig('recaptcha.theme')."',"."\n";
			
			$lang =& JFactory::getLanguage();
			
			$tag = $lang->getTag();
			$tag = explode('-', $tag);
			$tag = strtolower($tag[0]);
			
			$known_languages = array('en', 'nl', 'fr', 'de', 'pt', 'ru', 'es', 'tr');
			if (in_array($tag, $known_languages))
			{
				$args['out'] .= "\t"."lang : '".$tag."'"."\n";
			}
			else
			{
				$args['out'] .= "\t"."lang : '".$tag."',"."\n";
				$args['out'] .= "\t"."custom_translations : {"."\n"
							   ."\t"."\t".'instructions_visual : \''.JText::_('RSFP_INSTRUCTIONS_VISUAL', true).'\','."\n"
							   ."\t"."\t".'instructions_audio : \''.JText::_('RSFP_INSTRUCTIONS_AUDIO', true).'\','."\n"
							   ."\t"."\t".'play_again : \''.JText::_('RSFP_PLAY_AGAIN', true).'\','."\n"
							   ."\t"."\t".'cant_hear_this : \''.JText::_('RSFP_CANT_HEAR_THIS', true).'\','."\n"
                        	   ."\t"."\t".'visual_challenge : \''.JText::_('RSFP_VISUAL_CHALLENGE', true).'\','."\n"
                        	   ."\t"."\t".'audio_challenge : \''.JText::_('RSFP_AUDIO_CHALLENGE', true).'\','."\n"
                        	   ."\t"."\t".'refresh_btn : \''.JText::_('RSFP_REFRESH_BTN', true).'\','."\n"
                        	   ."\t"."\t".'help_btn : \''.JText::_('RSFP_HELP_BTN', true).'\','."\n"
                        	   ."\t"."\t".'incorrect_try_again : \''.JText::_('RSFP_INCORRECT_TRY_AGAIN', true).'\''."\n"
							   ."\t".'}'."\n";
			}
			
			$args['out'] .= '};'."\n";
			$args['out'] .= '</script>';
			$args['out'].= rsform_recaptcha_get_html($publickey, $error, $use_ssl);
		}
	}
	
	/*
		Task Functions
	*/
	
	function rsfp_bk_validate_onSubmitValidateRecaptcha($args)
	{
		if (!$this->canRun()) return;
		
		$this->loadLibrary();
		
		RSFormProHelper::readConfig(true);
		$task = strtolower(JRequest::getVar('task'));
		if ($task == 'ajaxvalidate')
			return true;
		
		// the response from reCAPTCHA
		$resp = null;
		// the error code from reCAPTCHA, if any
		$error = null;
		
		$privatekey = RSFormProHelper::getConfig('recaptcha.private.key');
		$challenge  = JRequest::getVar('recaptcha_challenge_field');
		$response   = JRequest::getVar('recaptcha_response_field');
		
		if ($challenge && $response)
			$resp = rsform_recaptcha_check_answer ($privatekey, isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '', $challenge, $response);
		
		if (!empty($privatekey))
			if (empty($resp->is_valid))
				$args['invalid'][] = $args['data']['componentId'];
	}
	
	/*
		Additional Functions
	*/	
	
	function recaptchaConfigurationScreen()
	{
		if (!$this->canRun()) return;
		
		$lang =& JFactory::getLanguage();
		$lang->load('plg_system_rsfprecaptcha', JPATH_ADMINISTRATOR);
		
		$themes[] = JHTML::_('select.option', 'red', JText::_( 'RSFP_RED_THEME' ) );
		$themes[] = JHTML::_('select.option', 'white', JText::_( 'RSFP_WHITE_THEME' ) );
		$themes[] = JHTML::_('select.option', 'clean', JText::_( 'RSFP_CLEAN_THEME' ) );
		$themes[] = JHTML::_('select.option', 'blackglass', JText::_( 'RSFP_BLACKGLASS_THEME' ) );
		$theme = JHTML::_('select.genericlist', $themes, 'rsformConfig[recaptcha.theme]', 'size="1" class="inputbox"', 'value', 'text', RSFormProHelper::getConfig('recaptcha.theme'));
		?>
		<div id="page-recaptcha">
			<table class="admintable">
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key"><label for="public"><?php echo JText::_( 'RSFP_RECAPTCHA_PBKEY' ); ?></label></td>
					<td><input type="text" name="rsformConfig[recaptcha.public.key]" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('recaptcha.public.key')); ?>" size="100" maxlength="100"></td>
				</tr>
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key"><label for="private"><?php echo JText::_( 'RSFP_RECAPTCHA_PRKEY' ); ?></label></td>
					<td><input type="text" name="rsformConfig[recaptcha.private.key]" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('recaptcha.private.key'));  ?>" size="100" maxlength="100"></td>
				</tr>
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key"><label for="theme"><?php echo JText::_( 'RSFP_RECAPTCHA_THEME' ); ?></label></td>
					<td><?php echo $theme; ?></td>
				</tr>
			</table>
		</div>
		<?php
	}	
}