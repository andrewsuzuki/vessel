@extends('vessel::layout_guest')

@section('content')

	<div class="row">
		
		<div class="col-lg-12">
			
			{{ Form::open(array('route' => array('vessel.register'), 'role' => 'form')) }}
			
			<div class="form-group">
				{{ Form::label('username', t('general.username')) }}
				{{ Form::text('username', null, array('class' => 'form-control')) }}
			</div>
			<div class="form-group">
				{{ Form::label('email', t('general.email')) }}
				{{ Form::text('email', null, array('class' => 'form-control')) }}
			</div>
			<div class="form-group">
				{{ Form::label('first_name', t('general.first-name')) }}
				{{ Form::text('first_name', null, array('class' => 'form-control')) }}
			</div>
			<div class="form-group">
				{{ Form::label('last_name', t('general.last-name')) }}
				{{ Form::text('last_name', null, array('class' => 'form-control')) }}
			</div>
			<div class="form-group">
				{{ Form::label('password', t('general.password')) }}
				{{ Form::password('password', array('class' => 'form-control')) }}
			</div>
			<div class="form-group">
				{{ Form::label('password_confirmation', t('general.password-confirm')) }}
				{{ Form::password('password_confirmation', array('class' => 'form-control')) }}
			</div>
			<div class="form-group">
				<div class="col-sm-4"></div>
					<div class="clearfix mb-15"></div>
					{{ Form::submit(t('general.save'), array('class' => 'btn btn-success mb-15')) }}
				</div>
			</div>
		</div>

	</div>

@stop