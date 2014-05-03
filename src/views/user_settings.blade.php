@extends('vessel::layout')

@section('content')

	<div class="row">
		
		<div class="col-lg-12">
			
			{{ Form::model($user, array('route' => array('vessel.me'), 'role' => 'form')) }}
			
			<div class="form-horizontal">
				<div class="row">
					<div class="col-md-7 col-sm-8">
						<div class="form-group">
							{{ Form::label('username', t('general.username'), array('class' => 'col-sm-4 control-label')) }}
							<div class="col-sm-8">
								<p class="form-control-static">{{ $user->username }}</p>
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('email', t('general.email'), array('class' => 'col-sm-4 control-label')) }}
							<div class="col-sm-8">
								{{ Form::text('email', null, array('class' => 'form-control input-sm')) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('first_name', t('general.first-name'), array('class' => 'col-sm-4 control-label')) }}
							<div class="col-sm-8">
								{{ Form::text('first_name', null, array('class' => 'form-control input-sm')) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('last_name', t('general.last-name'), array('class' => 'col-sm-4 control-label')) }}
							<div class="col-sm-8">
								{{ Form::text('last_name', null, array('class' => 'form-control input-sm')) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('password', t('general.password'), array('class' => 'col-sm-4 control-label')) }}
							<div class="col-sm-8">
								{{ Form::password('password', array('class' => 'form-control input-sm')) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('password_confirmation', t('general.password-confirm'), array('class' => 'col-sm-4 control-label')) }}
							<div class="col-sm-8">
								{{ Form::password('password_confirmation', array('class' => 'form-control input-sm')) }}
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-4"></div>
							<div class="col-sm-8">
								<div class="clearfix mb-15"></div>
								{{ Form::submit(t('general.save'), array('class' => 'btn btn-success mb-15')) }}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

@stop