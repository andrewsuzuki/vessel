<?php namespace Hokeo\Vessel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Zizaco\Entrust\HasRole;

class User extends Model implements UserInterface, RemindableInterface {

	protected $table = 'vessel_users';

	protected $softDelete = false;

	use DateAccessorTrait;

	use HasRole;
	
	public static function rules($mode = 'user')
	{
		if ($mode == 'admin') // user admin
		{
			// todo
		}
		else // user settings
		{
			return [
				'email' => 'required|email',
				'first_name' => '',
				'last_name' => '',
				'password' => 'min:6|confirmed',
			];
		}
	}

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}
}