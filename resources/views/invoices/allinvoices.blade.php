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
                      <th scope="col" class="border-0 text-uppercase font-medium">Customer</th>
                      <th scope="col" class="border-0 text-uppercase font-medium">Item Name</th>
                      <th scope="col" class="border-0 text-uppercase font-medium">Quantity</th>
                      <th scope="col" class="border-0 text-uppercase font-medium">Amount</th>
                      <th scope="col" class="border-0 text-uppercase font-medium">Action</th>
                    </tr>
                  </thead>
                  <tbody>

					<?php
					$i = 1;
					foreach($invoices as $val) {

						$form_fields = json_decode($val['form_data'], true);

						$quickbook_invoice_data = json_decode($val['quickbook_invoice_data'], true);

						$Line = isset($quickbook_invoice_data['Line'][0]['SalesItemLineDetail']) ? $quickbook_invoice_data['Line'][0]['SalesItemLineDetail'] : '';
						$Qty = isset($Line['Qty']) ? $Line['Qty'] : '';
						$UnitPrice = isset($Line['UnitPrice']) ? $Line['UnitPrice'] : '';

						$DisplayName = isset($val['DisplayName']) ? ucfirst($val['DisplayName']) : '';
						$Amount = isset($form_fields['Amount']) ? $form_fields['Amount'] : '';
						$item_quantity = isset($form_fields['item_quantity']) ? $form_fields['item_quantity'] : '';
						$name = isset($form_fields['name']) ? $form_fields['name'] : '';

						$Amount = !empty($UnitPrice) ? $UnitPrice : $Amount;
						$item_quantity = !empty($Qty) ? $Qty : $item_quantity;

					?>
						<tr>
							<td class="pl-4"><?php echo $i;?></td>
							<td><h5 class="font-medium mb-0"><?php echo $DisplayName;?></h5></td>
							<td><span class="text-muted"><?php echo $name;?></span></td>
							<td><span class="text-muted"><?php echo $item_quantity;?></span></td>
							<td><span class="text-muted"><?php echo $Amount;?></span></td>

							<td>
								<a href="/invoices/edit/<?php echo $val['id'];?>"><button type="button" class="btn btn-outline-info btn-circle btn-lg btn-circle ml-2"><i class="fa fa-edit"></i> </button></a>
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
