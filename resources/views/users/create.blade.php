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

	<form class="form-horizontal" action="/user/save" method="POST">
		 {{ csrf_field() }}
		<fieldset>
			<div id="legend">
				<legend class="">Create Customer</legend>
			</div>

		<div class="control-group">
			<!-- Username -->
			<label class="control-label"  for="username">Username</label>
			<div class="controls">
				<input type="text" id="DisplayName" name="DisplayName" value="{{ old('DisplayName') }}" class="input-xlarge"> (<b>Required</b>)
			</div>
		</div>
	 
		<div class="control-group">
			<!-- E-mail -->
			<label class="control-label" for="email">E-mail</label>
			<div class="controls">
				<input type="text" id="email" name="email" value="{{ old('email') }}" class="input-xlarge"> (<b>Required</b>)
			</div>
		</div>

		<div class="control-group">
			<!-- Password-->
			<label class="control-label" for="password">Address</label>
			<div class="controls">
				<input type="text" name="Line1" value="{{ old('Line1') }}" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label"  for="password_confirm">City</label>
			<div class="controls">
				<input type="text" id="City" name="City" value="{{ old('City') }}" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Country</label>
			<div class="controls">
				<input type="text" id="Country" name="Country" value="{{ old('Country') }}" class="input-xlarge">
			</div>
		</div>
	 
		<div class="control-group">
			<label class="control-label" >State</label>
			<div class="controls">
				<input type="text" id="CountrySubDivisionCode" name="CountrySubDivisionCode" value="{{ old('CountrySubDivisionCode') }}" class="input-xlarge">
			</div>
		</div>
	 
		<div class="control-group">
			<label class="control-label" >Postal Code</label>
			<div class="controls">
				<input type="text" id="PostalCode" name="PostalCode" value="{{ old('PostalCode') }}" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Title</label>
			<div class="controls">
				<input type="text" id="Title" name="Title" value="{{ old('Title') }}" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Name</label>
			<div class="controls">
				<input type="text" id="GivenName" name="GivenName" value="{{ old('GivenName') }}" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Middle Name</label>
			<div class="controls">
				<input type="text" id="MiddleName" name="MiddleName" value="{{ old('MiddleName') }}" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Family Name</label>
			<div class="controls">
				<input type="text" id="FamilyName" name="FamilyName" value="{{ old('FamilyName') }}" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Suffix</label>
			<div class="controls">
				<input type="text" id="Suffix" name="Suffix" value="{{ old('Suffix') }}" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Fully Qualified Name</label>
			<div class="controls">
				<input type="text" id="FullyQualifiedName" name="FullyQualifiedName" value="{{ old('FullyQualifiedName') }}" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Company Name</label>
			<div class="controls">
				<input type="text" id="CompanyName" name="CompanyName" value="{{ old('CompanyName') }}" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Phone Number</label>
			<div class="controls">
				<input type="text" id="FreeFormNumber" name="FreeFormNumber" value="{{ old('FreeFormNumber') }}" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Notes</label>
			<div class="controls">
				<input type="text" id="Notes" name="Notes" value="{{ old('Notes') }}" class="input-xlarge">
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
