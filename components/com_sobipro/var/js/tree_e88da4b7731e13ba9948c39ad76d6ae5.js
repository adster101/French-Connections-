 /**
 * @version: $Id: tree.js 904 2011-03-02 20:15:56Z Radek Suski $
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
 * $Date: 2011-03-02 21:15:56 +0100 (Wed, 02 Mar 2011) $
 * $Revision: 904 $
 * $Author: Radek Suski $
 * File location: components/com_sobipro/lib/js/tree.js $
 */
 // Created at Thu Jun 14 13:07:00 CEST 2012 by Sobi Pro Component

var menuTree_stmcid = 0;
var menuTree_stmLastNode = 7;
var menuTree_stmImgs = new Array();
var menuTree_stmImgMatrix = new Array();
var menuTree_stmParents = new Array();
var menuTree_stmSemaphor = 0;
var menuTree_stmPid = 0;
var menuTree_stmWait = 'http://localhost/media/sobipro/styles/spinner.gif';

menuTree_stmImgs[ 'root' ] = 'http://localhost/media/sobipro/tree/base.gif';
menuTree_stmImgs[ 'join' ] = 'http://localhost/media/sobipro/tree/join.gif';
menuTree_stmImgs[ 'joinBottom' ] = 'http://localhost/media/sobipro/tree/joinbottom.gif';
menuTree_stmImgs[ 'plus' ] = 'http://localhost/media/sobipro/tree/plus.gif';
menuTree_stmImgs[ 'plusBottom' ] = 'http://localhost/media/sobipro/tree/plusbottom.gif';
menuTree_stmImgs[ 'minus' ] = 'http://localhost/media/sobipro/tree/minus.gif';
menuTree_stmImgs[ 'minusBottom' ] = 'http://localhost/media/sobipro/tree/minusbottom.gif';
menuTree_stmImgs[ 'folder' ] = 'http://localhost/media/sobipro/tree/folder.gif';
menuTree_stmImgs[ 'disabled' ] = 'http://localhost/media/sobipro/tree/disabled.gif';
menuTree_stmImgs[ 'folderOpen' ] = 'http://localhost/media/sobipro/tree/folderopen.gif';
menuTree_stmImgs[ 'line' ] = 'http://localhost/media/sobipro/tree/line.gif';
menuTree_stmImgs[ 'empty' ] = 'http://localhost/media/sobipro/tree/empty.gif';;

menuTree_stmImgMatrix[ 2 ] = new Array( 'plus' );
menuTree_stmImgMatrix[ 3 ] = new Array( 'plus' );
menuTree_stmImgMatrix[ 4 ] = new Array( 'plus' );
menuTree_stmImgMatrix[ 5 ] = new Array( 'plus' );
menuTree_stmImgMatrix[ 6 ] = new Array( 'plus' );
menuTree_stmImgMatrix[ 7 ] = new Array( 'plusBottom' );;
//__PARENT_ARR__

function menuTree_stmExpand( catid, deep, pid ) 
{
	try { SP_id( "menuTree_imgFolder" + catid ).src = menuTree_stmWait; } catch( e ) {}	
	menuTree_stmcid = catid;
	menuTree_stmPid = pid;
	url = "index.php?option=com_sobipro&task=category.expand&sid=1&out=xml&expand=" + menuTree_stmcid + "&pid=" + menuTree_stmPid + "&tmpl=component&format=raw";
	menuTree_stmMakeRequest( url, deep, catid );	
}

function menuTree_stmCatData( node, val )
{
	return node.getElementsByTagName( val ).item( 0 ).firstChild.data;
}

function menuTree_stmAddSubcats( XMLDoc, deep, ccatid ) 
{
	var categories = XMLDoc.getElementsByTagName( 'category' );
	var subcats = "";
	deep++;
	for( i = 0; i < categories.length; i++ ) {
		var category 	= categories[ i ];
		var catid 		= menuTree_stmCatData( category, 'catid' );
		var name 		= menuTree_stmCatData( category, 'name' );
		var introtext 	= menuTree_stmCatData( category, 'introtext' );
		var parentid 	= menuTree_stmCatData( category, 'parentid' );
		var url 		= menuTree_stmCatData( category, 'url' );
		var childs 		= menuTree_stmCatData( category, 'childs' );
		var join 		= "<img src='" + menuTree_stmImgs['join'] + "' alt=''/>";
		var margin 		= "";
		var childContainer = "";		
		name 			= name.replace( "\\", "" );
		introtext 		= introtext.replace( "\\", "" );
		url 			= url.replace( "\\\\", "" );
		
		for( j = 0; j < deep; j++ ) {
			if( menuTree_stmImgMatrix[ parentid ][ j ] ) {
				switch( menuTree_stmImgMatrix[ parentid ][ j ] ) 
				{
					case 'plus':
					case 'minus':
					case 'line':
						image = 'line';
						break;
					default:
						image = 'empty';
						break;
				}
			}
			else {
				image = 'empty';
			}
			if( !menuTree_stmImgMatrix[ catid ] ) {
				catArray = new Array();
				catArray[ j ]  = image;
				menuTree_stmImgMatrix[ catid ] = catArray;
			}
			else {
				menuTree_stmImgMatrix[ catid ][ j ] = image;
			}
			margin = margin + "<img src='"+ menuTree_stmImgs[ image ] +"' style='border-style:none;' alt=''/>";
		}
		if( childs > 0 ) {
			join = "<a href='javascript:menuTree_stmExpand( " + catid + ", " + deep + ", " + menuTree_stmPid + " );' id='menuTree_imgUrlExpand" + catid + "'><img src='"+ menuTree_stmImgs['plus'] + "' id='menuTree_imgExpand" + catid + "'  style='border-style:none;' alt='expand'/></a>";
			menuTree_stmImgMatrix[catid][j] = 'plus';
		}
		if( menuTree_stmcid == menuTree_stmLastNode ) {
			line = "<img src='"+menuTree_stmImgs['empty']+"' alt=''>";
		}
		if( i == categories.length - 1 ) {
			if( childs > 0 ) {
				join = "<a href='javascript:menuTree_stmExpand( " + catid + ", " + deep + ", " + menuTree_stmPid + " );' id='menuTree_imgUrlExpand" + catid + "'><img src='"+ menuTree_stmImgs[ 'plusBottom' ] + "' id='menuTree_imgExpand" + catid + "'  style='border-style:none;' alt='expand'/></a>";
				menuTree_stmImgMatrix[ catid ][ j ] = 'plusBottom';
			}
			else {
				join = "<img src='" + menuTree_stmImgs[ 'joinBottom' ] + "' style='border-style:none;' alt=''/>";
				menuTree_stmImgMatrix[ catid ][ j ] = 'joinBottom';
			}
		}
		subcats = subcats + "<div class='sigsiuTreeNode' id='menuTreestNode" + catid + "'>" + margin  + join + "<a id='menuTree" + catid + "' href=\"" + url + "\"><img src='" + menuTree_stmImgs[ 'folder' ] + "' id='menuTree_imgFolder" + catid + "' alt=''></a><a class = 'treeNode' id='menuTree_CatUrl" + catid + "' href=\"" + url + "\">" + name + "</a></div>";
		if( childs > 0 ) {
			subcats = subcats + "<div class='clip' id='menuTree_childsContainer" + catid + "' style='display: block;  display:none;'></div>"
		}
	}
	var childsCont = "menuTree_childsContainer" + ccatid;
	SP_id( childsCont ).innerHTML = subcats;
}

function menuTree_stmMakeRequest( url, deep, catid ) 
{
	var menuTree_stmHttpRequest;
    if ( window.XMLHttpRequest ) {
        menuTree_stmHttpRequest = new XMLHttpRequest();
        if ( menuTree_stmHttpRequest.overrideMimeType ) {
            menuTree_stmHttpRequest.overrideMimeType( 'text/xml' );
        }
    }
    else if ( window.ActiveXObject ) {
        try { menuTree_stmHttpRequest = new ActiveXObject( "Msxml2.XMLHTTP" ); }
        catch ( e ) { try { menuTree_stmHttpRequest = new ActiveXObject("Microsoft.XMLHTTP"); } catch (e) {} }
    }
    if ( !menuTree_stmHttpRequest ) {
//        alert( 'AJAX_FAIL' );
        return false;
    }
    menuTree_stmHttpRequest.onreadystatechange = function() { menuTree_stmGetSubcats( menuTree_stmHttpRequest,deep,catid ); };
    menuTree_stmHttpRequest.open( 'GET', url, true );
    menuTree_stmHttpRequest.send( null );
}
function menuTree_stmGetSubcats( menuTree_stmHttpRequest, deep, catid ) 
{
	if ( menuTree_stmHttpRequest.readyState == 4 ) {
		if ( menuTree_stmHttpRequest.status == 200 ) {
			if( SP_id( "menuTree_imgFolder" + catid )  == undefined ) {
				window.setTimeout( function() { menuTree_stmGetSubcats( menuTree_stmHttpRequest, deep, catid ); } , 200 );
			}
			else {
				SP_id( "menuTree_imgFolder" + catid ).src = menuTree_stmImgs[ 'folderOpen' ];
	        	 if ( menuTree_stmcid == menuTree_stmLastNode ) {
	        	 	SP_id( "menuTree_imgExpand" + catid ).src = menuTree_stmImgs[ 'minusBottom' ];
	        	 }
	        	 else {
	        		 if( SP_id( "menuTree_imgExpand" + catid ).src == menuTree_stmImgs[ 'plusBottom' ] ) {
	        			 SP_id( "menuTree_imgExpand" + catid ).src = menuTree_stmImgs[ 'minusBottom' ];
	        		 }
	        		 else {
	        			 SP_id( "menuTree_imgExpand" + catid ).src = menuTree_stmImgs[ 'minus' ];
	        		 }
	        	 }
	        	 SP_id( "menuTree_imgUrlExpand" + catid ).href = "javascript:menuTree_stmColapse( " + catid + ", " + deep + " );";
	        	 SP_id( "menuTree_childsContainer" + catid ).style.display = "";
	        	 menuTree_stmAddSubcats( menuTree_stmHttpRequest.responseXML, deep, catid );
			}
        }
        else {
//            SobiPro.Alert( 'AJAX_FAIL' );
        }
    }
}
function menuTree_stmColapse( id, deep ) 
{
	SP_id( "menuTree_childsContainer" + id ).style.display = "none";
	SP_id( "menuTree_imgFolder" + id ).src = menuTree_stmImgs[ 'folder' ];
	if( id == menuTree_stmLastNode ) {
		SP_id( "menuTree_imgExpand" + id ).src = menuTree_stmImgs[ 'plusBottom' ];
	}
   	else if(SP_id( "menuTree_imgExpand" + menuTree_stmcid ).src == menuTree_stmImgs[ 'minusBottom' ] ){
	 	SP_id( "menuTree_imgExpand" + menuTree_stmcid ).src = menuTree_stmImgs[ 'plusBottom' ];
	}
	else {
		SP_id( "menuTree_imgExpand" + id ).src = menuTree_stmImgs[ 'plus' ];
	}
	SP_id( "menuTree_imgUrlExpand" + id ).href = "javascript:menuTree_stmExpand( " + id + ", " + deep + ", " + menuTree_stmPid + " );";
}