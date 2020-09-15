<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Redirect;
use App\User as usermodel;
use App\Quickbook_post_data;
use App\Invoice as invoice_model;
use Mail;
use App\Quickbook_token;

/* these are quickbook libs */

use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Customer;

use QuickBooksOnline\API\WebhooksService\WebhooksService;
 

include_once(app_path() . '/helpers.php');

class UserController extends Controller {

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

	public function create()
	{
		$data = [];
		return view('users/create', $data);
	}


	function get_quickbook_customer_flds($posted_fields) {

		$customer_arr = array();
		$BillAddr = array();
		$phone_arr = array();
		$email_arr = array();

		$BillAddr['Line1'] = $posted_fields['Line1'];
		$BillAddr['City'] = $posted_fields['City'];
		$BillAddr['Country'] = $posted_fields['Country'];
		$BillAddr['CountrySubDivisionCode'] = $posted_fields['CountrySubDivisionCode'];
		$BillAddr['PostalCode'] = $posted_fields['PostalCode'];

		$customer_arr['BillAddr'] = $BillAddr;
		$customer_arr['Notes'] = $posted_fields['Notes'];
		$customer_arr['Title'] = $posted_fields['Title'];
		$customer_arr['GivenName'] = $posted_fields['GivenName'];
		$customer_arr['MiddleName'] = $posted_fields['MiddleName'];
		$customer_arr['FamilyName'] = $posted_fields['FamilyName'];
		$customer_arr['Suffix'] = $posted_fields['Suffix'];
		$customer_arr['FullyQualifiedName'] = $posted_fields['FullyQualifiedName'];
		$customer_arr['CompanyName'] = $posted_fields['CompanyName'];
		$customer_arr['DisplayName'] = $posted_fields['DisplayName'];

		$phone_arr['FreeFormNumber'] = $posted_fields['FreeFormNumber'];
		$customer_arr['PrimaryPhone'] = $phone_arr;

		$email_arr['Address'] = $posted_fields['email'];

		$customer_arr['PrimaryEmailAddr'] = $email_arr;

		return $customer_arr;
	}

	public function save(Request $request)
	{

		$rules = array(
            'DisplayName' => 'required|unique:users|max:255',
            'email' => 'required|unique:users|max:255'
        );

        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return Redirect::back()
                ->withErrors($validator) // send back all errors to the login form
                ->withInput();

        } else {

			$posted_fields = $request->all();

			$customer_arr = $this->get_quickbook_customer_flds($posted_fields);

			$customer_save_res =  $this->save_customer_to_quickbook($customer_arr);

			$status = $customer_save_res['status'];

			if ($status === false) {

				$error_msg = $customer_save_res['error_msg'];

				return Redirect::back()->withInput()->withErrors([$error_msg]);

			} else {

				$quickbook_id = $customer_save_res['quickbook_id'];
				$quickbook_response = $customer_save_res['quickbook_response'];

				$user  = New usermodel();
				$user->DisplayName = $posted_fields['DisplayName'];
				$user->email = $posted_fields['email'];
				$user->quickbook_id = $quickbook_id;
				$user->quickbook_response = $quickbook_response;

				unset($posted_fields['DisplayName']);
				unset($posted_fields['email']);
				unset($posted_fields['_token']);

				$user->form_fields = json_encode($posted_fields);
				if($user->save()) {
				   return redirect()->back()->withSuccess('Saved successflly.');

			   } else {
				   return Redirect::back()->withInput()->withErrors(['Error in saving data']);
			   }
			}
		}
	}

	public function allusers() {

		$users = usermodel::all();
		$data['users'] = $users;
		return view('users/allusers', $data);
	}

	public function edit($id)
	{
		$user = usermodel::find($id);
		$data = [];
		$data['user'] = $user;
		return view('users/edit', $data);
	}

	public function update(Request $request)
	{

		$posted_fields = $request->all();
		$id = $posted_fields['id'];

		$rules = array(
            'DisplayName' => 'required|unique:users|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$id,
            'DisplayName' => 'required|max:255|unique:users,DisplayName,'.$id,
        );

        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return Redirect::back()
                ->withErrors($validator) // send back all errors to the login form
                ->withInput();

        } else {

			$posted_fields = $request->all();

            $user = usermodel::find($id);

            $quickbook_id = $user->quickbook_id;

			$customer_arr = $this->get_quickbook_customer_flds($posted_fields);

            if(!empty($quickbook_id)) {   /* update the quickbook record */

				$dataService = $this->getDatService();

				$quickbook_user = $dataService->Query("SELECT * FROM Customer where Id='$quickbook_id'");
				$error = $dataService->getLastError();

				if ($error) {

					$status_code = $error->getHttpStatusCode() . "\n";
					$helper_message = $error->getOAuthHelperError() . "\n";
					$response_message = $error->getResponseBody() . "\n";
					$error_msg = 'API Error 1: '.$status_code. $helper_message. $response_message;
					return Redirect::back()->withInput()->withErrors([$error_msg]);
				}

				if(!empty($quickbook_user)) {

					//Get the first element
					$theCustomer = reset($quickbook_user);

					$customer_arr['sparse'] = 'false';  //If you are going to do a full Update, set sparse to false

					$updateCustomer = Customer::update($theCustomer, $customer_arr);

					$resultingCustomerUpdatedObj = $dataService->Update($updateCustomer);
					$error = $dataService->getLastError();

					if ($error) {
						$status_code = $error->getHttpStatusCode() . "\n";
						$helper_message = $error->getOAuthHelperError() . "\n";
						$response_message = $error->getResponseBody() . "\n";

						$error_msg = 'API Error 2: '.$status_code. $helper_message. $response_message;
						return Redirect::back()->withInput()->withErrors([$error_msg]);
					}

					$user->quickbook_response = json_encode($resultingCustomerUpdatedObj);

				} else {

					/* insert the record to quickbook */

					$customer_save_res =  $this->save_customer_to_quickbook($customer_arr);

					$status = $customer_save_res['status'];

					if ($status === false) {

						$error_msg = $customer_save_res['error_msg'].' 2 ';

						return Redirect::back()->withInput()->withErrors([$error_msg]);

					} else {

						$quickbook_id = $results['quickbook_id'];
						$quickbook_response = $results['quickbook_response'];

						$user->quickbook_id = $quickbook_id;
						$user->quickbook_response = $quickbook_response;
						
					}
				}

			} else {  /* insert the record to quickbook */

				$customer_save_res =  $this->save_customer_to_quickbook($customer_arr);

				$status = $customer_save_res['status'];

				if ($status === false) {

					$error_msg = $customer_save_res['error_msg'].' 1 ';

					return Redirect::back()->withInput()->withErrors([$error_msg]);

				} else {

					$quickbook_id = $customer_save_res['quickbook_id'];
					$quickbook_response = $customer_save_res['quickbook_response'];

					$user->quickbook_id = $quickbook_id;
					$user->quickbook_response = $quickbook_response;
					
				}
			}

            $user->DisplayName = $posted_fields['DisplayName'];
            $user->email = $posted_fields['email'];

            unset($posted_fields['DisplayName']);
            unset($posted_fields['email']);
            unset($posted_fields['_token']);

            $user->form_fields = json_encode($posted_fields);

            if($user->save()) {
			   return redirect('/user/allusers')->withSuccess('Saved successflly.');

		   } else {
			   return Redirect::back()->withInput()->withErrors(['Error in saving data']);

		   }
		}
	}


	function save_customer_to_quickbook($customer_arr) {

		$customerObj = Customer::create($customer_arr);

		$dataService = $this->getDatService();

		$resultingCustomerObj = $dataService->Add($customerObj);

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
			$results['quickbook_id'] = $resultingCustomerObj->Id;
			$results['quickbook_response'] = json_encode($resultingCustomerObj);

		}
		return $results;
	}


	public function quickbook_post_data() {

		$dataService = $this->getDatService();

		$payLoad = file_get_contents("php://input");

		//$payLoad = '{"eventNotifications":[{"realmId":"4620816365148128340","dataChangeEvent":{"entities":[{"name":"Invoice","id":"145","operation":"Update","lastUpdated":"2020-09-10T11:33:20.000Z"}]}}]}';
		//$payLoad = '{"eventNotifications":[{"realmId":"4620816365148128340","dataChangeEvent":{"entities":[{"name":"Customer","id":"72","operation":"Update","lastUpdated":"2020-09-10T10:40:47.000Z"}]}}]}';

		//pr($payLoad);

		if(!empty($payLoad)) {

			$user  = New Quickbook_post_data();
			$user->posted_data = $payLoad;
			

			if($user->save()) {

			//if($payLoad) {

				$payLoad = json_decode($payLoad, true);

				$entity_name = isset($payLoad['eventNotifications'][0]['dataChangeEvent']['entities'][0]['name']) ? $payLoad['eventNotifications'][0]['dataChangeEvent']['entities'][0]['name'] : '';
				$id = isset($payLoad['eventNotifications'][0]['dataChangeEvent']['entities'][0]['id']) ? $payLoad['eventNotifications'][0]['dataChangeEvent']['entities'][0]['id'] : '';

				if(!empty($entity_name) && !empty($id)) {

					if($entity_name == 'Invoice') {

						$this->insert_update_invoice($entity_name, $id);

					} else {

						$dataService = $this->getDatService();

						$sql = "SELECT * FROM $entity_name where Id='$id'";

						$quickbook_response = $quickbook_user = $dataService->Query($sql);
						$error = $dataService->getLastError();

						$error1 = json_encode($error);
						$quickbook_user1 = json_encode($quickbook_user);

						if (!empty($quickbook_user)) {

							$email = isset($quickbook_user[0]->PrimaryEmailAddr->Address) ? $quickbook_user[0]->PrimaryEmailAddr->Address : '';

							if(!empty($email)) {

								$email = isset($quickbook_user[0]->PrimaryEmailAddr->Address) ? $quickbook_user[0]->PrimaryEmailAddr->Address : '';
								$quickbook_id = isset($quickbook_user[0]->Id) ? $quickbook_user[0]->Id : '';
								$DisplayName = isset($quickbook_user[0]->DisplayName) ? $quickbook_user[0]->DisplayName : '';

								$condition = ['quickbook_id' => $quickbook_id];

								$user_data = usermodel::where($condition)->get();

								if (count($user_data)) { /* update row */

									$id = $user_data[0]->id;
									$update_arr = array();
									$update_arr['email'] = $email;
									$update_arr['DisplayName'] = $DisplayName;
									$update_arr['quickbook_response'] = json_encode($quickbook_response);

									$is_update = usermodel::where('id', $id)->update($update_arr);

									if($is_update) {
										$res = 'user updated';
									} else {
										$res = 'update error ';
									}

								} else {  /* insert the row */

									$user  = New usermodel();
									$user->DisplayName = $DisplayName;
									$user->email = $email;
									$user->quickbook_id = $quickbook_id;
									$user->form_fields = '';
									$user->quickbook_response = json_encode($quickbook_response);

									if($user->save()) {
										$res = 'insert success';
									} else {
										$res = 'error in insert';
									}
								}
							} else {
								$res = 'email is empty ';
							}
						} else {
							$res = 'there is error or no quickbook user ';
						}
					}
				} else {
					$res = 'name / id empty';
				}

		   } else {
			   $res = 'user not saved';
		   }
	   } else {
		   $res = 'payload empty';
	   }
	}


	function insert_update_invoice($entity_name, $id) {

		$dataService = $this->getDatService();

		$dataService->throwExceptionOnError(true);

		$quickbook_invoice_data = $invoice = $dataService->FindbyId('invoice', $id);
		$error = $dataService->getLastError();

		if (!$error && !empty($invoice)) {

			$quickbook_invoice_id = isset($invoice->Id) ? $invoice->Id : '';
			$users_id = isset($invoice->CustomerRef) ? $invoice->CustomerRef : '';

			$condition = ['quickbook_invoice_id' => $quickbook_invoice_id];

			$invoice_data = invoice_model::where($condition)->get();

			if (count($invoice_data)) { /* update row */

				$id = $invoice_data[0]->id;
				$update_arr = array();
				$update_arr['users_id'] = $users_id;
				$update_arr['quickbook_invoice_id'] = $quickbook_invoice_id;
				$update_arr['quickbook_invoice_data'] = json_encode($quickbook_invoice_data);

				$is_update = invoice_model::where('id', $id)->update($update_arr);

				if($is_update) {
					$res = 'invoice updated';
				} else {
					$res = 'invoice update error  ';
				}

			} else {  /* insert the row */

				$invoice_record  = New invoice_model();
				$invoice_record->users_id = $users_id;
				$invoice_record->quickbook_invoice_id = $quickbook_invoice_id;
				$invoice_record->form_data = '';
				$invoice_record->quickbook_invoice_data = json_encode($quickbook_invoice_data);

				if($invoice_record->save()) {
					$res = 'insert success invoice';
				} else {
					$res =  'error in insert invoice';
				}
			}
		} else {
			$res =  'there is error or invoice is empty';
		}
	}


	public function test() {

		$dataService = $this->getDatService();

		$id = 73;
		$entity_name = 'Customer';

		$quickbook_response = $quickbook_user = $dataService->Query("SELECT * FROM $entity_name where Id='$id'");
		$error = $dataService->getLastError();

		pr($quickbook_response);
		$res = json_encode($quickbook_response);

		if (!$error && !empty($quickbook_user)) {

			$email = isset($quickbook_user[0]->PrimaryEmailAddr->Address) ? $quickbook_user[0]->PrimaryEmailAddr->Address : '';

			if(!empty($email)) {
				
			}
		}
	}

}
