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

	/**
	 * Date mutators
	 * 
	 * @return array Fields to convert to Carbon instances
	 */
	public function getDates()
	{
		return array('created_at', 'updated_at', 'last_login');
	}

	// Events

	public static function boot()
	{
		parent::boot();

		static::deleting(function($user) {
			// don't allow delete if user is self
			if ($user->id == \Auth::user()->id) return false;

			// unsync roles
			$user->roles()->sync(array());

			return true;
		});
	}
	
	/**
	 * Validation rules
	 * 
	 * @param  string $mode 'new' or 'edit'
	 * @param  bool   $self If user is editing self (/me)
	 * @param  object $user User model (when editing an existing user)
	 * @return array        Validation rules array
	 */
	public static function rules($mode = 'edit', $self = false, $user = null)
	{
		$base = array(
			'email' => 'required|email|unique:vessel_users,email'.(($mode == 'edit') ? ','.$user->id : ''),
			'first_name' => '',
			'last_name' => '',
			'password' => 'min:6|confirmed',
		);

		if (!$self)
		{
			$base['user_roles'] = 'required|array|roles';
		}

		if ($mode == 'new')
		{
			$base['username'] = 'required|unique:vessel_users';
			$base['password'] = 'required|'.$base['password'];
		}

		return $base;
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
	 * Get the token value for the "remember me" session.
	 *
	 * @return string
	 */
	public function getRememberToken()
	{
		return $this->remember_token;
	}

	/**
	 * Set the token value for the "remember me" session.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}

	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName()
	{
		return 'remember_token';
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