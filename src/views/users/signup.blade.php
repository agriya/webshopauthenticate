@extends('webshopauthenticate::base')
@section('content')
	@if(isset($success))
		@if (!Sentry::check())
			<a href="{{ URL::to(\Config::get('webshopauthenticate::uri').'/login') }}" class="btn btn-info btn-sm pull-right">{{ \Lang::get('webshopauthenticate::users.credential.submit') }}</a>
		@endif
		<h4>{{ \Lang::get('webshopauthenticate::auth/form.register.signup_done') }}</h4>
	   	<div id="success" class="alert alert-success">
	        {{ \Lang::get('webshopauthenticate::auth/form.register.signup_sent_email_1') }} <strong>{{$email}}</strong> {{ \Lang::get('webshopauthenticate::auth/form.register.signup_sent_email_2') }}
	        {{ \Lang::get('webshopauthenticate::auth/form.register.signup_sent_email_3') }}
	    </div>
	@else
		<div class = "page-header">
			<h3>{{ \Lang::get('webshopauthenticate::users.new_user_sigup') }}</h3>
		</div>
		<div class = "panel panel-default stocklist-panel">
			<div class = "panel-body">
			   {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'name' => 'signup_form', 'id' => 'signup_form')) }}
					<div class="form-group {{{ $errors->has('email') ? 'error' : '' }}}">
					     {{ Form::label('email', \Lang::get('webshopauthenticate::users.sign_up.email_address'),  array('class' => 'col-lg-2 control-label', 'for' => 'email')) }}
					     <div class="col-lg-3">
					    	 {{ Form::text('email', null, array('placeholder' => 'example@example.com', 'class' => 'form-control', 'id' => 'email', 'style' => 'width: 200px;')) }}
		 					 <label class="error">{{{ $errors->first('email') }}}</label>
		            	</div>
					</div>
					<div class="form-group {{{ $errors->has('first_name') ? 'error' : '' }}}">
					     {{ Form::label('first_name', \Lang::get('webshopauthenticate::users.sign_up.first_name'), array('class' => 'col-lg-2 control-label', 'for' => 'first_name')) }}
					     <div class="col-lg-3">
					     	{{ Form::text('first_name',null,array('class' => 'form-control', 'id' => 'first_name', 'style' => 'width: 200px;')) }}
		 					<label class="error">{{{ $errors->first('first_name') }}}</label>
		            	</div>
					</div>
					<div class="form-group {{{ $errors->has('last_name') ? 'error' : '' }}}">
					     {{ Form::label('last_name', \Lang::get('webshopauthenticate::users.sign_up.last_name'), array('class' => 'col-lg-2 control-label', 'for' => 'last_name')) }}
					     <div class="col-lg-3">
					     	{{ Form::text('last_name',null, array('class' => 'form-control', 'id' => 'last_name', 'style' => 'width: 200px;')) }}
					     	<label class="error">{{{ $errors->first('last_name') }}}</label>
		            	</div>
					</div>
					<div class="form-group {{{ $errors->has('password') ? 'error' : '' }}}">
		    			{{ Form::label('password', \Lang::get('webshopauthenticate::users.sign_up.password'), array('class' => 'col-lg-2 control-label', 'for' => 'password')) }}
		    			<div class="col-lg-3">
		    				{{ Form::password('password', array('class' => 'form-control', 'id' => 'password', 'style' => 'width: 200px;')) }}
		    				<label class="error">{{{ $errors->first('password') }}}</label>
		            	</div>
					</div>
					<div class="form-group {{{ $errors->has('password_confirmation') ? 'error' : '' }}}">
		    			{{ Form::label('password_confirmation', \Lang::get('webshopauthenticate::users.sign_up.confirm_password'), array('class' => 'col-lg-2 control-label', 'for' => 'password_confirmation')) }}
		    			<div class="col-lg-3">
		    				{{ Form::password('password_confirmation', array('class' => 'form-control', 'id' => 'password_confirmation', 'style' => 'width: 200px;')) }}
		    				<label class="error">{{{ $errors->first('password_confirmation') }}}</label>
		            	</div>
					</div>
				    <div>
		                {{ Form::submit(\Lang::get('webshopauthenticate::users.sign_up.submit'), array('class' => 'btn btn-success')) }}
		                 <a href="{{ URL::to(\Config::get('webshopauthenticate::uri').'/login') }}" class="btn btn-success" >{{ \Lang::get('webshopauthenticate::users.back_view') }}</a>
		            </div>
				{{ Form::close() }}
			</div>
		</div>
	@endif
@stop
@section('script_content')
<script language="javascript" type="text/javascript">
	var BASE = "{{ Request::root() }}";
    var err_msg = '';
    var messageFunc = function() { return err_msg; };
    jQuery.validator.addMethod(
          "chkIsNameHasRestrictedWordsLike",
          function(value, element) {
            if(value != "") {
                var filterWords = new Array();
                var restricted_keywords = "{{ Config::get('webshopauthenticate::screen_name_restrict_keywords_like') }}";
                filterWords = restricted_keywords.split(",");
                for(i = 0; i < filterWords.length; i++) {
                    // "i" is to ignore case
                    var regex = new RegExp(filterWords[i], "gi");
                    if(value.match(regex)) {
                        err_msg = "{{ Lang::get('webshopauthenticate::auth/form.register.restricted_keyword') }}";
                        err_msg = err_msg.replace("{0}", filterWords[i]);
                        return false;
                    }
                }
                return true;
            }
            return true;
          },
          messageFunc
    );
    jQuery.validator.addMethod(
          "chkIsNameHasRestrictedWordsExact",
          function(value, element) {
            if(value != "") {
                var filterWords = new Array();
                var restricted_keywords = "{{ Config::get('webshopauthenticate::screen_name_restrict_keywords_exact') }}";
                filterWords = restricted_keywords.split(",");
                for(i = 0; i < filterWords.length; i++) {
                    // "i" is to ignore case
                    var regex = new RegExp('\\b' + filterWords[i] + '\\b' , "gi");
                    if(value.match(regex)) {
                        err_msg = "{{ Lang::get('webshopauthenticate::auth/form.register.restricted_keyword') }}";
                        err_msg = err_msg.replace("{0}", filterWords[i]);
                        return false;
                    }
                }
                return true;
            }
            return true;
          },
          messageFunc
    );
	jQuery.validator.addMethod(
		"chkAlphaNumericchars",
		function(value, element) {
			if(value!=""){
				if (/^[a-zA-Z0-9\s]*$/.test(value))
					return true;
				return false;
			}
			return true;
		},
		"{{ Lang::get('webshopauthenticate::auth/form.edit-profile.merchant_signup_specialchars_not_allowed')}}"
	);
	jQuery.validator.addMethod(
		"chkspecialchars",
		function(value, element) {
			if(value!=""){
				if (/^[a-zA-Z0-9'/,&() -]*$/.test(value))
					return true;
				return false;
			}
			return true;
		},
		"{{ Lang::get('webshopauthenticate::auth/form.edit-profile.merchant_signup_specialchars_not_allowed')}}"
	);
	var mes_required = "{{ Lang::get('webshopauthenticate::auth/form.required') }}";
	$("#signup_form").validate({
		rules: {
			first_name: {
				required: true,
				minlength: "{{ Config::get('webshopauthenticate::fieldlength_name_min_length') }}",
                maxlength: "{{ Config::get('webshopauthenticate::fieldlength_name_max_length') }}",
				chkIsNameHasRestrictedWordsLike: true,
                chkIsNameHasRestrictedWordsExact: true,
				chkspecialchars: true
			},
			last_name: {
				required: true,
				minlength: "{{ Config::get('webshopauthenticate::fieldlength_name_min_length') }}",
                maxlength: "{{ Config::get('webshopauthenticate::fieldlength_name_max_length') }}",
				chkIsNameHasRestrictedWordsLike: true,
                chkIsNameHasRestrictedWordsExact: true,
				chkspecialchars: true
			},
			email: {
				required: true,
				email: true
			},
			password: {
				required: true,
				minlength: "{{ Config::get('webshopauthenticate::fieldlength_password_min') }}",
				maxlength: "{{ Config::get('webshopauthenticate::fieldlength_password_max') }}"
			},
			password_confirmation: {
				required: true,
				equalTo: "#password"
			}
		},
		messages: {
			first_name: {
				required: mes_required
			},
			last_name: {
				required: mes_required
			},
			email: {
				required: mes_required
			},
			password: {
				required: mes_required,
				minlength: jQuery.format("{{ Lang::get('webshopauthenticate::auth/form.register.validation_password_length_low') }}"),
				maxlength: jQuery.format("{{ Lang::get('webshopauthenticate::auth/form.register.validation_maxLength') }}")
			},
			"password_confirmation": {
				required: mes_required,
				equalTo: "{{ Lang::get('webshopauthenticate::auth/form.register.validation_password_mismatch') }}"
			}
		},
	    submitHandler: function(form) {
				form.submit();
		}
	});
</script>
@stop