<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Home;
use App\Quickbook_token;
use QuickBooksOnline\API\DataService\DataService;
use Illuminate\Http\Request;
use DB;
use Mail;
use Illuminate\Support\Facades\Storage;


include_once(app_path() . '/helpers.php');


class HomeController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */

	function getDatService() {

		$authorizationRequestUrl = config('app.authorizationRequestUrl');
		$tokenEndPointUrl = config('app.tokenEndPointUrl');
		$client_id = config('app.client_id');
		$client_secret = config('app.client_secret');
		$oauth_scope = config('app.oauth_scope');
		$oauth_redirect_uri = config('app.oauth_redirect_uri');
		$base_url = config('app.base_url');
		$auth_mode = config('app.auth_mode');

		$dataService = DataService::Configure(array(
			'auth_mode' => $auth_mode,
			'ClientID' => $client_id,
			'ClientSecret' =>  $client_secret,
			'RedirectURI' => $oauth_redirect_uri,
			'scope' => $oauth_scope,
			'baseUrl' => $base_url
		));

		return $dataService;
	}

	public function index(Request $request)
	{

		//$request->session()->forget('sessionAccessToken');

		$dataService = $this->getDatService();

		$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
		$authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();

		// Store the url in PHP Session Object;
		//$_SESSION['authUrl'] = $authUrl;
		session(['authUrl' => $authUrl]);

		$accessTokenJson = '';

		//set the access token using the auth object
		//if (isset($_SESSION['sessionAccessToken'])) {

		/*if(session()->has('sessionAccessToken')) {

			$accessToken = session('sessionAccessToken');
			$accessToken = $token_data;
			$accessTokenJson = array('token_type' => 'bearer',
				'access_token' => $accessToken->getAccessToken(),
				'refresh_token' => $accessToken->getRefreshToken(),
				'x_refresh_token_expires_in' => $accessToken->getRefreshTokenExpiresAt(),
				'expires_in' => $accessToken->getAccessTokenExpiresAt(),
				'realmID' => $accessToken->getRealmID()
			);
			$dataService->updateOAuth2Token($accessToken);
			$oauthLoginHelper = $dataService -> getOAuth2LoginHelper();
			$CompanyInfo = $dataService->getCompanyInfo();
			session(['quickbook_token_data' => $accessTokenJson]);
		}*/

		$accessToken = $this->get_token();
		//$accessToken = '';

		if(!empty($accessToken) && !$accessToken !== false) {

			$accessTokenJson = array('token_type' => 'bearer',
				'access_token' => $accessToken->getAccessToken(),
				'refresh_token' => $accessToken->getRefreshToken(),
				'x_refresh_token_expires_in' => $accessToken->getRefreshTokenExpiresAt(),
				'expires_in' => $accessToken->getAccessTokenExpiresAt(),
				'realmID' => $accessToken->getRealmID()
			);
			$dataService->updateOAuth2Token($accessToken);
			$oauthLoginHelper = $dataService -> getOAuth2LoginHelper();
			$CompanyInfo = $dataService->getCompanyInfo();
		}

		$data = [];

		$token_data = $this->get_token();

		$access_token_db = $token_data->getAccessToken();

		$data['authUrl'] = $authUrl;
		$data['access_token_db'] = $access_token_db;
		$data['accessTokenJson'] = $accessTokenJson;

		return view('home/auth_quickbook', $data);
	}

	public function callback()
	{
		if(isset($_SERVER['QUERY_STRING'])) {

			// Create SDK instance
			$dataService = $this->getDatService();

			$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
			$parseUrl = $this->parseAuthRedirectUrl($_SERVER['QUERY_STRING']);

			/*
			 * Update the OAuth2Token
			 */
			$accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($parseUrl['code'], $parseUrl['realmId']);
			$dataService->updateOAuth2Token($accessToken);

			/*
			 * Setting the accessToken for session variable
			 */
			//$_SESSION['sessionAccessToken'] = $accessToken;
			//session(['sessionAccessToken' => $accessToken]);

			//Storage::put('token.txt', print_r($accessToken, true));
			$accessToken1 = serialize($accessToken);
			//Storage::put('token.txt', $accessToken1);

			Quickbook_token::truncate();

			$save_obj  = New Quickbook_token();

			$save_obj->quickbook_token_data = $accessToken1;
			$save_obj->save();

		} else {
			echo 'no data found for callback'; die;
		}
	}

	public function parseAuthRedirectUrl($url)
	{
		parse_str($url,$qsArray);
		return array(
			'code' => $qsArray['code'],
			'realmId' => $qsArray['realmId']
		);
	}


	function get_quickbook_token_data() {

		$accessToken = $this->get_token();

		$accessTokenJson = '';

		if(!empty($accessToken)) {

			$accessTokenJson = array('token_type' => 'bearer',
				'access_token' => $accessToken->getAccessToken(),
				'refresh_token' => $accessToken->getRefreshToken(),
				'x_refresh_token_expires_in' => $accessToken->getRefreshTokenExpiresAt(),
				'expires_in' => $accessToken->getAccessTokenExpiresAt(),
				'realmID' => $accessToken->getRealmID()
			);
		}
		return $accessTokenJson;
	}

	public function get_refresh_token() {

		//$quickbook_token_data = session('quickbook_token_data');
		$accessToken = $this->get_token();
		//pr($quickbook_token_data, 1);


		$access_token = $accessToken->getAccessToken();
		$realmID = $accessToken->getRealmID();
		$refresh_token = $accessToken->getRefreshToken();

		$client_id = config('app.client_id');
		$client_secret = config('app.client_secret');
		$base_url = config('app.base_url');
		$auth_mode = config('app.auth_mode');
		$oauth_redirect_uri = config('app.oauth_redirect_uri');

		$dataService = DataService::Configure(array(
			'auth_mode' => $auth_mode,
			'ClientID' => $client_id,
			'ClientSecret' =>  $client_secret,
			'RedirectURI' => $oauth_redirect_uri,
			'baseUrl' => $base_url,
			'refreshTokenKey' => $accessToken->getRefreshToken(),
			'QBORealmID' => $realmID,
		));

		/*
		 * Update the OAuth2Token of the dataService object
		 */
		$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
		$refreshedAccessTokenObj = $OAuth2LoginHelper->refreshToken();
		$dataService->updateOAuth2Token($refreshedAccessTokenObj);

	    DB::beginTransaction();

		try
            {
				Quickbook_token::truncate();

				$save_obj  = New Quickbook_token();

				$save_obj->quickbook_token_data = serialize($refreshedAccessTokenObj);
				$save_obj->save();
				DB::commit();

				echo $res = 'refresh token is updated at: '.date('Y-m-d H:i:s');
				$this->basic_email($res);
		}
		catch (\Exception $e)
		{
			DB::rollback();
			echo $res = 'refresh token is not updated at: '.date('Y-m-d H:i:s');
			$this->basic_email($res);
			throw $e;
		}
	}


	public function get_token() {
		try{

			$token = Quickbook_token::find(1);
			if (!empty($token)) {

				$obj = unserialize($token->quickbook_token_data);
				return $obj;
			}
			return false;

		}
		catch(\Exception $e){
		   // do task when error
		   echo $e->getMessage();   // insert query
		   return false;
		}
	}


	public function test() {
		try{

			$obj = $this->get_token();

			pr($obj);
			
			//$accessToken = get_token();
			//pr($accessToken);

		}
		catch(\Exception $e){
		   // do task when error
		   echo $e->getMessage();   // insert query
		}
	}

	public function basic_email($res) {
      $data = array('name'=>$res);

      Mail::send(['text'=>'mail'], $data, function($message) {
         $message->to('devod0485@gmail.com', 'Tutorials Point')->subject
            ('Laravel Basic Testing Mail');
         $message->from('devtest6785@gmail.com','Arnav');
      });
      echo "Basic Email Sent. Check your inbox.";
   }

}
