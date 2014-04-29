@extends('webshopauthenticate::base')
@section('content')
	<!-- Notifications -->
    @include('webshopauthenticate::notifications')

	<a href="{{ URL::to(Config::get('webshopauthenticate::admin_uri').'/users/add') }}" title="{{ \Lang::get('webshopauthenticate::admin/addMember.addmember_page_title') }}">{{ \Lang::get('webshopauthenticate::admin/addMember.addmember_page_title') }} </a>
    {{ Form::open(array('id'=>'MemberSearchfrm', 'method'=>'get','class' => 'form-horizontal form-request' )) }}
        <div class="widget-box transparent @if(!\Input::has('search_members'))collapsed @endif">
            <div class="widget-header widget-header-flat admin-searchbar">
                <div class="widget-toolbar">
                    <i class="icon-chevron-down"></i>
                    <span>{{ \Lang::get('webshopauthenticate::admin/manageMembers.memberlist_search_members') }} </span>
                </div>
            </div>
            <div class="widget-body">
                <div class="widget-main no-padding">
                    <div id="search_holder">
                        <div class="border-type1" id="selSrchBooking">
                            <div class="row">
                                <div class="clearfix">
                                    <fieldset class="col-sm-6">
                                        <div class="form-group">
                                            {{ Form::label('user_name', \Lang::get('webshopauthenticate::admin/manageMembers.memberlist_user_name'), array('class' => 'control-label col-sm-4')) }}
                                            <div class="col-sm-5">
                                                {{ Form::text('user_name', \Input::get("user_name"), array('class' => 'form-control')) }}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {{ Form::label('user_email', \Lang::get('webshopauthenticate::admin/manageMembers.memberlist_user_email'), array('class' => 'control-label col-sm-4')) }}
                                            <div class="col-sm-5">
                                                {{ Form::text('user_email', \Input::get("user_email"), array('class' => 'form-control')) }}
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="col-sm-offset-2 col-sm-5">
                                    <button type="submit" name="search_members" value="search_members" class="btn btn-info btn-sm"><i class="icon-ok bigger-110"></i> {{ \Lang::get('webshopauthenticate::common.search') }}</button>
                                    <button type="reset" name="reset_search" value="reset_search" class="btn btn-sm" onclick="javascript:location.href='{{ URL::to(Config::get('webshopauthenticate::admin_uri')) }}'"><i class="icon-undo bigger-110"></i> {{ \Lang::get('webshopauthenticate::common.reset') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
           </div>
        </div>
    {{ Form::close() }}

    <div class="mb40">
    	<h1 class="title-one">{{ Lang::get('webshopauthenticate::default.users_list') }}</h1>
    </div>
	<div>
		@if(sizeof($user_list) > 0 )
			<div class="panel panel-default stocklist-panel">
			   <div class="panel-body">
			        <table class='table table-striped'>
			            <tr>
			                <th>{{ \Lang::get('webshopauthenticate::default.name') }}</th>
			                <th>{{ \Lang::get('webshopauthenticate::default.email') }}</th>
			                <th>{{ \Lang::get('webshopauthenticate::default.status') }}</th>
			                <th>{{ \Lang::get('webshopauthenticate::default.date_added') }}</th>
			                <th>{{ \Lang::get('webshopauthenticate::default.action') }}</th>
			            </tr>
			            @foreach($user_list as $usr)
			            <tr>
			                <td>{{ $usr->first_name.' '.$usr->last_name }}</td>
			                <td>{{ $usr->email }}</td>
			                <td>{{ ($usr->activated) ? \Lang::get('webshopauthenticate::common.active') : \Lang::get('webshopauthenticate::common.inactive') }}</td>
			                <td>{{ $usr->created_at }}</td>
			                <td>
			                	<p>
									<a href="{{ URL::to(Config::get('webshopauthenticate::admin_uri').'/users/edit').'/'.$usr->id }}" title="{{ \Lang::get('webshopauthenticate::common.edit') }}">{{ \Lang::get('webshopauthenticate::common.edit') }} </a>
			                	</p>
                                @if($usr->activated)
                                    <a href="{{ URL::to(Config::get('webshopauthenticate::admin_uri').'/users/changestatus').'?action=deactivate&user_id='.$usr->id }}" class="fn_dialog_confirm red" action="De-Activate" title="{{ \Lang::get('webshopauthenticate::admin/manageMembers.memberlist_deactivate') }}">{{ \Lang::get('webshopauthenticate::admin/manageMembers.memberlist_deactivate') }} </a>
                                @else
                                    <a href="{{ URL::to(Config::get('webshopauthenticate::admin_uri').'/users/changestatus').'?action=activate&user_id='.$usr->id }}" class="fn_dialog_confirm green" action="Activate" title="{{ \Lang::get('webshopauthenticate::admin/manageMembers.memberlist_activate') }}">{{ \Lang::get('webshopauthenticate::admin/manageMembers.memberlist_activate') }} </a>
                                @endif
                            </td>
			           	</tr>
			            @endforeach
			         </table>
					<div class="pagination">
                		{{ $user_list->appends(array('user_name' => Input::get('user_name'), 'user_email' => Input::get('user_email'), 'status' => Input::get('status'), 'search_members' => Input::get('search_members')))->links() }}
            		</div>
			     </div>
			</div>
		@else
			{{ \Lang::get('webshopauthenticate::default.users_not_found') }}
		@endif
	</div>
	<div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
@stop
@section('script_content')
	<script type="text/javascript">
	var common_ok_label = "{{ \Lang::get('webshopauthenticate::common.yes') }}" ;
	var common_no_label = "{{ \Lang::get('webshopauthenticate::common.cancel') }}" ;
	var cfg_site_name = "Webshop" ;
	$(window).load(function(){
		  $(".fn_dialog_confirm").click(function(){
				var atag_href = $(this).attr("href");
				var action = $(this).attr("action");
				var cmsg = "";
				var txtDelete = action;

				var txtCancel = common_no_label;
				var buttonText = {};
				buttonText[txtDelete] = function(){
											Redirect2URL(atag_href);
											$( this ).dialog( "close" );
										};
				buttonText[txtCancel] = function(){
											$(this).dialog('close');
										};
				switch(action){
					case "Activate":
						cmsg = "Are you sure you want to activate this Member?";

						break;
					case "De-Activate":
						cmsg = "Are you sure you want to de-activate this Member?";
						break;
				}
				$("#fn_dialog_confirm_msg").html(cmsg);
				$("#fn_dialog_confirm_msg").dialog({
					resizable: false,
					height:140,
					width: 320,
					modal: true,
					title: cfg_site_name,
					buttons:buttonText
				});
				return false;
			});
		});
	</script>
@stop