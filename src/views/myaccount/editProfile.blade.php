@extends('webshopauthenticate::base')
@section('content')
    <h1 class="title-one">{{ \Lang::get('webshopauthenticate::myaccount/form.account_menu_edit_profile') }}</h1>
    @if (Session::has('success_message') && Session::get('success_message') != "")
        <div class="alert alert-success">{{	Session::get('success_message') }}</div>
    @endif
	@if(Session::has('valid_user'))
		<div class="alert alert-danger">{{	Session::get('valid_user') }}</div>
	@endif

    <div class="clearfix row">
        <div class="col-lg-6">
            <h2 class="title-two">{{ \Lang::get('webshopauthenticate::myaccount/form.edit-profile.basic_details_title') }}:</h2>

            {{ Form::model($udetails, ['url' => URL::to(Config::get('webshopauthenticate::uri').'/myaccount'),'method' => 'post','id' => 'editaccount_frm', 'class' => 'form-horizontal form-request']) }}
                <fieldset class="mb40">
                    <div class="form-group {{{ $errors->has('current_email') ? 'error' : '' }}}">
                        {{ Form::label('current_email', \Lang::get('webshopauthenticate::myaccount/form.edit-profile.current_email'), array('class' => 'col-lg-4 control-label')) }}
                        <div class="col-lg-6">
                            {{ Form::label('current_email', $udetails['email'], array('class' => 'control-label text-bold')) }}
                        </div>
                    </div>

                    <!--<div class="form-group {{{ $errors->has('new_email') ? 'error' : '' }}}">
                        {{ Form::label('new_email', \Lang::get('webshopauthenticate::myaccount/form.edit-profile.new_email'), array('class' => 'col-lg-4 control-label')) }}
                        <div class="col-lg-6">
                            {{ Form::text('new_email', null, array ('class' => 'form-control')); }}
                            <label class="error">{{{ $errors->first('new_email') }}}</label>
                        </div>
                    </div>-->

                    <div class="form-group {{{ $errors->has('Oldpassword') ? 'error' : '' }}}">
                        {{ Form::label('Oldpassword', \Lang::get('webshopauthenticate::myaccount/form.edit-profile.current_password'), array('class' => 'col-lg-4 control-label')) }}
                        <div class="col-lg-6">
                            {{ Form::password('Oldpassword', array('class' => 'form-control')); }}
                            <label class="error">{{{ $errors->first('Oldpassword') }}}</label>
                        </div>
                    </div>

                    <div class="form-group {{{ $errors->has('password') ? 'error' : '' }}}">
                        {{ Form::label('password',  \Lang::get('webshopauthenticate::myaccount/form.edit-profile.password'), array('class' => 'col-lg-4 control-label')) }}
                        <div class="col-lg-6">
                            {{  Form::password('password', array('class' => 'form-control')); }}
                            <label class="error">{{{ $errors->first('password') }}}</label>
                        </div>
                    </div>

                    <div class="form-group {{{ $errors->has('password_confirmation') ? 'error' : '' }}}">
                        {{ Form::label('password_confirmation', \Lang::get('webshopauthenticate::myaccount/form.edit-profile.confirm_password'), array('class' => 'col-lg-4 control-label')) }}
                        <div class="col-lg-6">
                            {{  Form::password('password_confirmation', array('class' => 'form-control')); }}
                            <label class="error">{{{ $errors->first('password_confirmation') }}}</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-offset-4 col-lg-10">
                            <button type="submit" name="edit_basic" class="btn btn-success" id="edit_basic" value="edit_basic">{{ \Lang::get('webshopauthenticate::common.submit') }}</button>
                        </div>
                    </div>
                </fieldset>
            {{ Form::close() }}
        </div>

        <div class="col-lg-6">
            <h2 class="title-two">{{ \Lang::get('webshopauthenticate::myaccount/form.edit-profile.personal_details_title') }}:</h2>

            {{ Form::model($udetails, ['url' => URL::to(Config::get('webshopauthenticate::uri').'/myaccount'),'method' => 'post','id' => 'editpersonal_details_frm', 'class' => 'form-horizontal form-request', 'files' => 'true', 'enctype' => 'multipart/form-data']) }}
                <fieldset class="mb40">
                    <div class="form-group {{{ $errors->has('first_name') ? 'error' : '' }}}">
                        {{ Form::label('first_name', \Lang::get('webshopauthenticate::myaccount/form.edit-profile.first_name'), array('class' => 'col-lg-4 control-label required-icon')) }}
                        <div class="col-lg-6">
                            {{ Form::text('first_name', null, array ('class' => 'form-control')); }}
                            <label class="error">{{{ $errors->first('first_name') }}}</label>
                        </div>
                    </div>

                    <div class="form-group {{{ $errors->has('last_name') ? 'error' : '' }}}">
                        {{ Form::label('last_name', \Lang::get('webshopauthenticate::myaccount/form.edit-profile.last_name'), array('class' => 'col-lg-4 control-label required-icon')) }}
                        <div class="col-lg-6">
                            {{ Form::text('last_name', null, array ('class' => 'form-control')); }}
                            <label class="error">{{{ $errors->first('last_name') }}}</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-offset-4 col-lg-8">
                            <button type="submit" name="edit_personal" id="edit_personal" value="edit_personal" class="btn btn-success">{{ \Lang::get('webshopauthenticate::common.submit') }}</button>
                        </div>
                    </div>
                </fieldset>
            {{ Form::close() }}
        </div>
    </div>
@stop
@section('script_content')
	<script language="javascript" type="text/javascript">
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
			  "oldpasswordvalidate",
			  function(value, element) {
				var new_password = document.getElementById('password');
				var confirm_password = document.getElementById('password_confirmation');
				if((new_password.value != "" || confirm_password.value != "") && value == "")
					{
						return false;
					}
				else return true;
			  },
			 mes_required
		);

		jQuery.validator.addMethod(
			"newpasswordvalidate",
			function(value, element) {
			var old_password = document.getElementById('Oldpassword');
			if(old_password.value != "" && value == "")
				{
					return false;
				}
			else return true;
			},
			mes_required
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
			"{{ Lang::get('webshopauthenticate::auth/form.edit-profile.merchant_signup_specialchars_not_allowed') }}"
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
			"{{ Lang::get('webshopauthenticate::auth/form.edit-profile.merchant_signup_specialchars_not_allowed') }}"
		);
		jQuery.validator.addMethod(
				"chkSpecialCharsRepeatedTwice",
				function(value, element) {
				if(value!=""){
					value = value.trim();
					if ((/[,]{2}/.test(value)) || (/[&]{2}/.test(value)) || (/[-]{2}/.test(value)) || (/[ ]{2}/.test(value)) || (/[/(]{2}/.test(value)) || (/[/)]{2}/.test(value)) || (/[']{2}/.test(value)) || (/[//]{2}/.test(value)))
						return false;
					return true;
				}
				return true;
			},
			'{{ Lang::get('webshopauthenticate::auth/form.edit-profile.merchant_signup_twice_not_allowed') }}'
		);
		var mes_required = "{{ Lang::get('webshopauthenticate::auth/form.required') }}";
		$("#editaccount_frm").validate({
			rules: {
				new_email: {
					email: true
				},
				Oldpassword: {
					oldpasswordvalidate: true
				},
				password: {
					newpasswordvalidate: true,
					minlength: "{{ Config::get('webshopauthenticate::fieldlength_password_min') }}",
					maxlength: "{{ Config::get('webshopauthenticate::fieldlength_password_max') }}"
				},
				password_confirmation:{
					equalTo: "#password"
				}
			},
			messages: {
				Oldpassword: {
					oldpasswordvalidate: mes_required
				},
				password:{
					newpasswordvalidate: mes_required,
					minlength: jQuery.format("{{ Lang::get('webshopauthenticate::auth/form.register.validation_password_length_low') }}"),
					maxlength: jQuery.format("{{ Lang::get('webshopauthenticate::auth/form.register.validation_maxLength') }}")
				},
				password_confirmation:{
					required: mes_required,
					equalTo: "{{ Lang::get('webshopauthenticate::auth/form.register.validation_password_mismatch') }}"
				}
			}
		});

		$("#editpersonal_details_frm").validate({
			rules: {
				first_name: {
					required: true,
					minlength: "{{ Config::get('webshopauthenticate::fieldlength_name_min_length') }}",
					maxlength: "{{ Config::get('webshopauthenticate::fieldlength_name_max_length') }}",
					chkIsNameHasRestrictedWordsLike: true,
					chkIsNameHasRestrictedWordsExact: true,
					chkspecialchars: true,
				},
				last_name: {
					required: true,
					minlength: "{{ Config::get('webshopauthenticate::fieldlength_name_min_length') }}",
					maxlength: "{{ Config::get('webshopauthenticate::fieldlength_name_max_length') }}",
					chkIsNameHasRestrictedWordsLike: true,
					chkIsNameHasRestrictedWordsExact: true,
					chkspecialchars: true,
				}
			},
			messages: {
				first_name: {
					required: mes_required
				},
				last_name: {
					required: mes_required
				}
			},
			submitHandler: function(form) {
				form.submit();
			}
		});
    </script>
@stop