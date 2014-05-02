@extends(Config::get('webshopauthenticate::package_admin_layout'))
@section('content')
    @if (Session::has('success_message') && Session::get('success_message') != "")
        <div class="alert alert-success">{{	Session::get('success_message') }}</div>
    @endif
    @if (Session::has('error_message') && Session::get('error_message') != "")
        <div class="alert alert-danger">{{	Session::get('error_message') }}</div>
    @else
        @if(isset($d_arr['error_msg']) && $d_arr['error_msg'] != '')
            <div class="message-navbar mb20">
            	<h1 class="blue bigger-150 admin-title">{{ $d_arr['pageTitle'] }}</h1>
            </div>
            <p class="alert alert-danger">{{ $d_arr['error_msg'] }}</p>
        @else
            <div class="message-navbar mb20 mt10">
                <a href="{{ URL::to(Config::get('webshopauthenticate::admin_uri')) }}" title="{{ \Lang::get('webshopauthenticate::common.back_to_list') }}" class="btn btn-info btn-xs pull-right"><i class="icon-chevron-left"></i> {{ \Lang::get('webshopauthenticate::common.back_to_list') }}</a>
                <h1 class="admin-title blue bigger-150">{{ $d_arr['pageTitle'] }}</h1>
            </div>
             {{ Form::model($user_details, [
                'method' => 'post',
                'id' => 'addMemberfrm', 'class' => 'form-horizontal form-request'
                ]) }}
                {{ Form::hidden('mode', $d_arr['mode'], array("id" => "mode")) }}
                {{ Form::hidden('user_id', $d_arr['user_id'], array("id" => "user_id")) }}
                <fieldset class="border-type1">
                	<div class="mb20">
                    	<div class="clearfix">
                            <h2 class="title-two">Basic details</h2>
                            <div class="form-group">
                                {{ Form::label('first_name', \Lang::get('webshopauthenticate::admin/addMember.first_name_label'), array('class' => 'control-label required-icon col-sm-2')) }}
                                <div class="col-sm-3">
                                    {{ Form::text('first_name', null, array('class' => 'form-control')) }}
                                    {{ $errors->first('first_name') }}
                                </div>
                            </div>
                            <div class="form-group">
                                {{ Form::label('last_name', \Lang::get('webshopauthenticate::admin/addMember.last_name_label'), array('class' => 'control-label required-icon col-sm-2')) }}
                                <div class="col-sm-3">
                                    {{ Form::text('last_name', null, array('class' => 'form-control')) }}
                                    {{ $errors->first('last_name') }}
                                </div>
                            </div>
                            <div class="form-group">
                                {{ Form::label('email', \Lang::get('webshopauthenticate::admin/addMember.email_label'), array('class' => 'control-label required-icon col-sm-2')) }}
                                <div class="col-sm-3">
                                    {{ Form::text('email',null,array('class' => 'form-control')) }}
                                    {{ $errors->first('email') }}
                                 </div>
                            </div>
                            <?php
                                $mandatory_class = ($d_arr['mode'] != 'edit') ? "required-icon" : "";
                            ?>
                            <div class="form-group">
                                {{ Form::label('password', \Lang::get('webshopauthenticate::admin/addMember.password_label'), array('class' => "control-label required-icon col-sm-2")) }}
                                <div class="col-sm-3">
                                    {{ Form::password('password',array('class' => 'form-control')) }}
                                    {{ $errors->first('password') }}
                                 </div>
                             </div>
                             <div class="form-group">
                                {{ Form::label('password_confirmation', \Lang::get('webshopauthenticate::admin/addMember.confirm_password_label'), array('class' => "control-label required-icon col-sm-2")) }}
                                <div class="col-sm-3">
                                    {{ Form::password('password_confirmation',array('class' => 'form-control')) }}
                                    {{ $errors->first('password_confirmation') }}
                                </div>
                             </div>
                         </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-5">
                            @if($d_arr['mode'] == 'edit')
                                <button type="submit" name="add_members" value="add_members" class="btn btn-success btn-sm"><i class="icon-ok bigger-110"></i> {{ \Lang::get('webshopauthenticate::common.update') }}</button>
                            @else
                                <button type="submit" name="add_members" value="add_members" class="btn btn-success btn-sm"><i class="icon-ok bigger-110"></i> {{ \Lang::get('webshopauthenticate::common.submit') }}</button>
                            @endif
                            <button type="reset" name="reset_members" value="reset_members" class="btn btn-sm" onclick="javascript:location.href='{{ URL::to(Config::get('webshopauthenticate::admin_uri')) }}'"><i class="icon-undo bigger-110"></i> {{ \Lang::get('webshopauthenticate::common.cancel') }}</button>
                        </div>
                    </div>
                </fieldset>
            {{ Form::close() }}
         @endif
  @endif
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
	$("#addMemberfrm").validate({
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
			}
			@if($d_arr['mode'] == 'add')
				,
				"password": {
					required: true,
					minlength: "{{ Config::get('webshopauthenticate::fieldlength_password_min') }}",
					maxlength: "{{ Config::get('webshopauthenticate::fieldlength_password_max') }}"
				},
				"password_confirmation": {
					required: true,
					equalTo: "#password"
				}
			@else
				,
				"password": {
					 minlength:  {
					 	param: "{{ Config::get('webshopauthenticate::fieldlength_password_min') }}",
						depends: function (element) {
                 			   return $("#password").val() != "";
		                }
					 },
					 maxlength:  {
					 	param: "{{ Config::get('webshopauthenticate::fieldlength_password_max') }}",
						depends: function (element) {
                 			   return $("#password").val() != "";
		                }
					 }
				 },
				 "password_confirmation": {
				 	equalTo: {
						param: "#password",
						depends:  function (element) {
                 			   return $("#password").val() != "";
		                }
					}
				 }
			@endif
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
			}

			@if($d_arr['mode'] == 'add')
				,
				password: {
					required: mes_required,
					minlength: jQuery.format("{{ Lang::get('webshopauthenticate::auth/form.register.validation_password_length_low') }}"),
					maxlength: jQuery.format("{{ Lang::get('webshopauthenticate::auth/form.register.validation_maxLength') }}")
				},
				"password_confirmation": {
					required: mes_required,
					equalTo: "{{ Lang::get('webshopauthenticate::auth/form.register.validation_password_mismatch') }}"
				}
			@endif
		},
	    submitHandler: function(form) {
				form.submit();
		}
	});
</script>
@stop
