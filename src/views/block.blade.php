@extends('vessel::layout')

@section('content')

	<div class="row">
		
		<div class="col-lg-12">
			
			{{ Form::model($page, array('route' => array('vessel.blocks.'.$mode, 'id' => $block->id), 'role' => 'form', 'class' => 'vessel-block-edit-form')) }}

			{{ Form::hidden('updated_at', null, array('class' => 'vessel-carry-field')) }}
			<input type="hidden" name="force_edit" class="vessel-carry-field" value="{{ Session::get('force_edit') }}">
			
			<div class="row">
				<div class="col-md-10 col-sm-9">
					<div class="form-group">
						{{ Form::text('title', null, array('class' => 'vessel-carry-field form-control input-lg', 'data-slugto' => '#slug', 'placeholder' => 'Title')) }}
					</div>
				</div>
				<div class="col-md-2 col-sm-3">
					<div class="form-group">
						{{ Form::select('formatter', \Hokeo\Vessel\Facades\Formatter::selectArray(), \Hokeo\Vessel\Facades\Formatter::formatter()->getName(), array('class' => 'vessel-carry-field vessel-select-formatter form-control input-lg')) }}
					</div>
				</div>
			</div>

		</div>

	</div>

@stop