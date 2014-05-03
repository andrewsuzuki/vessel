@extends('vessel::layout')

@section('content')

	<ul class="nav nav-tabs tabs-from-url">
		<li class="active"><a href="#tab-users" data-toggle="tab">{{ t('users.main-title') }}</a></li>
		<li><a href="#tab-roles" data-toggle="tab">{{ t('users.roles-title') }}</a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane active" id="tab-users">
			<div class="clearfix mb-15"></div>

			<a href="{{ URL::route('vessel.users.new') }}" class="btn btn-primary">{{ t('users.new-button') }}</a>

			<div class="clearfix mb-15"></div>
			
			<table class="table table-bordered">

				<thead>
					<tr>
						<th>{{ t('general.username') }}</th>
						<th>{{ t('users.roles') }}</th>
						<th>{{ t('general.name') }}</th>
						<th>{{ t('general.email') }}</th>
						<th>{{ t('users.last-login') }}</th>
						<th>{{ t('general.actions') }}</th>
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
							{{ link_to_route('vessel.users.edit', t('general.edit'), array($user->id), array('class' => 'btn btn-xs btn-default')) }}
							{{ link_to_route('vessel.users.delete', t('general.delete'), array($user->id), array('class' => 'btn btn-xs btn-danger')) }}
						</td>
					</tr>

					@endforeach

				</tbody>

			</table>
		</div>
		<div class="tab-pane" id="tab-roles">
			<div class="clearfix mb-15"></div>

			<a href="{{ URL::route('vessel.users.roles.new') }}" class="btn btn-primary">{{ t('users.new-role-button') }}</a>

			<div class="clearfix mb-15"></div>
			
			<table class="table table-bordered">

				<thead>
					<tr>
						<th>{{ t('general.name') }}</th>
						<th>{{ t('general.actions') }}</th>
					</tr>
				</thead>

				<tbody>

					@foreach($roles as $role)

					<tr>
						<td>{{ link_to_route('vessel.users.roles.edit', $role->name, array($role->id)) }}</td>
						<td>
							{{ link_to_route('vessel.users.roles.edit', t('general.edit'), array($role->id), array('class' => 'btn btn-xs btn-default')) }}
							{{ link_to_route('vessel.users.roles.delete', t('general.delete'), array($role->id), array('class' => 'btn btn-xs btn-danger')) }}
						</td>
					</tr>

					@endforeach

				</tbody>

			</table>
		</div>
	</div>


@stop