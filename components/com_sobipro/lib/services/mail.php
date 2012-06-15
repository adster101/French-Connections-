<?php
/**
 * @version: $Id: mail.php 898 2011-03-01 18:29:11Z Radek Suski $
 * @package: SobiPro Library
 * ===================================================
 * @author
 * Name: Sigrid Suski & Radek Suski, Sigsiu.NET GmbH
 * Email: sobi[at]sigsiu.net
 * Url: http://www.Sigsiu.NET
 * ===================================================
 * @copyright Copyright (C) 2006 - 2011 Sigsiu.NET GmbH (http://www.sigsiu.net). All rights reserved.
 * @license see http://www.gnu.org/licenses/lgpl.html GNU/LGPL Version 3.
 * You can use, redistribute this file and/or modify it under the terms of the GNU Lesser General Public License version 3
 * ===================================================
 * $Date: 2011-03-01 19:29:11 +0100 (Tue, 01 Mar 2011) $
 * $Revision: 898 $
 * $Author: Radek Suski $
 * File location: components/com_sobipro/lib/services/mail.php $
 */

defined( 'SOBIPRO' ) || exit( 'Restricted access' );
SPLoader::loadClass( 'cms.base.mail' );

/**
 * @author Radek Suski
 * @version 1.0
 * @created 19-Sep-2010 13:00:06
 */
class SPMail extends SPMailInterface
{
	/**
	 * @param array $recipient Recipient e-mail address
	 * @param string $subject E-mail subject
	 * @param string $body Message body
	 * @param bool $html - HTML mail or plain text
	 * @param array $cc CC e-mail address
	 * @param array $bcc BCC e-mail address
	 * @param string $attachment Attachment file name
	 * @param array $cert - pem certificate
	 * @param array $replyto Reply to email address
	 * @param array $replytoname Reply to name
	 * @param array $from - array( from, fromname )
	 * @return boolean True on success
	 */
	public static function SpSendMail( $recipient, $subject, $body, $html = false, $replyto = null, $cc = null, $bcc = null, $attachment = null, $cert = null, $from = null )
	{
		$from = is_array( $from ) ? $from : array( Sobi::Cfg( 'mail.from' ), Sobi::Cfg( 'mail.fromname' ) );
		$mail = new self();
		$mail->setSender( $from );
		$mail->setSubject( $subject );
		$mail->setBody( $body );
		if ( $html ) {
			$mail->IsHTML( true );
		}
		if( $cert ) {
			$mail->Sign( $cert[ 'certificate' ] , $cert[ 'key' ], $cert[ 'password' ] );
		}
		$mail->addRecipient( $recipient );
		$mail->addCC( $cc );
		$mail->addBCC( $bcc );
		$mail->addAttachment( $attachment );
		$mail->addReplyTo( $replyto );
		return $mail->Send();
	}
}
?>