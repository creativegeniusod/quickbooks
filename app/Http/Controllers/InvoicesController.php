<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Invoices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Redirect;
use App\User;
use App\Invoice as invoice_model;
use DB;
use App\Quickbook_token;

/* quickbook libs */
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Invoice;

include_once(app_path() . '/helpers.php');

class InvoicesController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */

	function get_token() {
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

	function getDatService() {

		$authorizationRequestUrl = config('app.authorizationRequestUrl');
		$tokenEndPointUrl = config('app.tokenEndPointUrl');
		$client_id = config('app.client_id');
		$client_secret = config('app.client_secret');
		$oauth_scope = config('app.oauth_scope');
		$oauth_redirect_uri = config('app.oauth_redirect_uri');
		$base_url = config('app.base_url');
		$auth_mode = config('app.auth_mode');

		//$quickbook_token_data = session('quickbook_token_data');
		$quickbook_token_data = $this->get_quickbook_token_data();

		$access_token = isset($quickbook_token_data['access_token']) ? $quickbook_token_data['access_token'] : '';
		$realmID = isset($quickbook_token_data['realmID']) ? $quickbook_token_data['realmID'] : '';
		$refresh_token = isset($quickbook_token_data['refresh_token']) ? $quickbook_token_data['refresh_token'] : '';

		// Prep Data Services
		$dataService = DataService::Configure(array(
		  'auth_mode'       => $auth_mode,
		  'ClientID'        => $client_id,
		  'ClientSecret'    => $client_secret,
		  'accessTokenKey'  => $access_token,
		  'refreshTokenKey' => $refresh_token,
		  'QBORealmID'      => $realmID,
		  'baseUrl'         => $base_url
		));

		return $dataService;
	}

	function get_invoice_data($posted_fields) {
		
		$invoice_arr = array();
		$Line_arr = array();
		$SalesItemLineDetail_arr = array();
		$CustomerRef_arr = array();
		$BillEmail_arr = array();
		$BillEmailCc_arr = array();
		$BillEmailBcc_arr = array();

		$Line_arr['Amount'] = isset($posted_fields['Amount']) ? $posted_fields['Amount'] : '';
		$Line_arr['DetailType'] = 'SalesItemLineDetail';

		$SalesItemLineDetail_arr['ItemRef'] = array();
		$SalesItemLineDetail_arr['ItemRef']['value'] = isset($posted_fields['item_quantity']) ? $posted_fields['item_quantity'] : '';
		$SalesItemLineDetail_arr['ItemRef']['name'] = isset($posted_fields['name']) ? $posted_fields['name'] : '';

		$Line_arr['SalesItemLineDetail'] = $SalesItemLineDetail_arr;

		$CustomerRef_arr['value'] = isset($posted_fields['users_id']) ? $posted_fields['users_id'] : '';
		$BillEmail_arr['Address'] = isset($posted_fields['BillEmail']) ? $posted_fields['BillEmail'] : '';
		$BillEmailCc_arr['Address'] = isset($posted_fields['BillEmailCc']) ? $posted_fields['BillEmailCc'] : '';
		$BillEmailBcc_arr['Address'] = isset($posted_fields['BillEmailBcc']) ? $posted_fields['BillEmailBcc'] : '';

		$invoice_arr['Line'] = $Line_arr;
		$invoice_arr['CustomerRef'] = $CustomerRef_arr;
		$invoice_arr['BillEmail'] = $BillEmail_arr;
		$invoice_arr['BillEmailCc'] = $BillEmailCc_arr;
		$invoice_arr['BillEmailBcc'] = $BillEmailBcc_arr;

		return $invoice_arr;

	}

	public function create()
	{
		$data = [];
		$all_users = User::all('quickbook_id', 'DisplayName');
		$data['all_users'] = $all_users;
		return view('invoices/create', $data);
	}


	public function save(Request $request)
	{

		$rules = array(
            'Amount' => 'required|numeric',
            'item_quantity' => 'required|integer',
            'name' => 'required',
            'users_id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return Redirect::back()
                ->withErrors($validator) // send back all errors to the login form
                ->withInput();

        } else {

			$posted_fields = $request->all();

			$invoice_arr = $this->get_invoice_data($posted_fields);
			$invoice_res = $this->create_invoice($invoice_arr);

			$status = $invoice_res['status'];

			if ($status === false) {

				$error_msg = $invoice_res['error_msg'];

				return Redirect::back()->withInput()->withErrors([$error_msg]);

			} else {

				$invoice  = New invoice_model();

				$invoice->users_id = $posted_fields['users_id'];

				$invoice->quickbook_invoice_id = $invoice_res['quickbook_invoice_id'];
				$invoice->quickbook_invoice_data = $invoice_res['quickbook_invoice_data'];

				unset($posted_fields['users_id']);
				unset($posted_fields['_token']);

				$invoice->form_data = json_encode($posted_fields);

				if($invoice->save()) {
				   return redirect()->back()->withSuccess('Saved successflly.');

			   } else {
				   return Redirect::back()->withInput()->withErrors(['Error in saving data']);
			   }
			}
		}
	}


	public function allinvoices() {

		$invoices = DB::table('invoices')
		->select('users.id','users.DisplayName','invoices.*')
		->join('users','users.quickbook_id','=','invoices.users_id')
		->get()->toArray();
		$invoices = json_decode(json_encode($invoices), true);

		$data['invoices'] = $invoices;
		return view('invoices/allinvoices', $data);
	}

	public function edit($id)
	{
		$invoice = invoice_model::find($id);
		$data = [];
		$all_users = User::all('quickbook_id', 'DisplayName');
		$data['all_users'] = $all_users;
		$data['invoice'] = $invoice;
		return view('invoices/edit', $data);
	}

	public function update(Request $request)
	{
		$posted_fields = $request->all();
		$id = $posted_fields['id'];

		$rules = array(
            'Amount' => 'required|numeric',
            'item_quantity' => 'required|integer',
            'name' => 'required',
            'users_id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return Redirect::back()
                ->withErrors($validator) // send back all errors to the login form
                ->withInput();

        } else {

			$posted_fields = $request->all();
			$id = $posted_fields['id'];
			$invoice = invoice_model::find($id);
			$quickbook_invoice_id = $invoice->quickbook_invoice_id;

			$invoice_arr = $this->get_invoice_data($posted_fields);

			$invoice_res = $this->update_invoice($invoice_arr, $quickbook_invoice_id);

			$status = $invoice_res['status'];

			if ($status === false) {

				$error_msg = $invoice_res['error_msg'];

				return Redirect::back()->withInput()->withErrors([$error_msg]);

			} else {

				$invoice->users_id = $posted_fields['users_id'];
				$invoice->quickbook_invoice_id = $invoice_res['quickbook_invoice_id'];
				$invoice->quickbook_invoice_data = $invoice_res['quickbook_invoice_data'];

				unset($posted_fields['users_id']);
				unset($posted_fields['_token']);

				$invoice->form_data = json_encode($posted_fields);
				
				if($invoice->save()) {
				   return redirect('/invoices/all')->withSuccess('Saved successflly.');

			   } else {
				   return Redirect::back()->withInput()->withErrors(['Error in saving data']);

			   }
			}
		}
	}


	function update_invoice($invoice_arr, $id) {

		$dataService = $this->getDatService();

		$dataService->throwExceptionOnError(true);

		$invoice = $dataService->FindbyId('invoice', $id);

		$theResourceObj = Invoice::update($invoice, $invoice_arr);

		$resultingObj = $dataService->Update($theResourceObj);
		$error = $dataService->getLastError();
		$results = array();

		if ($error) {

			$status_code = $error->getHttpStatusCode() . ", ";
			$helper_message = $error->getOAuthHelperError() . ", ";
			$response_message = $error->getResponseBody();

			$error_msg = 'API Error3: '.$status_code. $helper_message. $response_message;

			$results['status'] = false;
			$results['error_msg'] = $error_msg;

		} else {

			$quickbook_res = array();
			$results['status'] = true;
			$results['quickbook_invoice_id'] = $resultingObj->Id;
			$results['quickbook_invoice_data'] = json_encode($resultingObj);
		}

		return $results;

	}

	function create_invoice($invoice_arr) {

		$dataService = $this->getDatService();

		$dataService->throwExceptionOnError(true);
		//Add a new Invoice
		$theResourceObj = Invoice::create($invoice_arr);

		$resultingObj = $dataService->Add($theResourceObj);

		$results = array();

		$error = $dataService->getLastError();

		if ($error) {

			$status_code = $error->getHttpStatusCode() . ", ";
			$helper_message = $error->getOAuthHelperError() . ", ";
			$response_message = $error->getResponseBody();

			$error_msg = 'API Error3: '.$status_code. $helper_message. $response_message;

			$results['status'] = false;
			$results['error_msg'] = $error_msg;

		} else {

			$quickbook_res = array();
			$results['status'] = true;
			$results['quickbook_invoice_id'] = $resultingObj->Id;
			$results['quickbook_invoice_data'] = json_encode($resultingObj);

		}
		return $results;

	}

}
