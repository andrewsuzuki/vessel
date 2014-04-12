@extends('vessel::layout')

@section('content')

	<div class="row">
		
		<div class="col-lg-12">
			
			{{ Form::model($settings, array('route' => array('vessel.settings'), 'role' => 'form')) }}
			
			<div class="form-horizontal">
				<div class="row">
					<div class="col-md-7 col-sm-8">
						<div class="form-group">
							{{ Form::label('title', 'Title', array('class' => 'col-sm-4 control-label')) }}
							<div class="col-sm-8">
								{{ Form::text('title', null, array('class' => 'form-control input-sm')) }}
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="clearfix mb-15"></div>

			{{ Form::submit('Save', array('class' => 'btn btn-success mb-15')) }}
		</div>

	</div>

@stop