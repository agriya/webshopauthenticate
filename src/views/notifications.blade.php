@if ($errors->any())
<div class="alert alert-danger alert-block">
	<h4>{{ Lang::get('webshopauthenticate::common.error') }}</h4>
	{{ Lang::get('webshopauthenticate::common.error_info') }}
</div>
@endif

@if ($success_message = Session::get('success'))
<div class="alert alert-success alert-block">
	<h4>{{ Lang::get('webshopauthenticate::common.success') }}</h4>
	{{ $success_message }}
</div>
@endif

@if ($error_message = Session::get('error'))
<div class="alert alert-danger alert-block">
	<h4>{{ Lang::get('webshopauthenticate::common.error') }}</h4>
	{{ $error_message }}
</div>
@endif