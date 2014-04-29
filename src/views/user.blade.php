@extends('vessel::layout')

@section('content')

	<div class="row">
		
		<div class="col-lg-12">
			
			{{ Form::model($user, array('route' => array('vessel.users.edit'), 'role' => 'form')) }}
			
			<div class="form-horizontal">
				<div class="row">
					<div class="col-md-7 col-sm-8">
						<div class="form-group">
							{{ Form::label('username', 'Username', array('class' => 'col-sm-4 control-label')) }}
							<div class="col-sm-8">
								@if ($mode == 'new')
								{{ Form::text('username', null, array('class' => 'form-control input-sm')) }}
								@else
								<p class="form-control-static">{{ $user->username }}</p>
								@endif
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('email', 'Email', array('class' => 'col-sm-4 control-label')) }}
							<div class="col-sm-8">
								{{ Form::text('email', null, array('class' => 'form-control input-sm')) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('first_name', 'First name', array('class' => 'col-sm-4 control-label')) }}
							<div class="col-sm-8">
								{{ Form::text('first_name', null, array('class' => 'form-control input-sm')) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('last_name', 'Last name', array('class' => 'col-sm-4 control-label')) }}
							<div class="col-sm-8">
								{{ Form::text('last_name', null, array('class' => 'form-control input-sm')) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('password', 'Password', array('class' => 'col-sm-4 control-label')) }}
							<div class="col-sm-8">
								{{ Form::password('password', array('class' => 'form-control input-sm')) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('password_confirmation', 'Password (Confirm)', array('class' => 'col-sm-4 control-label')) }}
							<div class="col-sm-8">
								{{ Form::password('password_confirmation', array('class' => 'form-control input-sm')) }}
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-4"></div>
							<div class="col-sm-8">
								<div class="clearfix mb-15"></div>
								{{ Form::submit('Save', array('class' => 'btn btn-success mb-15')) }}
								@if ($mode == 'edit')
								<a href="{{ URL::route('vessel.users.delete', array('id' => $user->id)) }}" class="btn btn-danger mb-15">Delete</a>
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

@stop