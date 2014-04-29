@extends('vessel::layout')

@section('content')

	<a href="{{ URL::route('vessel.users.new') }}" class="btn btn-primary">New User</a>

	<div class="clearfix mb-15"></div>
	
	<table class="table table-bordered">

		<thead>
			<tr>
				<th>Username</th>
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
				<td>{{ $user->first_name.' '.$user->last_name }}</td>
				<td>{{ $user->email }}</td>
				<td>{{ $user->last_login->user() }}</td>
				<td>
					{{ link_to_route('vessel.users.edit', 'Edit', array($user->id), array('class' => 'btn btn-xs btn-default')) }}
					{{ link_to_route('vessel.users.delete', 'Delete', array($user->id), array('class' => 'btn btn-xs btn-danger')) }}
				</td>
			</tr>

			@endforeach

		</tbody>

	</table>

@stop