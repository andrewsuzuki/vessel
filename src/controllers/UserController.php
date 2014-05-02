<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\View\Environment;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthManager;
use Illuminate\Validation\Factory;
use Illuminate\Hashing\HasherInterface;
use Illuminate\Routing\Redirector;
use Krucas\Notification\Notification;

class UserController extends Controller {

	protected $view;

	protected $input;

	protected $auth;

	protected $validator;

	protected $hash;

	protected $redirect;

	protected $notification;

	protected $t;

	protected $user; // model

	protected $role; // model

	public function __construct(
		Environment $view,
		Request $input,
		AuthManager $auth,
		Factory $validator,
		HasherInterface $hash,
		Redirector $redirect,
		Notification $notification,
		User $user,
		Role $role,
		Permission $permission)
	{
		$this->view         = $view;
		$this->input        = $input;
		$this->auth         = $auth;
		$this->validator    = $validator;
		$this->hash         = $hash;
		$this->redirect     = $redirect;
		$this->notification = $notification;
		$this->user         = $user;
		$this->role         = $role;
		$this->permission   = $permission;
	}

	/**
	 * Get user's settings page
	 * 
	 * @return response
	 */
	public function getMe()
	{
		$user = $this->auth->user();
		$this->view->share('title', 'User Settings');
		return $this->view->make('vessel::user_settings')->with(compact('user'));
	}

	/**
	 * Handle user setting saving
	 * 
	 * @return response
	 */
	public function postMe()
	{
		// get current authenticated user
		$user = $this->auth->user();

		$rules = $this->user->rules('edit', true, $user); // get validation rules (for user settings editing)
		$validator = $this->validator->make($this->input->all(), $rules); // validate input

		if ($validator->fails())
		{
			// redirect back with error and input
			$this->notification->error($validator->messages()->first());
			return $this->redirect->back()->withInput();
		}

		// save settings
		
		$user->email      = $this->input->get('email');
		$user->first_name = $this->input->get('first_name');
		$user->last_name  = $this->input->get('last_name');

		// if a new password was entered, save it
		if ($this->input->get('password'))
			$user->password = $this->hash->make($this->input->get('password'));

		$user->save();
		
		$this->notification->success(t('messages.general.save-success-p', array('name' => 'Your user settings')));
		return $this->redirect->route('vessel.me');
	}

	/**
	 * Get Users page
	 * 
	 * @return response
	 */
	public function getUsers()
	{
		$users = $this->user->all();
		$roles = $this->role->all();
		$this->view->share('title', 'Users');
		return $this->view->make('vessel::users')->with(compact('users', 'roles'));
	}

	/**
	 * Get new/edit user page
	 * 
	 * @return response
	 */
	public function getUser($id = null)
	{
		if ($id === null)
		{
			$mode = 'new';
			$user = $this->user->newInstance(); // new user
			$this->view->share('title', 'New User');
		}
		elseif (!($user = $this->user->find($id))) // find existing user
		{
			throw new \VesselBackNotFoundException; // 404 if not found
		}
		else
		{
			$mode = 'edit';
			$this->view->share('title', 'Edit User '.$user->username);
		}

		$user_is_self = $user->id == $this->auth->user()->id;

		$roles = $this->role->all(); // get roles

		return $this->view->make('vessel::user')->with(compact('user', 'roles', 'user_is_self', 'mode'));
	}

	/**
	 * Handle post to user edit page
	 * 
	 * @return response
	 */
	public function postUser($id = null)
	{
		if ($id === null)
		{
			$mode = 'new';
			$user = $this->user->newInstance(); // new user
		}
		elseif (!($user = $this->user->find($id))) // find existing user
		{
			throw new \VesselBackNotFoundException; // 404 if not found
		}
		else
		{
			$mode = 'edit';
		}

		$user_is_self = $user->id == $this->auth->user()->id;

		$rules     = $this->user->rules($mode, $user_is_self, $user); // get validation rules (for user settings editing)
		$validator = $this->validator->make($this->input->all(), $rules); // validate input

		if ($validator->fails())
		{
			// redirect back with error and input
			$this->notification->error($validator->messages()->first());
			return $this->redirect->back()->withInput();
		}

		// save settings
		
		// if new user, save username
		if ($id === null)
			$user->username = $this->input->get('username');

		$user->email      = $this->input->get('email');
		$user->first_name = $this->input->get('first_name');
		$user->last_name  = $this->input->get('last_name');

		// if a new password was entered, save it
		if ($this->input->get('password'))
			$user->password = $this->hash->make($this->input->get('password'));

		$user->save();
		
		// sync roles if user isn't self
		if (!$user_is_self)
			$user->roles()->sync($this->input->get('user_roles'));

		// notification
		$this->notification->success(t('messages.users.edit.saved'));
		// redirect
		return $this->redirect->route('vessel.users.edit', array('id' => $user->id));
	}

	/**
	 * Delete a user
	 *
	 * @param  int|string $id ID of user to delete
	 * @return object         redirect response
	 */
	public function getUserDelete($id)
	{
		$user = $this->user->find($id); // get user
		if (!$user) throw new \VesselBackNotFoundException;

		if ($user->delete()) // delete user
			$this->notification->success(t('messages.users.edit.deleted'));
		else
			$this->notification->error(t('messages.users.edit.deleted-error'));

		return $this->redirect->route('vessel.users');
	}

	/**
	 * Get new/edit role page
	 * 
	 * @return response
	 */
	public function getRole($id = null)
	{
		if ($id === null)
		{
			$mode = 'new';
			$role = $this->role->newInstance(); // new role
			$this->view->share('title', 'New Role');
		}
		elseif (!($role = $this->role->find($id))) // find existing role
		{
			throw new \VesselBackNotFoundException; // 404 if not found
		}
		else
		{
			$mode = 'edit';
			$this->view->share('title', 'Edit Role '.$role->name);
		}

		$role_is_native = in_array($role->name, $this->role->getNative());

		$permissions = $this->permission->all(); // get permissions
		$role_permissions = $role->permissions()->getRelatedIds(); // get role's current permissions

		return $this->view->make('vessel::role')->with(compact('role', 'permissions', 'role_permissions', 'role_is_native', 'mode'));
	}

	/**
	 * Handle post to role edit page
	 * 
	 * @return response
	 */
	public function postRole($id = null)
	{
		if ($id === null)
		{
			$mode       = 'new';
			$role       = $this->role->newInstance(); // new role
			$rulesinput = null;
		}
		elseif (!($role = $this->role->find($id))) // find existing role
		{
			throw new \VesselBackNotFoundException; // 404 if not found
		}
		else
		{
			$mode       = 'edit';
			$rulesinput = $role;
		}

		$rules     = $this->role->rules($rulesinput); // get validation rules
		$validator = $this->validator->make($this->input->all(), $rules); // validate input

		if ($validator->fails())
		{
			// redirect back with error and input
			$this->notification->error($validator->messages()->first());
			return $this->redirect->back()->withInput();
		}

		// save
		if (!in_array($role->name, $this->role->getNative()))
			$role->name = $this->input->get('name');
		$role->save();
		$role->permissions()->sync($this->input->get('role_permissions')); // sync permissions
		
		// notification
		$this->notification->success(t('messages.general.save-success', array('name' => 'Role')));
		// redirect
		return $this->redirect->route('vessel.users.roles.edit', array('id' => $role->id));
	}

	/**
	 * Delete a role
	 *
	 * @param  int|string $id ID of role to delete
	 * @return object         redirect response
	 */
	public function getRoleDelete($id)
	{
		$role = $this->role->find($id); // get role
		if (!$role) throw new \VesselBackNotFoundException;

		if ($role->delete()) // delete role
			$this->notification->success(t('messages.general.delete-success', array('name' => 'Role')));
		else // if role was native, error
			$this->notification->error(t('messages.roles.delete.error'));

		return $this->redirect->route('vessel.users', array('#tab-roles'));
	}
}