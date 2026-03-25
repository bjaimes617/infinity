<form route='clear-activity'>
@method('DELETE')
<button type='button' data-toggle='modal' class="dropdown-item" data-target ='#confirmDelete'
        data-title ='{{trans('laravel-logger.modals.clearLog.title')}}' 
        data-message="{{trans('laravel-logger.modals.clearLog.message')}}">
    {{trans('laravel-logger.dashboard.menu.clear')}}   
    <i class="fa fa-fw fa-trash" aria-hidden="true"></i></button>
</form>