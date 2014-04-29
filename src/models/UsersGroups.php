<?php namespace Agriya\Webshopauthenticate;
class UsersGroups extends CustomEloquent
{
    protected $table = "users_groups";
    public $timestamps = false;
    protected $primarykey = 'user_id';
    protected $table_fields = array("user_id", "group_id");
}