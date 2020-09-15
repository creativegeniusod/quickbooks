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
	//echo '<pre>';
	//print_r($users);
	?>
	<style>
	body{
    background: #edf1f5;
    margin-top:20px;
}
.card {
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 0 solid transparent;
    border-radius: 0;
}
.btn-circle.btn-lg, .btn-group-lg>.btn-circle.btn {
    width: 50px;
    height: 50px;
    padding: 14px 15px;
    font-size: 18px;
    line-height: 23px;
}
.text-muted {
    color: #8898aa!important;
}
[type=button]:not(:disabled), [type=reset]:not(:disabled), [type=submit]:not(:disabled), button:not(:disabled) {
    cursor: pointer;
}
.btn-circle {
    border-radius: 100%;
    width: 40px;
    height: 40px;
    padding: 10px;
}
.user-table tbody tr .category-select {
    max-width: 150px;
    border-radius: 20px;
}
	</style>
	<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" >

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title text-uppercase mb-0">Manage Users</h5>
            </div>
            <div class="table-responsive">
                <table class="table no-wrap user-table mb-0">
                  <thead>
                    <tr>
                      <th scope="col" class="border-0 text-uppercase font-medium pl-4">#</th>
                      <th scope="col" class="border-0 text-uppercase font-medium">Username</th>
                      <th scope="col" class="border-0 text-uppercase font-medium">Email</th>
                      <th scope="col" class="border-0 text-uppercase font-medium">Address</th>
                      <th scope="col" class="border-0 text-uppercase font-medium">Name</th>
                      <th scope="col" class="border-0 text-uppercase font-medium">Phone</th>
                      <th scope="col" class="border-0 text-uppercase font-medium">Manage</th>
                    </tr>
                  </thead>
                  <tbody>

					<?php
					$i = 1;
					foreach($users as $user)   {

						$form_fields = json_decode($user['form_fields'], true);
						$quickbook_response = json_decode($user['quickbook_response'], true);
						$quickbook_response = isset($quickbook_response[0]) ? $quickbook_response[0] : $quickbook_response;

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
						$FreeFormNumber = isset($PrimaryPhone['FreeFormNumber']) ? $PrimaryPhone['FreeFormNumber'] : '';

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

					?>
						<tr>
							<td class="pl-4"><?php echo $i;?></td>
							<td><h5 class="font-medium mb-0"><?php echo $user['DisplayName'];?></h5></td>
							<td><span class="text-muted"><?php echo $user['email'];?></span></td>
							<td><span class="text-muted"><?php echo $Line1.' '. $City. ' '.$CountrySubDivisionCode. ' '. $Country. ' '. $PostalCode;?></span></td>
							<td><span class="text-muted"><?php echo $Title.' '. $GivenName. ' '.$MiddleName. ' '. $FamilyName;?></span></td>
							<td><span class="text-muted"><?php echo $FreeFormNumber;?></span></td>

							<td>
								<!--<a href="/user/delete/<?php echo $user['id'];?>"><button type="button" class="btn btn-outline-info btn-circle btn-lg btn-circle ml-2"><i class="fa fa-trash"></i> </button></a>-->
								<a href="/user/edit/<?php echo $user['id'];?>"><button type="button" class="btn btn-outline-info btn-circle btn-lg btn-circle ml-2"><i class="fa fa-edit"></i> </button></a>
							</td>
						</tr>
					<?php
						$i++;
					}
					?>

                  </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


</div>
    
@stop
