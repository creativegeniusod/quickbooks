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

	<form class="form-horizontal" action="/invoices/save" method="POST">
		 {{ csrf_field() }}
		<fieldset>
			<div id="legend">
				<legend class="">Create Invoice</legend>
			</div>

		<div class="control-group">
			<!-- Username -->
			<label class="control-label"  for="username">Amount</label>
			<div class="controls">
				<input type="text" id="Amount" name="Amount" value="{{ old('Amount') }}" class="input-xlarge"> (<b>Required</b>)
			</div>
		</div>


		<div class="control-group">
			<!-- Password-->
			<label class="control-label" for="password">Item Quantity</label>
			<div class="controls">
				<input type="text" name="item_quantity" value="{{ old('item_quantity') }}" class="input-xlarge"> (<b>Required</b>)
			</div>
		</div>

		<div class="control-group">
			<label class="control-label"  for="password_confirm">Item Name</label>
			<div class="controls">
				<input type="text" id="name" name="name" value="{{ old('name') }}" class="input-xlarge"> (<b>Required</b>)
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Customer</label>
			<div class="controls">
				<select name="users_id">
					<option value="">Please Select</option>
						@foreach($all_users as $val)
							<option value="{{ $val['quickbook_id'] }}" @if(old('users_id') == $val['id']) {{ 'selected' }} @endif>{{ $val['DisplayName'] }}</option>
						@endforeach
				</select> (<b>Required</b>)
			</div>
		</div>
	 
		<div class="control-group">
			<label class="control-label" >Bill Email</label>
			<div class="controls">
				<input type="text" id="BillEmail" name="BillEmail" value="{{ old('BillEmail') }}" class="input-xlarge">
			</div>
		</div>
	 
		<div class="control-group">
			<label class="control-label" >Bill Email Cc</label>
			<div class="controls">
				<input type="text" id="BillEmailCc" name="BillEmailCc" value="{{ old('BillEmailCc') }}" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Bill Email Bcc</label>
			<div class="controls">
				<input type="text" id="BillEmailBcc" name="BillEmailBcc" value="{{ old('BillEmailBcc') }}" class="input-xlarge">
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
