@extends('vessel::layout')

@section('content')

	<div class="row">
		
		<div class="col-lg-12">
			
			{{ Form::model($block, array('route' => array('vessel.blocks.'.$mode, 'id' => $block->id), 'role' => 'form', 'class' => 'vessel-block-edit-form')) }}

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
						{{ Form::select('formatter', $formatters_select_array, $formatter_current, array('class' => 'vessel-carry-field vessel-select-formatter form-control input-lg')) }}
					</div>
				</div>
			</div>

			<div class="form-group">
				{{ $interface or '' }}
			</div>

			<div class="form-horizontal">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							{{ Form::label('slug', 'Slug', array('class' => 'col-sm-3 control-label')) }}
							<div class="col-sm-5 col-md-6">
								{{ Form::text('slug', null, array('class' => 'vessel-carry-field form-control input-sm')) }}
							</div>
							<div class="col-sm-4 col-md-3">
								<div class="checkbox" style="padding-top:5px">
									<label>
										{{ Form::checkbox('active', '1', true, array('class' => 'vessel-carry-field')) }} Active
									</label>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							{{ Form::label('description', 'Description', array('class' => 'col-sm-3 control-label')) }}
							<div class="col-sm-9">
								{{ Form::text('description', null, array('class' => 'vessel-carry-field form-control input-sm')) }}
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="clearfix mb-15"></div>

			{{ Form::submit('Save', array('class' => 'btn btn-success mb-15')) }}
			@if ($mode == 'edit')
			<a href="{{ URL::route('vessel.blocks.delete', array('id' => $block->id)) }}" class="btn btn-danger mb-15">Delete</a>
			@endif
		</div>

	</div>

@stop