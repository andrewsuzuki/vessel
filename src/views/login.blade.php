@extends('vessel::layout_guest')

@section('content')

	{{ Form::open(array('route' => array('vessel.login')), array('role' => 'form')) }}
		<fieldset>
			<div class="form-group">
				{{ Form::text('usernameemail', null, array('class' => 'form-control', 'placeholder' => t('general.username-or-email'))) }}
			</div>
			<div class="form-group">
				{{ Form::password('password', array('class' => 'form-control', 'placeholder' => t('general.password'))) }}
			</div>
			<div class="checkbox">
				<label>
					{{ Form::checkbox('remember') }} {{ t('general.remember-me') }}
				</label>
			</div>
			{{ Form::submit(t('general.log-in'), array('class' => 'btn btn-primary')) }}

			@if($registration_enabled)
			{{ link_to_route('vessel.register', t('general.register'), null, array('class' => 'btn btn-default')) }}
			
			@endif

		</fieldset>
	{{ Form::close() }}

@stop