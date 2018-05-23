<?php
/**
* webterminal
* author Sannikov Dmitriy sannikovdi@yandex.ru
* support page 
* @package project
* @author Wizard <sergejey@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 09:04:00 [Apr 04, 2016])
*/
//
//
class webterminal extends module {
/**
*
* Module class constructor
*
* @access private
*/
function webterminal() {
  $this->name="webterminal";
  $this->title="webterminal";
  $this->module_category="<#LANG_SECTION_APPLICATIONS#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
 function edit_classes(&$out, $id) {
  require(DIR_MODULES.$this->name.'/classes_edit.inc.php');
 }
function saveParams($data=0) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $tab;
  global $today;
  global $forecast;
  global $type;	
  global $skin;		
	
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
if (isset($today)) {
   $this->today=$today;
  }	
	
if (isset($type)) {
   $this->type=$type;
  }		
	
if (isset($skin)) {
   $this->skin=$skin;
  }		
	
	
if (isset($forecast)) {
   $this->forecast=$forecast;
  }		
	
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
// global $type;	
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['TAB']=$this->tab;
	
//$out['TODAY']=$this->today;	
//$out['FORECAST']=$this->forecast;		
$out['TYPE']=$this->type;			

	
//if (IsSet($this->skin)) {$out['SKIN']=$this->skin;}	
//else {$out['SKIN']=1;}
	
$out['SKIN']=1;
	
//$out['TYPE']=$type;				
//$out['TYPE']='FORECAST';
	
	
  $date = date("Y-m-d");
  $date = strtotime($date);
  $out['D1']=date('d/m', strtotime("+1 day", $date));
  $out['D2']=date('d/m', strtotime("+2 day", $date));
  $out['D3']=date('d/m', strtotime("+3 day", $date));
  $out['D4']=date('d/m', strtotime("+4 day", $date));
  $out['D5']=date('d/m', strtotime("+5 day", $date));
  $out['D6']=date('d/m', strtotime("+6 day", $date));
  $out['D7']=date('d/m', strtotime("+7 day", $date));	
	
	
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 $this->getConfig();
//        if ((time() - gg('cycle_livegpstracksRun')) < $this->config['TLG_TIMEOUT']*2 ) {
        if ((time() - gg('cycle_yandexweatherRun')) < 360*2 ) {
			$out['CYCLERUN'] = 1;
		} else {
			$out['CYCLERUN'] = 0;
		}
 $out['DUUID'] = $this->config['DUUID'];
 $out['DEVICEID']=$this->config['DEVICEID'];
	
 $out['EVERY']=$this->config['EVERY'];
 
 if (!$out['UUID']) {
	 $out['UUID'] = md5(microtime() . rand(0, 9999));
	 $this->config['UUID'] = $out['UUID'];
	 $this->saveConfig();
 }
 
 if ($this->view_mode=='update_settings') {
	global $duuid;
	$this->config['DUUID']=$duuid;	 
	global $deviceid;
	$this->config['DEVICEID']=$deviceid;	 
   
   $this->saveConfig();
   $this->redirect("?");
 }
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 
// if ($this->tab=='' || $this->tab=='outdata') {
//   $this->outdata_search($out);
// }  
 if ($this->tab=='' || $this->tab=='indata' || $this->tab=='widgets') {
$today = $this->today;		 
    $this->indata_search($out); 
 }
	
 if ($this->tab=='indataforecast') {
    $forecast = $this->forecast;		 
    $this->indata_search($out); 
 }
	
 if ($this->view_mode=='config_edit') {
   $this->config_edit($out, $this->id);
 }
 if ($this->view_mode=='config_check') {
echo "echeck";
   $this->config_check($this->id);
 }
 if ($this->view_mode=='config_uncheck') {
   $this->config_uncheck($this->id);
 }
if ($this->view_mode=='config_mycity') {
   $this->config_mycity($this->id);
 }
if ($this->view_mode=='indata_del') {
   $this->config_del($this->id);
 }
	
 if ($this->view_mode=='get') {
setGlobal('cycle_yandexweatherControl','start'); 
		$this->getdatefnc();
 }
	
//$today = $this->today;	
//$forecast = $this->forecast;		
//if ($map == "on") {$out["MULTI_ROUTES"]["map"]= gg('yt_settings.height');} else {$out["MULTI_ROUTES"]["map"]= "0";}		
	
}
/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {

 $this->admin($out);
}
 
 function indata_search(&$out) {	 
  require(DIR_MODULES.$this->name.'/indata.inc.php');
  require(DIR_MODULES.$this->name.'/cfgdata.inc.php');
 }
 function processCycle() {
   $this->getConfig();
   $every=$this->config['EVERY'];
   $tdev = time()-$this->config['LATEST_UPDATE'];
   $has = $tdev>$every*60;
   if ($tdev < 0) {
		$has = true;
   }
   
   if ($has) {  
$this->getdatefnc();   
		 
	$this->config['LATEST_UPDATE']=time();
	$this->saveConfig();
   } 
  }
/**
* InData edit/add
*
* @access public
*/
 
 function config_edit(&$out, $id) {	
  require(DIR_MODULES.$this->name.'/config_edit.inc.php');
 } 
/**
* InData delete record
*
* @access public
*/
 function config_del($id) {
  // some action for related tables
  SQLExec("DELETE FROM yaweather_cities WHERE ID='".$id."'");
 }
/**
* InData delete record
*
* @access public
*/
 function config_check($id) {
  $rec=SQLSelectOne("SELECT * FROM yaweather_cities WHERE ID=".$id);
//echo "<br>". implode( $id);
   $rec['check']=1;
SQLUpdate('yaweather_cities',$rec); 
} 
/**
* InData delete record
*
* @access public
*/
 
 function config_uncheck($id) {
  $rec=SQLSelectOne("SELECT * FROM yaweather_cities WHERE ID=".$id);
   $rec['check']=0;
SQLUpdate('yaweather_cities',$rec); 
} 
	
	
 function config_mycity($id) {
$rec=SQLSelectOne("update yaweather_cities set mycity=0");
SQLExec($rec);
	 
$rec=SQLSelectOne("update yaweather_cities set mycity=1 WHERE ID=".$id );
SQLExec($rec);
	 
} 	
	
 
 
///////////////////////////////////
function  getdatefnc(){
$this->getConfig();
$timestamp = time();
$token = md5('eternalsun'.$timestamp);
 
$uuid = "0b122ce93c77f68831839ca1d7cbf44a";
$deviceid = "3fb4aa04ac896f1b51dd48d643d9e76e";

	
	$properties=SQLSelect("SELECT * FROM `yaweather_cities` where `check`=1   ");
	
	
foreach ($properties as $did)
{

   
 
$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"User-Agent: yandex-weather-android/4.2.1\n" .
               "X-Yandex-Weather-Client: YandexWeatherAndroid/4.2.1\n" .
               "X-Yandex-Weather-Device: os=null;os_version=21;manufacturer=chromium;model=App Runtime for Chrome Dev;device_id=$deviceid;uuid=$uuid;\n" .
               "X-Yandex-Weather-Token: $token\n" .
               "X-Yandex-Weather-Timestamp: $timestamp\n" .
               "X-Yandex-Weather-UUID: $uuid\n" .
               "X-Yandex-Weather-Device-ID: $deviceid\n" .
               "Accept-Encoding: gzip, deflate\n" .
               "Host: api.weather.yandex.ru\n" .
               "Connection: Keep-Alive"
  )
);
 
$context = stream_context_create($opts);
	
$cityid=$did[ID];
$latlon=$did[latlon];	
	
 //ID города узнаем тут: https://pogoda.yandex.ru/static/cities.xml
//region="11162" id="28440
//$file = file_get_contents('https://api.weather.yandex.ru/v1/forecast?geoid=54&lang=ru', false, $context);
//$file = file_get_contents('https://api.weather.yandex.ru/v1/forecast?geoid=53&lang=ru', false, $context);
$file = file_get_contents('https://api.weather.yandex.ru/v1/forecast?geoid='.$cityid.'&lang=ru', false, $context);	
if (isset($cityid)) {$file = file_get_contents('https://api.weather.yandex.ru/v1/forecast?geoid='.$cityid.'&lang=ru', false, $context);}
if (isset($latlon)) {$file = file_get_contents('https://api.weather.yandex.ru/v1/forecast?'.$latlon.'&lang=ru', false, $context);}	
//$file = file_get_contents('https://api.weather.yandex.ru/v1/locations?lang=ru', false, $context);
 
header('Content-type: text/json');
//echo gzdecode($file);
$otvet=gzdecode($file);
$data=json_decode($otvet,true);
//$objn=$data[0]['id'];
$objn=$data[info][slug];
$src=$data[info];
//echo $objn;
addClassObject('YandexWeather',$objn);
sg( $objn.'.json',$otvet);
$src=$data[info];
sg( $objn.'.now',gg('sysdate').' '.gg('timenow')); 
	
foreach ($src as $key=> $value ) { 
if (is_array($value)) {
foreach ($value as $key2=> $value2 ) {sg( $objn.'.'.$key.'_'.$key2,$value2); }
}	
else	
{sg( $objn.'.'.$key,$value); }     

$src=$data[geo_object];
foreach ($src as $key=> $value ) {
if (is_array($value)) {
foreach ($value as $key2=> $value2 ) {sg( $objn.'.'.$key.'_'.$key2,$value2); }
}	
else	
{sg( $objn.'.'.$key,$value); }     
}	
	
	
$src=$data[fact];
	foreach ($src as $key=> $value ) { sg( $objn.'.'.$key,$value); }
	
	}     
	$fobjn= $objn;
	$src=$data[forecasts][0][parts];
		foreach ($data[forecasts] as $day=> $value ) { 
			foreach ($data[forecasts][$day][parts] as $key=> $value ) {    
				
				
				
			sg( $fobjn.'.'."forecast_".$day."_".$key.'_temp_avg',$data[forecasts][$day][parts][$key][temp_avg]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'_wind_speed',$data[forecasts][$day][parts][$key][wind_speed]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'_wind_gust',$data[forecasts][$day][parts][$key][wind_gust]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'_wind_dir',$data[forecasts][$day][parts][$key][wind_dir]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'_pressure_mm',$data[forecasts][$day][parts][$key][pressure_mm]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'_pressure_pa',$data[forecasts][$day][parts][$key][pressure_pa]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'_humidity',$data[forecasts][$day][parts][$key][humidity]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'condition',$data[forecasts][$day][parts][$key][condition]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'daytime',$data[forecasts][$day][parts][$key][daytime]); 
 			}
		}
	
	
//mycity	
	
$objmycity='yw_mycity';
addClassObject('YandexWeather',$objmycity);	
	
$mycity1=SQLSelectOne("SELECT ID FROM `yaweather_cities` where `mycity`=1 ");
$mycity=$mycity1[ID];	

sg($objmycity.'.cityID', $mycity);
	
if ($mycity==$cityid){
$objprops=get_props($fobjn);
foreach ($objprops as $value){ sg($objmycity.'.'.$value,gg($fobjn.".".$value));}	
}
}	
	
}
  
  
  
 
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
  parent::install();
 }
/**
* Uninstall
*
* Module uninstall routine
*
* @access public
*/
 function uninstall() {
 }
/**
* dbInstall
*
* Database installation routine
*
* @access private
*/
 function dbInstall($data) {

}
// --------------------------------------------------------------------
//////
/*
*
* TW9kdWxlIGNyZWF0ZWQgQXByIDA0LCAyMDE2IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/


