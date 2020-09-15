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
$form_fields = json_decode($invoice['form_data'], true);

$quickbook_invoice_data = json_decode($invoice['quickbook_invoice_data'], true);

$Line = isset($quickbook_invoice_data['Line'][0]['SalesItemLineDetail']) ? $quickbook_invoice_data['Line'][0]['SalesItemLineDetail'] : '';
$Qty = isset($Line['Qty']) ? $Line['Qty'] : '';
$UnitPrice = isset($Line['UnitPrice']) ? $Line['UnitPrice'] : '';

$BillEmailq = isset($quickbook_invoice_data['BillEmail']['Address']) ? $quickbook_invoice_data['BillEmail']['Address'] : '';
$BillEmailCcq = isset($quickbook_invoice_data['BillEmailCc']['Address']) ? $quickbook_invoice_data['BillEmailCc']['Address'] : '';
$BillEmailBccq = isset($quickbook_invoice_data['BillEmailBcc']['Address']) ? $quickbook_invoice_data['BillEmailBcc']['Address'] : '';

$Amount = isset($form_fields['Amount']) ? $form_fields['Amount'] : '';
$item_quantity = isset($form_fields['item_quantity']) ? $form_fields['item_quantity'] : '';
$name = isset($form_fields['name']) ? $form_fields['name'] : '';
$users_id = isset($invoice['users_id']) ? $invoice['users_id'] : '';
$id = isset($invoice['id']) ? $invoice['id'] : '';
$BillEmail = isset($form_fields['BillEmail']) ? $form_fields['BillEmail'] : '';
$BillEmailCc = isset($form_fields['BillEmailCc']) ? $form_fields['BillEmailCc'] : '';
$BillEmailBcc = isset($form_fields['BillEmailBcc']) ? $form_fields['BillEmailBcc'] : '';

$Amount = !empty($UnitPrice) ? $UnitPrice : $Amount;
$item_quantity = !empty($Qty) ? $Qty : $item_quantity;
$BillEmail = !empty($BillEmailq) ? $BillEmailq : $BillEmail;
$BillEmailCc = !empty($BillEmailCcq) ? $BillEmailCcq : $BillEmailCc;
$BillEmailBcc = !empty($BillEmailBccq) ? $BillEmailBccq : $BillEmailBcc;



?>

	<form class="form-horizontal" action="/invoices/update" method="POST">
		 {{ csrf_field() }}
		<fieldset>
			<div id="legend">
				<legend class="">Edit Invoice</legend>
			</div>

		<div class="control-group">
			<!-- Username -->
			<label class="control-label"  for="username">Amount</label>
			<div class="controls">
				<input type="text" id="Amount" name="Amount" value="<?php echo $Amount; ?>" class="input-xlarge"> (<b>Required</b>)
				<input type="hidden" id="id" name="id" value="<?php echo $id;?>" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<!-- Password-->
			<label class="control-label" for="password">Item Quantity</label>
			<div class="controls">
				<input type="text" name="item_quantity" value="<?php echo $item_quantity;?>" class="input-xlarge"> (<b>Required</b>)
			</div>
		</div>

		<div class="control-group">
			<label class="control-label"  for="password_confirm">Item Name</label>
			<div class="controls">
				<input type="text" id="name" name="name" value="<?php echo $name;?>" class="input-xlarge"> (<b>Required</b>)
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Customer</label>
			<div class="controls">
				<select name="users_id">
					<option value="">Please Select</option>
						<?php foreach($all_users as $val) {
						?>
							<option value="<?php echo $val['quickbook_id'];?>" <?php if($users_id == $val['quickbook_id']) echo 'selected' ;?>><?php echo $val['DisplayName'] ;?></option>
						<?php
						}
						?>
				</select> (<b>Required</b>)
			</div>
		</div>
	 
		<div class="control-group">
			<label class="control-label" >Bill Email</label>
			<div class="controls">
				<input type="text" id="BillEmail" name="BillEmail" value="<?php echo $BillEmail;?>" class="input-xlarge">
			</div>
		</div>
	 
		<div class="control-group">
			<label class="control-label" >Bill Email Cc</label>
			<div class="controls">
				<input type="text" id="BillEmailCc" name="BillEmailCc" value="<?php echo $BillEmailCc;?>" class="input-xlarge">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" >Bill Email Bcc</label>
			<div class="controls">
				<input type="text" id="BillEmailBcc" name="BillEmailBcc" value="<?php echo $BillEmailBcc;?>" class="input-xlarge">
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
