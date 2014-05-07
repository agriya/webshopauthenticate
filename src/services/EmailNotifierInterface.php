<?php
class EmailNotifierInterface implements NotifierInterface {
	public function notify($user, $data)
	{
	    \Mail::send($data['mail_template'], $data, function($m) use ($user, $data)
		{
		    $m->to($user->email, $user->first_name);
			$m->subject($data['subject']);
		});
	}
}
?>