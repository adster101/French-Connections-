<?php
/**
 * @package			Test
 * @subpackage	Router
 * @copyright		Copyright (C) 2009 Joomla!
 * @author			Amy Stephen
 * @license			GNU General Public License Version 2, or later
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin');

class plgSystemTestrouter extends JPlugin	{
	
/**
 * OnAfterInitialise
 *
 * Parse: for incoming requests - Joomla! has already parsed the incoming URL, looked up the menu item,
 * 	populated variables, but has not yet routed the request. attachBuildRule allows one to override these
 * 	settings and to compensate with information that the core Router does not have. Runs one time per page load.
 *
 * Build: runs one time for each internal Web site URL presented on the Web page. In the same sense,
 * 	Joomla! has already populated variables needed to write the URL to output. In attachBuildRule,
 * 	one can impact these Web links.
 *
 */
  
  public function onAfterRender()
	{
		$app = JFactory::getApplication();

		if ($app->getName() != 'site' || $app->getCfg('sef') == '0')
		{
			return true;
		}

		// Replace src links
		$base   = JURI::base(true).'/';
		$buffer = JResponse::getBody();
   
    
		$buffer = str_replace('/search/','/',JResponse::getBody());

		$this->checkBuffer($buffer);
    
    JResponse::setBody($buffer);
    
		return true;
  }
  
	/**
	 * @param   string  $buffer
	 *
	 * @return  void
	 */
	private function checkBuffer($buffer)
	{
		if ($buffer === null)
		{
			switch (preg_last_error())
			{
				case PREG_BACKTRACK_LIMIT_ERROR:
					$message = "PHP regular expression limit reached (pcre.backtrack_limit)";
					break;
				case PREG_RECURSION_LIMIT_ERROR:
					$message = "PHP regular expression limit reached (pcre.recursion_limit)";
					break;
				case PREG_BAD_UTF8_ERROR:
					$message = "Bad UTF8 passed to PCRE function";
					break;
				default:
					$message = "Unknown PCRE error calling PCRE function";
			}
			throw new RuntimeException($message);
		}
	}
	
}
?>