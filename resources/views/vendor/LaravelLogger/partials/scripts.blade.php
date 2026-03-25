 @if(config('LaravelLogger.enablejQueryCDN')) 
    <script type="text/javascript" src="{{ config('LaravelLogger.JQueryCDN') }}"></script>    
    <script src="{{asset('plugins/jquery-ui/jquery-ui.min.js')}}" type="text/javascript"></script>
    @endif
@if(config('LaravelLogger.enableBootstrapJsCDN'))
    <script type="text/javascript" src="{{ config('LaravelLogger.bootstrapJsCDN') }}"></script>
@endif  
<script type="text/javascript" src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
@if(config('LaravelLogger.enablePopperJsCDN'))
    <script type="text/javascript" src="{{ config('LaravelLogger.popperJsCDN') }}"></script>
@endif

@if(config('LaravelLogger.loggerDatatables'))
    @include('LaravelLogger::scripts.datatables')    
@endif

@include('LaravelLogger::scripts.add-title-attribute')