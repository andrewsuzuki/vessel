@extends('vessel::layout')

@section('content')

	<div class="row">
		
		<div class="col-lg-12">
			
			{{ Form::model($settings, array('route' => array('vessel.settings'), 'role' => 'form')) }}
			
			<div class="form-horizontal">
				<div class="row">
					<div class="col-md-7 col-sm-8">
						<div class="form-group">
							{{ Form::label('title', t('general.title'), array('class' => 'col-sm-4 control-label')) }}
							<div class="col-sm-8">
								{{ Form::text('title', null, array('class' => 'form-control input-sm')) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('description', t('settings.default-description'), array('class' => 'col-sm-4 control-label')) }}
							<div class="col-sm-8">
								{{ Form::textarea('description', null, array('class' => 'form-control input-sm', 'rows' => '2')) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('url', t('settings.base-url'), array('class' => 'col-sm-4 control-label')) }}
							<div class="col-sm-8">
								{{ Form::text('url', null, array('class' => 'form-control input-sm')) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('home', t('general.homepage'), array('class' => 'col-sm-4 control-label')) }}
							<div class="col-sm-8">
								{{ Form::select('home', $home_select_array, null, array('class' => 'form-control input-sm')) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('timezone', t('general.timezone'), array('class' => 'col-sm-4 control-label')) }}
							<div class="col-sm-8">
								{{ Form::select('timezone', $timezone_select_array, null, array('class' => 'form-control input-sm')) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('registration', t('settings.registration'), array('class' => 'col-sm-4 control-label')) }}
							<div class="col-sm-8">
								<div class="checkbox">
									<label>
										{{ Form::checkbox('registration', 'yes') }}
										{{ t('settings.allow-user-registration') }}
									</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('default_role', t('settings.default-role'), array('class' => 'col-sm-4 control-label')) }}
							<div class="col-sm-8">
								{{ Form::select('default_role', $role_select_array, null, array('class' => 'form-control input-sm')) }}
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

				<h2>{{ t('settings.theme-title') }}</h2>

				{{ Form::hidden('theme', null) }}

				<div class="row">

					@foreach ($themes as $name => $theme)

					<div class="col-sm-6 col-md-4">

						<div class="panel panel-default vessel-theme-choice">
							<div class="panel-heading">
								<h3 class="panel-title">{{ $theme['title'] }}</h3>
							</div>
							<div class="panel-body">
								{{ isset($theme['thumbnail']) ? '<p><img src="'.$theme['thumbnail'].'" alt="" class="img-responsive" /></p>' : '' }}
								{{ isset($theme['description']) ? '<p>'.$theme['description'].'</p>' : '' }}
								{{ isset($theme['author']) ? '<p><strong>'.t('general.author').':</strong> '.(isset($theme['author_url']) ? '<a href="'.$theme['author_url'].'" target="_blank">'.$theme['author'].'</a>' : $theme['author']).' '.(isset($theme['author_email']) ? '(<a href="mailto:'.$theme['author_email'].'">'.strtolower(t('general.email')).'</a>)' : '').'</p>' : '' }}
								{{ isset($theme['url']) ? '<p><strong>'.t('settings.theme-url').':</strong> <a href="'.$theme['url'].'" target="_blank">'.$theme['url'].'</a></p>' : '' }}

								<a href="#" class="btn btn-primary vessel-choose-theme" data-themename="{{ $name }}" role="button">{{ t('general.choose') }}</a>
							</div>
						</div>
					</div>

					@endforeach

				</div>

				<div class="clearfix mb-15"></div>
				{{ Form::submit(t('general.save'), array('class' => 'btn btn-success mb-15')) }}
			</div>
		</div>

	</div>

@stop