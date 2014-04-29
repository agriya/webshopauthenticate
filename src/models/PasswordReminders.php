<?php namespace Agriya\Webshopauthenticate;
class PasswordReminders extends CustomEloquent
{
    protected $table = "password_reminders";
    public $timestamps = false;
    protected $primarykey = 'ip_id';
    protected $table_fields = array("email", "token", "created_at");

    public function addNew($data_arr)
	{
		$this->setFieldValues($data_arr);
		$this->save();
	}
}