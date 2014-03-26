<?php namespace Hokeo\Vessel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Model implements UserInterface, RemindableInterface {

	protected $table = 'vessel_users';

	protected $softDelete = false;

	use DateAccessorTrait;
	
	public static function rules($edit = null)
	{
		return [
			'title' => 'required',
			'slug' => 'required|alpha_dash|unique:vessel_pages,slug'.(($edit) ? ','.$edit->id : '')
		];
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