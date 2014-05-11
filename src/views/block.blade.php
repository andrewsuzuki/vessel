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
						{{ Form::text('title', null, array('class' => 'vessel-carry-field form-control input-lg', 'data-slugto' => '#slug', 'placeholder' => t('general.title'))) }}
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
							{{ Form::label('slug', t('general.slug'), array('class' => 'col-sm-3 control-label')) }}
							<div class="col-sm-5 col-md-6">
								{{ Form::text('slug', null, array('class' => 'vessel-carry-field form-control input-sm')) }}
							</div>
							<div class="col-sm-4 col-md-3">
								<div class="checkbox" style="padding-top:5px">
									<label>
										{{ Form::checkbox('active', '1', true, array('class' => 'vessel-carry-field')) }} {{ t('general.active') }}
									</label>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							{{ Form::label('description', t('general.description'), array('class' => 'col-sm-3 control-label')) }}
							<div class="col-sm-9">
								{{ Form::text('description', null, array('class' => 'vessel-carry-field form-control input-sm')) }}
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="clearfix mb-15"></div>

			{{ Form::submit(t('general.save'), array('class' => 'btn btn-success mb-15')) }}
			@if ($mode == 'edit')
			{{ link_to_route('vessel.blocks.delete', t('general.delete'), array($block->id), array('class' => 'btn btn-danger mb-15')) }}
			@endif
		</div>

	</div>

@stop