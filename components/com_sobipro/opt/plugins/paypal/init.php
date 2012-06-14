<?php
/**
 * @version: $Id: init.php 830 2011-02-22 19:58:59Z Radek Suski $
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
 * $Date: 2011-02-22 20:58:59 +0100 (Tue, 22 Feb 2011) $
 * $Revision: 830 $
 * $Author: Radek Suski $
 * File location: components/com_sobipro/opt/plugins/paypal/init.php $
 */
defined( 'SOBIPRO' ) || exit( 'Restricted access' );
/**
 * @author Radek Suski
 * @version 1.0
 * @created 27-Nov-2009 17:10:15
 */
class SPPPaypal extends SPPlugin
{
	/* (non-PHPdoc)
	 * @see Site/lib/plugins/SPPlugin#provide($action)
	 */
	public function provide( $action )
	{
		return
			$action == 'PaymentMethodView' ||
			$action == 'AppPaymentMessageSend'
		;
	}

	public static function admMenu( &$links )
	{
		$links[ Sobi::Txt( 'APP.PAYPAL' ) ] = 'paypal';
	}

	public function AppPaymentMessageSend( &$methods, $entry, &$payment, $html = false )
	{
		return $this->PaymentMethodView( $methods, $entry, $payment, !( $html ) );
	}

	/**
	 * This function have to add own string into the given array
	 * Basically: $methods[ $this->id ] = "Some String To Output";
	 * Optionally the value can be also SobiPro Arr2XML array.
	 * Check the documentation for more information
	 * @param array $methods
	 * @param SPEntry $entry
	 * @param array $payment
	 * @return void
	 */
	public function PaymentMethodView( &$methods, $entry, &$payment, $message = false )
	{
		SPFactory::registry()->loadDBSection( 'paypal' );
		$ppurl = SPLang::replacePlaceHolders( Sobi::Reg( 'paypal.ppurl.value' ), $entry );
		$ppemail = SPLang::replacePlaceHolders( Sobi::Reg( 'paypal.ppemail.value' ), $entry );
		$pprurl = SPLang::replacePlaceHolders( Sobi::Reg( 'paypal.pprurl.value' ), $entry );
		$ppcc = SPLang::replacePlaceHolders( Sobi::Reg( 'paypal.ppcc.value' ), $entry );
		$cfg = SPLoader::loadIniFile( 'etc.paypal' );
		$rp = $cfg[ 'general' ][ 'replace' ];
		$to = ( $cfg[ 'general' ][ 'replace' ] == ',' ) ? '.' : ',';
		$amount = str_replace( $rp, $to, $payment[ 'summary' ][ 'sum_brutto' ] );

		$values = array(
			'entry' => $entry,
			'amount' => preg_replace( '/[^0-9\.,]/', null, $amount ),
			'ppurl' => $ppurl,
			'ppemail' => $ppemail,
			'pprurl' => $pprurl,
			'ppcc' => $ppcc,
		);

		$expl = SPLang::replacePlaceHolders( SPLang::getValue( 'ppexpl', 'plugin' ), $values );
		$subject = SPLang::replacePlaceHolders( SPLang::getValue( 'ppsubject', 'plugin' ), $values );
		$values[ 'expl' ] = $expl;
		$values[ 'subject' ] = $subject;
		$methods[ $this->id ] = array (
			'content' => ( $message ? $this->raw( $cfg, $values ) : $this->content( $cfg, $values ) ),
			'title' => Sobi::Txt( 'APP.PPP.PAY_TITLE' )
		);
	}

	/**
	 * @param array $config
	 * @param array $values
	 * @return string
	 */
	private function raw( $config, $values )
	{
		$out = "\n";
		$out .= $values[ 'expl' ];
		$out .= Sobi::Txt( 'APP.PPP.PAY_TITLE' ).': ';
		$out .= $config[ 'message' ][ 'url' ];
		array_shift( $config[ 'message' ] );
		$v = array();
		foreach ( $config[ 'message' ] as $field => $value ) {
			$v[] = $field.'='.urlencode( SPLang::replacePlaceHolders( $value, $values ) );
		}
		$out .= implode( '&', $v );
		return $out;
	}

	/**
	 * @param array $config
	 * @param array $values
	 * @return string
	 */
	private function content( $config, $values )
	{
		$out = "\n";
		$out .= $values[ 'expl' ];
		$out .= "\n";
		$out .= '<form action="'.$values[ 'ppurl' ].'" method="post">'."\n";
		foreach ( $config[ 'fields' ] as $field => $value ) {
			$out .= '<input name="'.$field.'" value="'.htmlentities( SPLang::replacePlaceHolders( $value, $values ) ).'" type="hidden"/>'."\n";
		}
		$img = SPLang::replacePlaceHolders( $config[ 'general' ][ 'image' ] );
		$out .= '<input src="'.$img.'" name="submit" alt="" type="image"/>'."\n";
		$out .= '</form>'."\n";
		return $out;
	}
}
?>