<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_search
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$document = JFactory::getDocument();
$app = JFactory::getApplication();
$lang = $app->input->get('lang', 'en');

$bedrooms = '';
$occupancy = '';
$arrival = '';
$departure = '';


// The following are coordinates that trace out the outline shape of a region. They should really be stored in the classifications table
$area_map[146] = "106,118,105,119,104,119,103,120,102,119,102,120,102,121,101,122,101,124,102,126,102,126,103,129,103,130,103,131,103,132,104,133,106,132,106,133,105,135,107,136,106,137,106,138,104,139,104,140,104,140,103,140,102,140,102,140,102,142,100,142,97,144,96,143,96,144,96,145,96,146,96,147,95,148,94,147,92,148,91,147,90,148,89,148,89,148,87,147,86,148,85,148,85,148,85,149,83,149,83,151,83,152,84,153,86,153,86,153,86,154,87,155,87,156,86,157,87,157,87,158,85,159,87,161,88,160,90,161,89,162,87,162,86,163,85,164,83,164,83,165,84,165,85,166,86,166,87,165,87,167,87,168,88,168,89,167,90,167,91,166,93,167,94,168,96,168,96,167,97,166,98,167,100,165,101,165,103,167,103,165,102,164,101,163,102,157,101,156,102,156,102,155,101,154,101,152,102,153,107,150,107,150,109,149,108,148,110,146,110,147,113,146,113,145,115,144,115,144,116,144,117,145,117,146,118,146,118,146,119,145,120,144,121,142,122,141,123,141,124,138,126,136,125,135,124,134,124,132,121,129,121,131,120,130,119,130,118,131,116,130,115,130,115,129,114,128,114,127,113,124,112,122,110,120,109,120,109,121,108,121,107,121,106,119,106,118,106,118";
$area_map[150] = "82,117,80,117,79,118,80,119,78,122,78,123,77,123,76,124,75,126,74,127,74,129,72,130,72,130,73,131,72,133,72,134,71,134,69,136,68,135,63,137,62,136,62,137,61,136,61,137,61,138,59,137,59,137,58,137,57,137,57,138,57,139,57,140,56,140,57,141,56,143,56,143,56,144,58,144,59,146,58,147,59,147,60,148,59,149,58,150,59,150,58,152,57,152,56,153,57,154,56,154,55,155,55,156,54,157,54,158,56,158,58,161,59,161,61,161,61,160,63,161,63,161,64,161,65,161,68,161,68,160,68,159,68,158,69,158,71,158,72,159,74,159,76,161,78,161,79,163,80,162,80,162,82,163,83,163,83,164,86,163,87,162,89,162,89,161,88,161,87,161,86,160,85,159,85,159,87,158,87,158,86,157,87,156,87,155,86,155,86,153,85,153,84,153,83,151,83,149,84,149,85,147,85,147,86,148,88,147,89,148,89,148,90,148,91,147,92,147,94,147,95,148,96,147,96,146,96,145,96,144,96,143,98,144,100,142,102,142,102,140,103,140,104,140,104,139,105,139,106,138,105,137,107,136,105,135,106,133,105,133,104,133,104,132,102,132,103,129,102,126,102,126,101,124,100,122,99,120,98,121,98,119,97,119,95,122,94,124,93,125,91,124,90,125,90,125,89,122,89,122,88,120,89,119,88,118,84,119,82,117";
$area_map[157] = "112,93,111,94,112,98,110,99,110,99,111,101,110,102,111,103,112,105,113,107,114,108,114,108,114,109,112,110,113,110,114,110,115,111,115,110,116,109,117,110,118,110,119,110,119,110,119,112,120,112,120,112,121,113,120,113,120,115,120,115,120,115,119,116,119,116,118,117,118,118,117,118,117,119,116,120,115,120,114,121,114,121,113,122,112,122,113,125,114,127,114,127,115,128,115,130,116,130,118,131,119,130,120,130,120,130,120,131,121,129,123,131,124,130,125,130,126,131,126,132,129,130,131,130,130,131,131,131,132,131,133,132,134,132,134,132,135,133,136,133,136,132,137,132,137,133,138,131,137,130,137,129,134,129,134,128,134,127,134,126,136,127,137,126,136,125,137,124,137,123,138,123,139,122,139,122,139,121,140,121,140,121,141,120,141,120,145,119,146,119,146,117,144,116,144,113,145,113,146,114,146,114,147,115,148,114,148,114,150,113,153,113,155,111,155,111,156,110,156,109,156,107,154,105,153,104,154,103,153,103,151,102,152,101,153,100,154,99,154,98,155,96,154,96,152,95,152,94,150,94,151,93,151,92,151,91,150,90,150,89,147,89,145,90,144,90,143,92,144,92,142,94,141,94,141,95,139,94,139,93,141,92,142,90,141,89,138,92,136,92,136,91,133,92,133,91,132,92,131,89,129,88,127,88,127,88,126,89,124,95,123,93,123,92,122,93,120,93,120,92,119,93,119,94,117,95,117,95,115,95,114,95,112,94,112,93";
$area_map[144] = "153,67,153,66,152,66,152,65,152,64,151,63,149,62,146,60,145,61,143,59,142,60,140,60,140,59,139,58,138,60,138,60,137,60,136,62,136,62,135,63,135,64,134,65,133,65,132,65,131,66,131,66,132,66,133,68,131,69,132,72,133,73,133,73,133,74,132,77,130,77,131,78,130,79,133,81,131,82,132,83,132,84,133,85,132,87,132,88,132,88,132,88,131,89,131,90,132,90,132,91,132,91,133,92,135,92,135,91,136,91,136,92,138,92,141,89,141,88,142,87,141,86,142,85,142,84,142,84,145,82,145,81,145,80,145,79,145,78,148,78,148,76,149,75,150,75,150,74,151,73,151,72,152,72,153,71,152,70,151,70,150,70,152,68,151,68,152,67,153,67";
$area_map[139] = "101,83,99,84,98,85,96,84,95,86,95,87,95,88,92,89,91,90,91,91,92,92,92,92,93,93,93,93,95,96,95,97,95,98,95,99,95,100,95,100,93,102,94,104,95,105,94,105,94,106,94,107,94,110,92,110,92,111,90,113,90,114,89,117,89,118,88,118,89,119,88,120,90,122,89,123,90,125,90,124,92,124,92,124,93,124,95,123,95,122,97,119,98,119,98,120,99,120,99,121,100,122,101,124,101,122,102,120,102,119,102,119,103,120,104,119,105,118,105,118,106,118,106,119,107,121,108,121,109,121,109,119,110,120,110,120,112,122,113,121,114,120,115,120,115,119,115,120,117,119,117,118,118,118,118,117,119,116,119,116,119,115,119,115,120,115,120,113,121,113,120,112,119,112,119,112,119,111,118,110,118,110,117,110,116,110,116,110,115,110,114,111,114,110,113,111,112,110,112,110,113,109,113,108,114,108,113,106,112,105,110,102,110,102,110,101,110,101,110,100,110,99,110,99,111,98,111,98,111,94,111,94,112,93,113,91,113,91,114,90,111,88,110,88,110,88,110,87,109,84,108,84,108,85,106,86,106,85,105,85,104,85,103,85,101,84,101,83";
$area_map[156] = "149,114,148,114,148,114,146,115,145,114,144,114,144,116,145,117,146,117,146,119,145,119,141,120,141,120,140,121,139,121,139,122,139,122,139,123,137,123,137,124,136,125,137,126,136,127,135,126,134,127,135,128,134,128,135,129,137,129,138,131,138,132,137,133,137,132,136,132,136,133,135,133,134,133,133,132,133,132,132,131,131,132,130,131,130,131,131,130,129,130,126,132,126,131,125,130,124,130,123,131,124,132,124,134,124,134,125,135,126,136,124,138,123,138,123,139,123,141,122,142,122,142,121,143,121,143,119,145,119,146,119,146,121,147,121,146,120,146,121,145,121,145,122,144,123,144,124,145,123,145,124,146,123,147,124,148,124,147,126,148,126,148,126,147,126,147,127,147,128,147,129,147,129,146,128,146,128,146,128,146,128,144,130,145,130,145,130,145,131,146,132,145,132,146,131,147,129,147,129,148,129,149,132,148,132,148,133,148,134,149,134,150,136,151,136,150,136,151,137,151,138,151,138,151,138,151,140,152,140,153,141,153,143,152,143,153,145,152,145,152,146,152,147,151,147,152,148,151,150,151,150,150,151,151,152,150,152,149,152,149,151,149,151,149,151,148,151,148,152,147,153,147,153,146,153,145,155,146,155,144,155,143,156,143,156,143,158,141,163,138,162,137,165,134,165,133,165,132,165,131,163,131,162,132,161,132,160,131,159,132,159,131,156,131,155,129,154,127,154,126,153,125,153,124,154,123,154,123,154,122,156,121,155,120,155,120,154,119,154,119,152,118,151,117,151,115,150,115,149,114";
$area_map[143] = "165,171,164,167,167,163,167,159,166,158,167,157,166,156,166,155,165,152,166,150,165,149,165,146,165,146,164,146,164,147,163,148,164,149,163,149,164,150,164,151,163,152,162,151,160,151,160,152,160,153,159,153,157,153,157,154,156,154,156,155,155,154,154,156,154,157,153,157,153,158,152,158,152,159,153,159,153,159,154,160,153,161,153,163,154,163,155,164,154,165,154,166,153,166,153,167,155,167,156,167,155,169,154,170,154,170,156,170,156,171,158,171,157,172,156,172,156,174,157,175,161,176,160,177,162,178,163,176,162,176,163,175,163,174,164,173,163,173,165,172,164,171,165,171";
$area_map[138] = "46,106,45,109,45,113,45,112,46,113,46,115,45,116,44,114,44,119,43,120,43,123,45,121,46,123,45,124,44,123,43,126,43,129,39,142,38,145,36,147,35,147,35,148,36,148,37,148,37,150,38,149,40,150,40,152,39,153,39,154,41,152,42,154,44,154,45,154,46,155,49,155,49,156,50,157,50,157,51,159,52,158,53,159,54,159,54,158,54,156,55,156,55,155,55,154,56,154,56,153,57,152,58,152,59,150,58,149,59,149,59,148,59,147,58,147,58,146,59,146,58,144,56,144,55,143,56,143,56,141,57,141,56,140,57,139,56,138,58,137,58,137,59,137,59,137,60,138,60,138,61,136,62,136,63,136,68,135,69,136,70,135,71,134,72,134,72,133,73,132,71,130,72,129,73,130,75,130,74,127,75,125,76,124,78,123,78,122,80,119,79,118,80,117,79,116,79,115,77,115,77,114,77,113,77,112,78,111,77,110,75,108,76,108,75,107,74,106,72,106,71,107,70,106,71,105,69,104,67,106,68,106,66,108,64,109,64,110,64,112,62,113,61,113,60,115,59,115,58,116,55,114,55,114,54,112,52,111,51,111,52,114,51,114,52,116,54,117,53,118,52,117,51,116,51,114,50,113,50,111,48,109,46,107,46,106";
$area_map[155] = "51,110,50,108,49,107,47,105,44,103,44,102,46,102,46,100,47,99,46,98,47,97,47,96,46,96,46,95,45,95,45,95,45,94,46,93,46,92,47,91,49,91,49,92,50,91,52,92,54,91,52,90,53,89,52,88,53,87,52,82,51,82,51,81,51,80,50,79,53,79,54,78,55,77,55,78,56,77,59,77,60,78,60,77,61,77,61,76,62,76,62,77,64,77,64,78,65,78,65,79,65,80,65,80,66,80,68,80,69,80,69,79,70,80,70,81,73,85,73,86,73,87,74,88,75,88,77,91,76,92,74,92,74,93,73,93,71,94,72,96,71,97,72,98,73,98,73,99,72,100,71,100,71,102,70,102,67,105,67,106,66,108,64,109,63,110,64,112,62,113,61,113,60,114,60,114,60,115,58,115,58,116,55,114,55,114,55,112,53,111,53,111,52,110,52,111,51,110";
$area_map[153] = "49,50,48,51,49,52,48,54,49,59,48,60,46,63,43,62,39,65,38,65,36,65,34,66,35,68,33,69,32,68,32,69,30,69,30,70,29,70,29,73,31,72,32,73,33,72,34,72,35,72,35,72,37,72,39,74,40,74,38,74,36,73,35,73,33,73,33,75,32,75,34,76,36,77,35,78,35,80,34,79,33,80,33,81,34,82,36,85,37,88,40,90,41,90,42,91,43,91,45,92,45,91,46,92,47,91,49,91,49,91,50,91,52,92,54,91,52,90,53,89,52,88,53,87,52,84,52,82,51,82,50,81,51,80,49,79,53,79,54,78,54,78,55,77,55,78,56,77,58,77,59,77,60,77,60,77,61,77,61,76,62,76,62,73,64,69,64,68,64,67,66,68,67,67,69,66,69,65,70,65,71,63,71,62,72,62,72,59,72,59,73,58,73,57,72,56,72,57,70,55,70,55,67,54,67,52,65,51,62,52,62,52,61,52,61,51,60,51,60,50,60,50,59,49,58,50,56,50,54,51,53,51,52,51,50,50,49,50";
$area_map[148] = "81,42,82,44,82,45,82,46,82,47,85,50,85,50,85,52,86,51,87,53,87,54,87,55,90,54,90,55,92,54,94,56,93,58,95,57,96,58,97,57,98,58,100,57,101,59,101,60,100,62,100,64,98,65,98,65,99,67,99,68,100,68,98,69,99,71,98,73,100,75,101,80,101,83,98,84,98,84,96,84,95,86,95,87,95,88,92,89,91,90,85,90,83,91,81,90,79,92,78,91,78,91,76,91,76,90,75,88,74,88,73,86,73,85,71,81,71,80,68,79,69,80,68,80,66,80,65,80,65,79,64,78,64,78,64,76,62,76,63,73,64,70,64,69,65,67,67,68,66,67,69,66,69,65,70,65,72,63,71,62,72,62,73,61,72,59,73,58,74,57,73,56,72,54,73,53,74,52,74,49,73,48,73,47,73,46,75,46,77,45,79,45,79,44,80,43,80,42";
$area_map[147] = "84,90,91,91,91,92,92,92,94,93,95,96,95,97,95,98,95,99,93,102,94,104,94,105,94,106,94,106,94,107,94,110,92,110,92,111,90,113,90,114,89,116,89,118,88,118,84,119,83,117,80,117,79,116,79,115,78,115,77,113,77,113,77,112,78,111,78,109,76,108,76,108,75,107,75,106,72,106,71,106,71,106,71,105,69,104,71,102,71,102,72,100,73,99,73,98,71,97,72,96,72,95,73,93,74,93,75,92,76,92,77,91,78,92,78,91,79,92,81,91,82,91,84,91,84,90";
$area_map[149] = "140,30,137,31,135,30,135,29,134,29,133,29,132,30,131,30,129,28,129,30,126,29,125,31,126,32,126,32,126,33,125,34,126,35,124,36,125,36,124,37,125,39,125,40,126,40,126,41,124,42,125,43,124,44,126,46,125,47,126,47,128,49,130,50,131,51,131,52,131,52,132,52,135,55,134,56,134,57,136,59,136,60,137,60,137,60,138,59,138,60,139,58,140,59,140,59,142,60,143,59,145,60,146,60,149,62,150,61,150,60,151,58,152,57,153,52,152,51,152,48,153,47,154,46,154,45,153,45,154,43,152,42,151,43,150,42,149,41,151,38,152,39,154,40,155,39,157,40,158,37,157,37,155,35,152,37,151,36,151,37,150,36,149,35,148,35,148,36,147,36,144,33,145,32,144,31,144,31,142,30,141,30,140,30";
$area_map[137] = "157,40,155,39,154,40,152,39,151,38,150,40,149,40,149,41,151,42,151,42,151,42,151,43,152,42,154,43,153,45,154,45,154,46,153,48,152,48,152,52,153,52,152,55,151,58,150,58,150,60,150,61,149,62,151,63,152,64,152,65,152,66,153,66,154,67,154,67,154,68,155,69,157,68,157,67,158,68,158,66,159,66,158,63,158,62,159,58,158,57,158,55,159,54,160,51,159,50,161,47,160,46,162,44,162,43,164,42,164,40,165,38,164,38,163,38,162,37,161,38,160,37,159,38,158,37,157,40";
$area_map[141] = "105,53,105,53,104,53,102,53,101,54,101,53,100,54,101,55,99,57,102,59,102,60,100,63,101,64,99,65,98,66,100,67,99,68,100,68,98,69,99,70,99,71,99,73,100,74,101,79,101,80,101,80,101,81,101,82,101,83,101,84,103,85,104,85,105,85,106,85,107,86,108,85,107,84,108,84,110,87,110,88,111,88,114,90,113,91,114,91,112,93,112,94,114,95,115,94,117,95,117,95,119,94,119,92,120,92,121,93,121,92,122,93,123,92,123,92,123,93,123,93,123,94,124,95,125,89,126,88,127,88,129,88,131,89,132,89,132,88,132,88,131,87,131,87,132,85,131,84,132,83,131,82,133,81,131,80,130,79,130,78,130,77,132,76,132,74,132,73,133,73,132,72,131,69,132,68,132,66,131,67,129,67,128,66,127,66,126,65,125,66,125,64,126,63,124,61,124,61,123,59,121,59,120,60,120,61,117,61,117,61,116,61,114,61,112,61,109,57,109,57,108,56,108,55,106,53,105,53";
$area_map[142] = "122,18,120,20,120,21,119,22,118,22,116,22,115,22,114,23,115,24,115,25,115,26,114,29,112,29,113,30,113,31,113,32,113,33,113,34,111,33,110,34,111,34,109,34,108,35,108,36,109,37,108,38,108,39,108,40,108,40,107,42,107,42,106,44,105,45,105,45,106,47,107,48,105,50,105,52,106,53,106,53,108,55,108,56,109,57,109,57,110,57,111,60,112,61,115,61,116,60,117,61,117,60,120,60,119,59,121,59,123,59,123,60,124,61,126,63,125,64,126,65,126,65,127,66,128,65,129,67,130,67,131,66,131,65,133,65,134,65,135,64,134,63,134,63,135,62,136,62,137,60,137,60,136,59,136,60,135,59,133,57,134,56,135,55,132,53,131,52,131,53,130,52,131,51,130,50,127,49,126,47,125,48,125,46,125,46,124,44,124,43,123,42,125,41,125,40,124,40,125,38,124,37,124,36,124,35,126,35,125,34,126,33,126,32,126,32,125,30,126,30,126,29,126,29,127,29,128,30,129,28,128,28,128,27,127,27,126,26,125,26,125,26,124,25,123,25,122,24,123,23,123,23,122,22,122,21,122,20,122,20,122,19,122,19,122,18";
$area_map[140] = "12,44,11,44,10,44,8,45,8,44,6,44,5,45,4,46,3,46,2,47,2,48,2,50,2,50,2,49,4,50,5,49,7,49,6,50,6,50,7,50,7,50,8,51,9,52,7,51,6,52,5,51,4,51,4,53,5,52,6,52,7,53,8,55,7,55,6,55,3,55,2,55,2,56,3,56,4,57,4,56,6,57,7,60,6,61,8,61,9,60,11,60,11,59,11,59,13,61,13,62,14,61,15,62,16,62,17,62,17,63,18,63,19,63,20,63,19,63,20,64,21,65,22,66,23,66,26,65,27,65,27,66,27,67,27,68,26,67,25,67,25,68,29,67,31,68,30,69,31,69,31,69,32,68,33,68,33,68,35,68,34,66,35,65,37,65,38,64,39,65,43,62,45,63,47,60,48,59,48,54,48,52,48,51,48,50,47,49,46,49,45,50,44,50,43,49,42,47,39,48,38,47,39,46,37,46,37,47,38,48,37,49,36,47,35,47,35,48,34,47,33,47,33,47,33,46,31,46,28,49,27,47,26,46,25,44,25,44,25,44,24,44,24,43,24,43,23,42,23,42,22,42,21,41,19,43,19,42,18,42,18,43,17,43,18,44,17,45,15,44,14,44,14,45,13,45,12,44";
$area_map[151] = "80,21,80,21,79,22,78,22,78,22,78,22,77,23,75,23,75,24,74,23,73,24,72,25,72,25,70,24,65,28,64,31,64,32,65,32,68,32,67,33,65,33,65,33,64,33,64,34,61,35,61,35,60,36,58,35,57,34,55,34,51,33,49,33,48,34,48,33,48,32,46,29,47,28,46,27,45,27,44,28,42,28,39,26,39,27,40,27,40,29,39,30,40,32,40,32,41,33,41,33,41,34,43,36,42,39,43,39,43,45,44,46,45,47,43,47,43,50,44,50,46,49,47,49,49,50,50,49,51,50,52,51,53,50,54,51,56,50,57,50,58,50,58,50,59,49,60,50,60,50,61,51,61,52,62,52,63,52,65,51,67,51,67,53,68,54,70,55,70,55,72,56,72,56,71,54,72,53,72,53,74,52,74,51,74,50,72,48,72,47,73,46,74,46,76,45,79,45,79,44,80,43,80,42,81,42,81,41,80,41,80,40,81,39,82,39,83,37,83,36,85,36,84,33,84,32,84,32,84,31,83,30,84,28,83,27,84,26,84,24,80,21";
$area_map[154] = "82,15,82,16,83,17,83,18,82,18,81,18,81,20,82,22,83,24,85,26,84,27,84,28,84,28,83,29,84,31,84,31,84,32,85,32,84,33,85,36,84,36,84,37,85,38,86,37,88,37,90,38,91,37,92,38,93,38,93,39,94,38,95,39,95,39,96,39,97,39,98,39,99,39,100,39,101,39,102,39,102,40,102,40,104,42,105,42,106,44,107,42,107,42,108,40,107,40,108,39,107,38,109,37,108,37,108,36,108,35,109,34,110,34,110,33,111,33,112,33,113,33,112,32,113,31,113,30,112,29,113,28,115,26,115,25,115,25,115,24,114,23,115,22,114,22,113,22,112,22,111,21,110,21,108,21,107,21,106,21,102,22,101,21,98,21,98,20,96,21,97,20,96,19,96,20,94,19,94,20,93,19,93,20,92,20,92,19,93,18,93,17,92,17,91,17,89,18,87,16,86,16,85,15,83,16,82,15";
$area_map[152] = "94,2,93,2,92,2,91,2,91,2,90,2,88,3,87,3,87,3,85,3,83,5,82,5,82,6,83,7,82,9,83,11,83,12,82,12,82,14,83,15,85,15,86,16,87,16,87,16,88,17,89,18,90,17,91,17,92,17,93,18,92,19,92,20,92,20,93,19,94,19,94,19,96,20,96,19,97,20,96,21,98,20,98,21,101,21,102,21,107,21,107,21,108,20,111,21,111,21,111,21,113,22,114,22,113,21,115,20,113,18,113,17,114,16,113,16,112,15,109,15,108,16,107,15,107,13,107,13,106,12,105,12,105,12,103,12,103,11,102,10,102,9,103,9,101,7,100,7,99,7,98,8,98,8,97,7,96,6,95,6,94,6,94,4,95,4,95,2,94,2,94,2";
$area_map[145] = "83,37,84,38,87,38,88,37,90,38,91,38,92,38,93,39,95,39,95,39,96,39,100,39,101,39,102,39,102,40,106,44,105,45,105,45,106,46,106,47,105,47,106,48,107,48,105,50,105,52,102,53,101,53,100,53,100,54,100,55,99,57,98,57,98,57,97,57,96,58,94,57,94,58,94,56,94,55,93,55,92,54,92,54,91,54,90,53,89,54,88,55,87,54,87,54,87,53,86,51,86,51,85,50,85,49,83,49,84,48,82,47,83,46,82,45,82,44,81,42,81,41,81,41,81,40,81,40,82,40,83,37";
?>
<form id="property-search" action="<?php echo JRoute::_('index.php?option=com_fcsearch&lang=' . $lang . '&Itemid=165&s_kwds=' . JText::_('COM_FCSEARCH_S_KWDS_DEFAULT') ) ?>" method="POST" class="form-vertical">
  <div class="well well-small clearfix">
    <h4 class="bottom"><?php echo JText::_('COM_FCSEARCH_ACCOMMODATION_SEARCH') ?></h4>
    <div class="search-map" style="margin:0pt auto;width:300px;">
      <svg version = "1.1" width="100%" height="200px">
        <?php foreach ($regions as $region) : ?>
          <a xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="<?php echo JRoute::_('index.php?option=com_fcsearch&s_kwds=' . $region->alias . '&lang=' . $lang . '&Itemid=165'); ?>" xlink:title="">
            <polygon points="<?php echo $area_map[$region->id] ?>" fill="#c0d0eb" class="woot">
              <title><?php echo $region->title ?></title>
              <animate attributeName="fill"
                            from="#c0d0eb" to="#647cb6"
                            begin="mouseover" dur="0.5s"
                            fill="freeze"/>
              <animate attributeName="fill"
                            from="#647cb6" to="#c0d0eb"
                            begin="mouseout" dur="0.5s"
                            fill="freeze"/>
            </polygon>
          </a>
        <?php endforeach; ?>
      </svg>
    </div>
    <label for="s_kwds" class="element-invisible">
      <?php echo JText::_('COM_FCSEARCH_ACCOMMODATION_SEARCH') ?>
    </label>
    <input id="s_kwds" class="span9 typeahead" type="text" name="s_kwds" autocomplete="Off" value="" placeholder="<?php echo JText::_('COM_FCSEARCH_ACCOMMODATION_DESTINATION_OR_PROPERTY') ?>"/> 
    <button id="property-search-button" class="btn btn-primary pull-right" href="#">
      <i class="icon-search icon-white"> </i>
      <?php echo JText::_('COM_FCSEARCH_SEARCH') ?>
    </button>

    <div class="row-fluid">
      <div class="span3">
        <label for="arrival">
          <?php echo JText::_('COM_FCSEARCH_SEARCH_ARRIVAL') ?>
        </label>
        <input id="arrival" class="span9 start_date" type="text" name="arrival" autocomplete="Off" value="<?php echo $arrival; ?>"/>    
      </div>
      <div class="span3">
        <label for="departure">
          <?php echo JText::_('COM_FCSEARCH_SEARCH_DEPARTURE') ?>
        </label>
        <input id="departure" class="span9 end_date" type="text" name="departure" autocomplete="Off" value="<?php echo $departure; ?>" />    
      </div>

      <div class="span3">
        <label for="occupancy">
          <?php echo JText::_('COM_FCSEARCH_SEARCH_OCCUPANCY') ?>
        </label>
        <select id="occupancy" class="span12" name="occupancy">
          <?php echo JHtml::_('select.options', array('' => '...', 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10), 'value', 'text', $occupancy); ?>          
        </select>
      </div>
      <div class="span3">
        <label for="bedrooms">
          <?php echo JText::_('COM_FCSEARCH_SEARCH_BEDROOMS') ?>
        </label>
        <select id="bedrooms" class="span12" name="bedrooms">
          <?php echo JHtml::_('select.options', array('' => '...', 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, '10+' => 10), 'value', 'text', $bedrooms); ?>
        </select>
      </div>
    </div>

  </div>
  <input type="hidden" name="option" value="com_fcsearch" />
</form>
<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
    <h3 id="myModalLabel">
      <?php echo JText::_('COM_FCSEARCH_SEARCH_PLEASE_ENTER_A_DESTINATION'); ?>
    </h3>
  </div>
  <div class="modal-body">
    <p>
      <?php echo JText::_('COM_FCSEARCH_SEARCH_PLEASE_ENTER_A_DESTINATION_BODY'); ?>
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true"> 
      <?php echo JText::_('COM_FCSEARCH_SEARCH_PLEASE_ENTER_A_DESTINATION_CLOSE'); ?>
    </button>
  </div>
</div>