{!! Form::open(array('route' => 'restore-activity', 'method' => 'POST', 'class' => 'mb-0')) !!}
    {!! Form::button('<i class="fa fa-fw fa-history" aria-hidden="true"></i>' . trans('laravel-logger.dashboardCleared.menu.restoreAll'), array('type' => 'button', 'class' => 'text-success dropdown-item', 'data-toggle' => 'modal', 'data-target' => '#confirmRestore', 'data-title' => trans('laravel-logger.modals.restoreLog.title'),'data-message' => trans('laravel-logger.modals.restoreLog.message'))) !!}
{!! Form::close() !!}
