@extends('layouts.default')
@section('content')

<!------ Include the above in your HEAD tag ---------->

<div class="container">

		@if ($errors->any())
			<div class="alert alert-danger">
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
	@endif
	
	@if (\Session::has('success'))
    <div class="alert alert-success">
        <ul>
            <li>{!! \Session::get('success') !!}</li>
        </ul>
    </div>
@endif

<?php
$form_fields = json_decode($user['form_fields'], true);

$quickbook_response = isset($user['quickbook_response']) ? json_decode($user['quickbook_response'], true) : '';

$quickbook_response = isset($quickbook_response[0]) ? $quickbook_response[0] : '';

$BillAddr = isset($quickbook_response['BillAddr']) ? $quickbook_response['BillAddr'] : '';
$PrimaryPhone = isset($quickbook_response['PrimaryPhone']) ? $quickbook_response['PrimaryPhone'] : '';

$Line1q = isset($BillAddr['Line1']) ? $BillAddr['Line1'] : '';
$Cityq = isset($BillAddr['City']) ? $BillAddr['City'] : '';
$Countryq = isset($BillAddr['Country']) ? $BillAddr['Country'] : '';
$CountrySubDivisionCodeq = isset($BillAddr['CountrySubDivisionCode']) ? $BillAddr['CountrySubDivisionCode'] : '';
$PostalCodeq = isset($BillAddr['PostalCode']) ? $BillAddr['PostalCode'] : '';
$Titleq = isset($quickbook_response['Title']) ? $quickbook_response['Title'] : '';
$GivenNameq = isset($quickbook_response['GivenName']) ? $quickbook_response['GivenName'] : '';
$MiddleNameq = isset($quickbook_response['MiddleName']) ? $quickbook_response['MiddleName'] : '';
$FamilyNameq = isset($quickbook_response['FamilyName']) ? $quickbook_response['FamilyName'] : '';
$Suffixq = isset($quickbook_response['Suffix']) ? $quickbook_response['Suffix'] : '';
$FullyQualifiedNameq = isset($quickbook_response['FullyQualifiedName']) ? $quickbook_response['FullyQualifiedName'] : '';
$CompanyNameq = isset($quickbook_response['CompanyName']) ? $quickbook_response['CompanyName'] : '';
$FreeFormNumberq = isset($PrimaryPhone['FreeFormNumber']) ? $PrimaryPhone['FreeFormNumber'] : '';
$Notesq = isset($quickbook_response['Notes']) ? $quickbook_response['Notes'] : '';


$Line1 = isset($form_fields['Line1']) ? $form_fields['Line1'] : '';
$City = isset($form_fields['City']) ? $form_fields['City'] : '';
$Country = isset($form_fields['Country']) ? $form_fields['Country'] : '';
$CountrySubDivisionCode = isset($form_fields['CountrySubDivisionCode']) ? $form_fields['CountrySubDivisionCode'] : '';
$PostalCode = isset($form_fields['PostalCode']) ? $form_fields['PostalCode'] : '';
$Title = isset($form_fields['Title']) ? $form_fields['Title'] : '';
$GivenName = isset($form_fields['GivenName']) ? $form_fields['GivenName'] : '';
$MiddleName = isset($form_fields['MiddleName']) ? $form_fields['MiddleName'] : '';
$FamilyName = isset($form_fields['FamilyName']) ? $form_fields['FamilyName'] : '';
$Suffix = isset($form_fields['Suffix']) ? $form_fields['Suffix'] : '';
$FullyQualifiedName = isset($form_fields['FullyQualifiedName']) ? $form_fields['FullyQualifiedName'] : '';
$CompanyName = isset($form_fields['CompanyName']) ? $form_fields['CompanyName'] : '';
$FreeFormNumber = isset($form_fields['FreeFormNumber']) ? $form_fields['FreeFormNumber'] : '';
$Notes = isset($form_fields['Notes']) ? $form_fields['Notes'] : '';

$Line1 = !empty($Line1q) ? $Line1q : $Line1;
$City = !empty($Cityq) ? $Cityq : $City;
$Country = !empty($Countryq) ? $Countryq : $Country;
$CountrySubDivisionCode = !empty($CountrySubDivisionCodeq) ? $CountrySubDivisionCodeq : $CountrySubDivisionCode;
$PostalCode = !empty($PostalCodeq) ? $PostalCodeq : $PostalCode;
$Title = !empty($Titleq) ? $Titleq : $Title;
$GivenName = !empty($GivenNameq) ? $GivenNameq : $GivenName;
$MiddleName = !empty($MiddleNameq) ? $MiddleNameq : $MiddleName;
$FamilyName = !empty($FamilyNameq) ? $FamilyNameq : $FamilyName;
$Suffix = !empty($Suffixq) ? $Suffixq : $Suffix;
$FullyQualifiedName = !empty($FullyQualifiedNameq) ? $FullyQualifiedNameq : $FullyQualifiedName;
$CompanyName = !empty($CompanyNameq) ? $CompanyNameq : $CompanyName;
$FreeFormNumber = !empty($FreeFormNumberq) ? $FreeFormNumberq : $FreeFormNumber;
$Notes = !empty($Notesq) ? $Notesq : $Notes;

?>

	<form class="form-horizontal" action="/user/update" method="POST">
		 {{ csrf_field() }}
		<fieldset>
			<div id="legend">
				<legend class="">Create Customer</legend>
			</div>

		<div class="control-group">
			<!-- Username -->
			<label class="control-label"  for="username">Username</label>
			<div class="controls">
				<input type="text" id="DisplayName" name="DisplayName" value="<?php echo $user['DisplayName'];?>" class="input-xlarge"> (<b>Required</b>)
				<input type="hidden" id="id" name="id" value="<?php echo $user['id'];?>">
			</div>
		</div>
	 
		<div class="control-group">
			<!-- E-mail -->
			<label class="control-label" for="email">E-mail</label>
			<div class="controls">
				<input type="text" id="email" name="email" value="<?php echo $user['email'];?>" class="input-xlarge"> (<b>Required</b>)
			</div>
		</div>

		<div class="control-group">
			<!-- Password-->
			<label class="control-label" for="password">Address</label>
			<div class="controls">
				<input type="text" name="Line1" value="<?php echo $Line1;?>" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label"  for="password_confirm">City</label>
			<div class="controls">
				<input type="text" id="City" name="City" value="<?php echo $City;?>" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Country</label>
			<div class="controls">
				<input type="text" id="Country" name="Country" value="<?php echo $Country;?>" class="input-xlarge">
			</div>
		</div>
	 
		<div class="control-group">
			<label class="control-label" >State</label>
			<div class="controls">
				<input type="text" id="CountrySubDivisionCode" name="CountrySubDivisionCode" value="<?php echo $CountrySubDivisionCode;?>" class="input-xlarge">
			</div>
		</div>
	 
		<div class="control-group">
			<label class="control-label" >Postal Code</label>
			<div class="controls">
				<input type="text" id="PostalCode" name="PostalCode" value="<?php echo $PostalCode;?>" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Title</label>
			<div class="controls">
				<input type="text" id="Title" name="Title" value="<?php echo $Title;?>" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Name</label>
			<div class="controls">
				<input type="text" id="GivenName" name="GivenName" value="<?php echo $GivenName;?>" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Middle Name</label>
			<div class="controls">
				<input type="text" id="MiddleName" name="MiddleName" value="<?php echo $MiddleName;?>" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Family Name</label>
			<div class="controls">
				<input type="text" id="FamilyName" name="FamilyName" value="<?php echo $FamilyName;?>" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Suffix</label>
			<div class="controls">
				<input type="text" id="Suffix" name="Suffix" value="<?php echo $Suffix;?>" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Fully Qualified Name</label>
			<div class="controls">
				<input type="text" id="FullyQualifiedName" name="FullyQualifiedName" value="<?php echo $FullyQualifiedName;?>" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Company Name</label>
			<div class="controls">
				<input type="text" id="CompanyName" name="CompanyName" value="<?php echo $CompanyName;?>" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Phone Number</label>
			<div class="controls">
				<input type="text" id="FreeFormNumber" name="FreeFormNumber" value="<?php echo $FreeFormNumber;?>" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Notes</label>
			<div class="controls">
				<input type="text" id="Notes" name="Notes" value="<?php echo $Notes;?>" class="input-xlarge">
			</div>
		</div>


		<div class="control-group">
			<!-- Button -->
			<div class="controls">
				<button class="btn btn-success">Save</button>
			</div>
		</div>
	  </fieldset>
	</form>

</div>
    
@stop
