<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Captcha
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Recaptcha Plugin.
 * Based on the official recaptcha library( https://developers.google.com/recaptcha/docs/php )
 *
 * @package     Joomla.Plugin
 * @subpackage  Captcha
 * @since       2.5
 */
class PlgCaptchaRecaptcha_fc extends JPlugin {

  const RECAPTCHA_API_SERVER = "http://www.google.com/recaptcha/api";
  const RECAPTCHA_API_SECURE_SERVER = "https://www.google.com/recaptcha/api";
  const RECAPTCHA_VERIFY_SERVER = "www.google.com";

  /**
   * Load the language file on instantiation.
   *
   * @var    boolean
   * @since  3.1
   */
  protected $autoloadLanguage = true;

  /**
   * Initialise the captcha
   *
   * @param   string  $id  The id of the field.
   *
   * @return  Boolean	True on success, false otherwise
   *
   * @since  2.5
   */
  public function onInit($id) {
    $document = JFactory::getDocument();
    $app = JFactory::getApplication();

    $lang = $this->_getLanguage();
    $pubkey = $this->params->get('public_key', '');
    $theme = $this->params->get('theme', 'clean');

    if ($pubkey == null || $pubkey == '') {
      throw new Exception(JText::_('PLG_RECAPTCHA_FC_ERROR_NO_PUBLIC_KEY'));
    }

    $server = self::RECAPTCHA_API_SERVER;

    if ($app->isSSLConnection()) {
      $server = self::RECAPTCHA_API_SECURE_SERVER;
    }

    $document->addScriptDeclaration('var RecaptchaOptions = {
      theme: "' . $theme . '",
      custom_theme_widget: "recaptcha_widget"
    }');


    return true;
  }

  /**
   * Gets the challenge HTML
   *
   * @param   string  $name   The name of the field.
   * @param   string  $id     The id of the field.
   * @param   string  $class  The class of the field.
   *
   * @return  string  The HTML to be embedded in the form.
   *
   * @since  2.5
   */
  public function onDisplay($name, $id, $class) {
    $pubkey = $this->params->get('public_key', '');

    return '
      <div id="recaptcha_widget" style="display:none">
      <div class="panel panel-default">
      <div class="panel-body">
      <div class="row-fluid">
      <div class="span9">
<div id="recaptcha_image"></div>
</div>
<div class="offset1 span2">
    <a class="btn btn-small" href="javascript:Recaptcha.reload()" data-toggle="tooltip" title="Get another CAPTCHA">
            <i class="icon icon-loop">&nbsp;</i>
          </a>
          
          <a title="Get an audio CAPTCHA" data-toggle="tooltip" class="recaptcha_only_if_image btn btn-small" href="javascript:Recaptcha.switch_type(\'audio\')">
            <i class="icon icon-music">&nbsp;</i>      
          </a>
          
          <a title="Get an image CAPTCHA" data-toggle="tooltip" class="recaptcha_only_if_audio btn btn-small" href="javascript:Recaptcha.switch_type(\'image\')">
            <i class="icon icon-picture">&nbsp;</i>      
          </a>

          <a href="javascript:Recaptcha.showhelp()">
            Help
          </a>
          </div>
          </div>
        <hr />
        <div class="recaptcha_only_if_incorrect_sol" style="color:red">
          Incorrect please try again
        </div>
        <!--<label class="recaptcha_only_if_image">Enter the words above:</label>
        <label class="recaptcha_only_if_audio">Enter the numbers you hear:</label><br />-->
        
        <input type="text" id="recaptcha_response_field" name="recaptcha_response_field" placeholder="Type the text" />
        <img id="recaptcha_logo" alt="" width="71" height="36" src="http://www.google.com/recaptcha/api/img/clean/logo.png" class="pull-right">
      
    <script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k=" ' . $pubkey . ' ""></script>
    <noscript>
      <iframe 
        src="http://www.google.com/recaptcha/api/noscript?k="' .$pubkey . '"" 
        height="300" 
        width="500" 
        frameborder="0">
      </iframe><br>
      <textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
      <input type="hidden" name="recaptcha_response_field" value="manual_challenge">
    </noscript>
    </div>
    </div>';
  }

  /**
   * Calls an HTTP POST function to verify if the user's guess was correct
   *
   * @return  True if the answer is correct, false otherwise
   *
   * @since  2.5
   */
  public function onCheckAnswer($code) {
    $input = JFactory::getApplication()->input;
    $privatekey = $this->params->get('private_key');
    $remoteip = $input->server->get('REMOTE_ADDR', '', 'string');
    $challenge = $input->get('recaptcha_challenge_field', '', 'string');
    $response = $input->get('recaptcha_response_field', '', 'string');

    // Check for Private Key
    if (empty($privatekey)) {
      $this->_subject->setError(JText::_('PLG_RECAPTCHA_FC_ERROR_NO_PRIVATE_KEY'));

      return false;
    }

    // Check for IP
    if (empty($remoteip)) {
      $this->_subject->setError(JText::_('PLG_RECAPTCHA_FC_ERROR_NO_IP'));

      return false;
    }

    // Discard spam submissions
    if ($challenge == null || strlen($challenge) == 0 || $response == null || strlen($response) == 0) {
      $this->_subject->setError(JText::_('PLG_RECAPTCHA_FC_ERROR_EMPTY_SOLUTION'));

      return false;
    }

    $response = $this->_recaptcha_http_post(
            self::RECAPTCHA_VERIFY_SERVER, "/recaptcha/api/verify", array(
        'privatekey' => $privatekey,
        'remoteip' => $remoteip,
        'challenge' => $challenge,
        'response' => $response
            )
    );

    $answers = explode("\n", $response[1]);

    if (trim($answers[0]) == 'true') {
      return true;
    } else {
      // @todo use exceptions here
      $this->_subject->setError(JText::_('PLG_RECAPTCHA_FC_ERROR_' . strtoupper(str_replace('-', '_', $answers[1]))));

      return false;
    }
  }

  /**
   * Encodes the given data into a query string format.
   *
   * @param   array  $data  Array of string elements to be encoded
   *
   * @return  string  Encoded request
   *
   * @since  2.5
   */
  private function _recaptcha_qsencode($data) {
    $req = "";

    foreach ($data as $key => $value) {
      $req .= $key . '=' . urlencode(stripslashes($value)) . '&';
    }

    // Cut the last '&'
    $req = rtrim($req, '&');

    return $req;
  }

  /**
   * Submits an HTTP POST to a reCAPTCHA server.
   *
   * @param   string  $host
   * @param   string  $path
   * @param   array   $data
   * @param   int     $port
   *
   * @return  array   Response
   *
   * @since  2.5
   */
  private function _recaptcha_http_post($host, $path, $data, $port = 80) {
    $req = $this->_recaptcha_qsencode($data);

    $http_request = "POST $path HTTP/1.0\r\n";
    $http_request .= "Host: $host\r\n";
    $http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
    $http_request .= "Content-Length: " . strlen($req) . "\r\n";
    $http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
    $http_request .= "\r\n";
    $http_request .= $req;

    $response = '';

    if (($fs = @fsockopen($host, $port, $errno, $errstr, 10)) == false) {
      die('Could not open socket');
    }

    fwrite($fs, $http_request);

    while (!feof($fs)) {
      // One TCP-IP packet
      $response .= fgets($fs, 1160);
    }

    fclose($fs);
    $response = explode("\r\n\r\n", $response, 2);

    return $response;
  }

  /**
   * Get the language tag or a custom translation
   *
   * @return  string
   *
   * @since  2.5
   */
  private function _getLanguage() {
    $language = JFactory::getLanguage();

    $tag = explode('-', $language->getTag());
    $tag = $tag[0];
    $available = array('en', 'pt', 'fr', 'de', 'nl', 'ru', 'es', 'tr');

    if (in_array($tag, $available)) {
      return "lang : '" . $tag . "',";
    }

    // If the default language is not available, let's search for a custom translation
    if ($language->hasKey('PLG_RECAPTCHA_FC_CUSTOM_LANG')) {
      $custom[] = 'custom_translations : {';
      $custom[] = "\t" . 'instructions_visual : "' . JText::_('PLG_RECAPTCHA_FC_INSTRUCTIONS_VISUAL') . '",';
      $custom[] = "\t" . 'instructions_audio : "' . JText::_('PLG_RECAPTCHA_FC_INSTRUCTIONS_AUDIO') . '",';
      $custom[] = "\t" . 'play_again : "' . JText::_('PLG_RECAPTCHA_FC_PLAY_AGAIN') . '",';
      $custom[] = "\t" . 'cant_hear_this : "' . JText::_('PLG_RECAPTCHA_FC_CANT_HEAR_THIS') . '",';
      $custom[] = "\t" . 'visual_challenge : "' . JText::_('PLG_RECAPTCHA_FC_VISUAL_CHALLENGE') . '",';
      $custom[] = "\t" . 'audio_challenge : "' . JText::_('PLG_RECAPTCHA_FC_AUDIO_CHALLENGE') . '",';
      $custom[] = "\t" . 'refresh_btn : "' . JText::_('PLG_RECAPTCHA_FC_REFRESH_BTN') . '",';
      $custom[] = "\t" . 'help_btn : "' . JText::_('PLG_RECAPTCHA_FC_HELP_BTN') . '",';
      $custom[] = "\t" . 'incorrect_try_again : "' . JText::_('PLG_RECAPTCHA_FC_INCORRECT_TRY_AGAIN') . '",';
      $custom[] = '},';
      $custom[] = "lang : '" . $tag . "',";

      return implode("\n", $custom);
    }

    // If nothing helps fall back to english
    return '';
  }

}
