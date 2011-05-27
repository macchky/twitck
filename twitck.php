<?php
/**
 * Twitck PHP twitter wrapper class
 * Version 0.1
 *
 * License
 * Copyright (c) 2011, Chiba Kimiya. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products derived from this software without specific prior written permission.
 *
 * This software is provided by the author "as is" and any express or implied warranties, including, but not limited to, the implied warranties of merchantability and fitness for a particular purpose are disclaimed. In no event shall the author be liable for any direct, indirect, incidental, special, exemplary, or consequential damages (including, but not limited to, procurement of substitute goods or services; loss of use, data, or profits; or business interruption) however caused and on any theory of liability, whether in contract, strict liability, or tort (including negligence or otherwise) arising in any way out of the use of this software, even if advised of the possibility of such damage.
 *
 *
 * BSD License
 *
 */
	require_once('Twitter/twitteroauth.php');
	require_once './config.php';



class twitck
{
	
	
	
	public $LimitCount;
	
	public $LimitTime;
	
	public $Page;
	
	public $PageP;
	
	public $PageN;
	
	public $User;
	
	// url for the twitter-api
	
	const TWIT_HOME = 'statuses/home_timeline';
	
	const TWIT_MENTION = 'statuses/mentions';
	
	const TWIT_UPDATE = 'statuses/update';
	
	const TWIT_RT = 'statuses/retweet/';
	
	const TWIT_STATUS_DES = 'statuses/destroy/';
	
	const TWIT_DM_NEW = 'direct_messages/new';
	
	const TWIT_DM_DES = 'direct_messages/destroy/';	
	
	const TWIT_FAV_REG = 'favorites/create/';

	const TWIT_FAV_DES = 'favorites/destroy/';
	
	const TWIT_FAV_STATUS = 'favorites';
	

	
	function __construct()
	{
		$_token = unserialize($_SESSION['TWITTER_ACCESS_TOKEN']);
		$this->User = $_token->screen_name;
	}
	
	private function _getLimit()
	{
		$token = unserialize($_SESSION['TWITTER_ACCESS_TOKEN']);
		$twitter = new TwitterOAuth($conf['consumerKey'], $conf['consumerSecret'], $token->oauth_token, $token->oauth_token_secret);
		$limit = $twitter->get('account/rate_limit_status');
		
		$this->LimitCount = $limit->remaining_hits;
		
		$this->LimitTime = date( "H時i分s秒", $limit->reset_time_in_seconds);
		
		return;
	}
	
	
	/*取得件数*/
	private function _initCount()
	{
		if (!isset($_SESSION['TWITTER_COUNT']))
		{
			$_SESSION['TWITTER_COUNT'] = 20;
		
		} elseif (isset($_GET["count"]))
		{
			$_SESSION['TWITTER_COUNT'] = $_GET["count"];
		}
		return;
	}	
	
	private function _initPage()
	{
		if (isset($_GET["page"]))
		{
			$this->Page = $_GET["page"];
			$this->PageN = $this->Page + 1;
			$this->PageP = $this->Page - 1;
		} else {
			$this->PageN = 2;
			$this->PageP = 1;
			}
		return;
	}	
	
		  /*DMモード*/
	private function _initDMmode()
	{

		if (!isset($_SESSION['TWITTER_DMMODE'])) {
		$_SESSION['TWITTER_DMMODE'] = 'direct_messages';
		} elseif (isset($_GET["mode"])) {
  
			if ( $_GET["mode"] == 'inbox'){
   
			$_SESSION['TWITTER_DMMODE'] = 'direct_messages';
   
			} elseif ( $_GET["mode"] == 'sent'){
   
			$_SESSION['TWITTER_DMMODE'] = 'direct_messages/sent';
   
			}
		}
		return;
	}

	private function doRequest($url = null, array $param = null ,$meth = null)
	{
		if (isset($_SESSION['TWITTER_ACCESS_TOKEN']))
		{
		
		//$param = (array) $param;
		$token = unserialize($_SESSION['TWITTER_ACCESS_TOKEN']);
		$twitter = new TwitterOAuth($conf['consumerKey'], $conf['consumerSecret'], $token->oauth_token, $token->oauth_token_secret);

		
			if ($meth == 'GET')
			{
			$client = $twitter->get($url ,$param);
			
			} elseif ($meth == 'POST') {
			$client = $twitter->post($url ,$param);
			
			} else {
			exit('リクエストパラメータが適切ではありません');
			}
			
		$data = $client;

		$this->_getLimit();
		
		}
		return $data;
	
	}
	


	/********以下主要関数********/
	
	public function statusHome()
	{

		$this->_initPage();
		$this->_initCount();

		//build url
		$url = self::TWIT_HOME;
		$param = array(
		'count'  => $_SESSION['TWITTER_COUNT'],
		);
		
		$meth = 'GET';
		$_data = $this->doRequest($url ,$param ,$meth);
		
		return $_data;
	}
	
	
	public function statusMention()
	{

		$this->_initCount();
		
		//build url
		$url = self::TWIT_MENTION;
		$param = array(
			'count'  => $_SESSION['TWITTER_COUNT'],
			'include_rts' => 'true',
		);
		
		$meth ='GET';
		$_data = $this->doRequest($url ,$param ,$meth);
		
		return $_data;
	}
	
	public function statusUpdate($status, $replyid)
	{
		
		//build url
		$meth = 'POST';
		$url = self::TWIT_UPDATE;
		$replyid = (string) $replyid;
		$param = array(
		"status" => $status,
		"in_reply_to_status_id" => $replyid,
		);
		
		$_data = $this->doRequest($url, $param, $meth);
		
		return $_data;
	}


	/*公式RT*/
	public function statusRT($id = null)
	{

		//build url
		$url = self::TWIT_RT . $id;
		$meth = 'POST';
		$_data = $this->doRequest($url, $param, $meth);
		
		return $_data;
	}

	/*ツイート削除*/
	public function statusDestroy($id = null)
	{
		
		//build url
		$url = self::TWIT_STATUS_DES . $id;		
		$meth = 'POST';
		$_data = $this->doRequest($url ,$param ,$meth);
		
		return $_data;
	}
	
	
	public function DMstatus()
	{

		$this->_initCount();
		$this->_initDMmode();
		
		//build url
		$url = $_SESSION['TWITTER_DMMODE'];
		$param = array(
		'count'  => $_SESSION['TWITTER_COUNT'],
		);
		
		$meth = 'GET';
		$_data = $this->doRequest($url ,$param ,$meth);
		
		return $_data;
	}

	/*DM送信*/
	public function DMstatusUpdate($status,$uid)
	{

		//build url
		$url = self::TWIT_DM_NEW;
		$param = array(
		'text'  => $status,
		'user_id' => $uid,
		);
		
		$meth = 'POST';
		$_data = $this->doRequest($url ,$param ,$meth);
		
		return $_data;
	}
	
	
	/*DM削除*/
	public function DMdestroy($id = null)
	{
		
		//build url
		$url = self::TWIT_DM_DES . $id;	
		$meth = 'POST';
		$_data = $this->doRequest($url ,$param ,$meth);
		
		return $_data;
	}

	
	
	/*お気に入り追加*/
	public function Favcreate($id = null)
	{
		
		//build url
		$url = self::TWIT_FAV_REG . $id;		
		$meth = 'POST';
		$_data = $this->doRequest($url ,$param ,$meth);
		
		return $_data;
	}
	
	/*お気に入り削除*/
	public function Favdestroy($id = null)
	{
		
		//build url
		$url = self::TWIT_FAV_DES . $id;
		$meth = 'POST';
		$_data = $this->doRequest($url ,$param ,$meth);
		
		return $_data;
	}


	
	public function Favstatus()
	{

		$this->_initCount();
		$this->_initPage();
		
		//build url
		$url = self::TWIT_FAV_STATUS;
		$param = array(
		'count'  => $_SESSION['TWITTER_COUNT'],
		'page'  => $this->Page,
		);
		
		$meth = 'GET';
		$_data = $this->doRequest($url ,$param ,$meth);
		
		return $_data;
	}

	public function Listindex()
	{

		$this->_initCount();
		$this->_initPage();
		
		//build url
		$url = $this->User . "/lists";
		$param = array(
		'cursor' => '-1',
		);
		
		$meth = 'GET';
		$_data = $this->doRequest($url ,$param ,$meth);

		return $_data;
	}

	public function Liststatus($listname = null)
	{

		$this->_initCount();
		$this->_initPage();
		
		//build url
		$url = $this->User . "/lists/" . $listname . "/statuses";
		$param = array(
		'per_page' => $_SESSION['TWITTER_COUNT'],
		);	
		$meth = 'GET';
		$_data = $this->doRequest($url ,$param ,$meth);

		return $_data;
	}
	
}