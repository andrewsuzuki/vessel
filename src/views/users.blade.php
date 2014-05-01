@extends('vessel::layout')

@section('content')

	<ul class="nav nav-tabs tabs-from-url">
		<li class="active"><a href="#tab-users" data-toggle="tab">Users</a></li>
		<li><a href="#tab-roles" data-toggle="tab">Roles / Permissions</a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane active" id="tab-users">
			<div class="clearfix mb-15"></div>

			<a href="{{ URL::route('vessel.users.new') }}" class="btn btn-primary">New User</a>

			<div class="clearfix mb-15"></div>
			
			<table class="table table-bordered">

				<thead>
					<tr>
						<th>Username</th>
						<th>Roles</th>
						<th>Name</th>
						<th>Email</th>
						<th>Last Login</th>
						<th>Actions</th>
					</tr>
				</thead>

				<tbody>

					@foreach($users as $user)

					<tr>
						<td>{{ link_to_route('vessel.users.edit', $user->username, array($user->id)) }}</td>
						<td>{{ $user->getRolesString() }}</td>
						<td>{{ $user->first_name.' '.$user->last_name }}</td>
						<td>{{ $user->email }}</td>
						<td>{{ ($user->last_login->toDateTimeString() == '-0001-11-30 00:00:00') ? 'n/a' : $user->last_login->user() }}</td>
						<td>
							{{ link_to_route('vessel.users.edit', 'Edit', array($user->id), array('class' => 'btn btn-xs btn-default')) }}
							{{ link_to_route('vessel.users.delete', 'Delete', array($user->id), array('class' => 'btn btn-xs btn-danger')) }}
						</td>
					</tr>

					@endforeach

				</tbody>

			</table>
		</div>
		<div class="tab-pane" id="tab-roles">
			<div class="clearfix mb-15"></div>

			<a href="{{ URL::route('vessel.users.roles.new') }}" class="btn btn-primary">New Role</a>

			<div class="clearfix mb-15"></div>
			
			<table class="table table-bordered">

				<thead>
					<tr>
						<th>Name</th>
						<th>Actions</th>
					</tr>
				</thead>

				<tbody>

					@foreach($roles as $role)

					<tr>
						<td>{{ link_to_route('vessel.users.roles.edit', $role->name, array($role->id)) }}</td>
						<td>
							{{ link_to_route('vessel.users.roles.edit', 'Edit', array($role->id), array('class' => 'btn btn-xs btn-default')) }}
							{{ link_to_route('vessel.users.roles.delete', 'Delete', array($role->id), array('class' => 'btn btn-xs btn-danger')) }}
						</td>
					</tr>

					@endforeach

				</tbody>

			</table>
		</div>
	</div>


@stop