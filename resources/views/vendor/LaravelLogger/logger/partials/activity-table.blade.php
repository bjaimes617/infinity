@php

$drilldownStatus = config('LaravelLogger.enableDrillDown');
$prependUrl = '/activity/log/';

if (isset($hoverable) && $hoverable === true) {
    $hoverable = true;
} else {
    $hoverable = false;
}

if (Request::is('activity/cleared')) {
    $prependUrl = '/activity/cleared/log/';
}

@endphp

<div class="table-responsive activity-table">
    <table class="table table-striped table-condensed table-sm @if(config('LaravelLogger.enableDrillDown') && $hoverable) table-hover @endif data-table" style="width: 100%;">
        <thead>
            <tr>
                <th>
                    <i class="fa fa-database fa-fw" aria-hidden="true"></i>
                    <span class="hidden-sm hidden-xs">
                        {!! trans('laravel-logger.dashboard.labels.id') !!}
                    </span>
                </th>
                <th>
                   <i class="far fa-clock" aria-hidden="true"></i>
                    {!! trans('laravel-logger.dashboard.labels.time') !!}
                </th>
                <th>
                    <i class="fas fa-file-alt" aria-hidden="true"></i>
                    {!! trans('laravel-logger.dashboard.labels.description') !!}
                </th>
                <th>
                    <i class="fa fa-user" aria-hidden="true"></i>
                    {!! trans('laravel-logger.dashboard.labels.user') !!}
                </th>
                <th>
                    <i class="fa fa-truck fa-fw" aria-hidden="true"></i>
                    <span class="hidden-sm hidden-xs">
                        {!! trans('laravel-logger.dashboard.labels.method') !!}
                    </span>
                </th>
                <th>
                   <i class="fas fa-road" aria-hidden="true"></i>
                    {!! trans('laravel-logger.dashboard.labels.route') !!}
                </th>
                <th>
                    <i class="fa fa-map-marker fa-fw" aria-hidden="true"></i>
                    {!! trans('laravel-logger.dashboard.labels.ipAddress') !!}
                </th>
                <th>
                    <i class="fa fa-laptop fa-fw" aria-hidden="true"></i>
                    {!! trans('laravel-logger.dashboard.labels.agent') !!}
                </th>
                @if(Request::is('activity/cleared'))
                    <th>
                        <i class="fa fa-trash-o fa-fw" aria-hidden="true"></i>
                        {!! trans('laravel-logger.dashboard.labels.deleteDate') !!}
                    </th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($activities as $activity)            
                <tr @if($drilldownStatus && $hoverable) class="clickable-row" data-href="{{ url($prependUrl . $activity->id) }}" data-toggle="tooltip" title="{{trans('laravel-logger.tooltips.viewRecord')}}" @endif >
                    <td>
                        <small>
                            @if($hoverable)
                                {{ $activity->id }}
                            @else
                                <a href="{{ url($prependUrl . $activity->id) }}">
                                    {{ $activity->id }}
                                </a>
                            @endif
                        </small>
                    </td>
                    <td title="{{ $activity->created_at }}">
                        {{ $activity->timePassed }}
                    </td>
                    <td>
                        {{ $activity->description }}
                    </td>
                    <td>
                        @php
                            switch ($activity->userType) {
                                case trans('laravel-logger.userTypes.registered'):
                                    $userTypeClass = 'success';
                                    $userLabel = $activity->userDetails['name'];
                                    break;

                                case trans('laravel-logger.userTypes.crawler'):
                                    $userTypeClass = 'danger';
                                    $userLabel = $activity->userType;
                                    break;

                                case trans('laravel-logger.userTypes.guest'):
                                default:
                                    $userTypeClass = 'warning';
                                    $userLabel = $activity->userType;
                                    break;
                            }

                        @endphp
                        <span class="badge badge-{{$userTypeClass}}">
                            {{$userLabel}}
                        </span>
                    </td>
                    <td>
                        @php
                            switch (strtolower($activity->methodType)) {
                                case 'get':
                                    $methodClass = 'info';
                                    break;

                                case 'post':
                                    $methodClass = 'warning';
                                    break;

                                case 'put':
                                    $methodClass = 'warning';
                                    break;

                                case 'delete':
                                    $methodClass = 'danger';
                                    break;

                                default:
                                    $methodClass = 'info';
                                    break;
                            }
                        @endphp
                        <span class="badge badge-{{ $methodClass }}">
                            {{ $activity->methodType }}
                        </span>
                    </td>
                    <td>
                        @if($hoverable)
                            {{ showCleanRoutUrl($activity->route) }}
                        @else
                            <a href="{{ $activity->route }}">
                                {{$activity->route}}
                            </a>
                        @endif
                    </td>
                    <td>
                        {{ $activity->ipAddress }}
                    </td>
                    <td>                        
                        @php
                            $platform       = $activity->userAgentDetails['platform'];
                            $browser        = $activity->userAgentDetails['browser'];
                            $browserVersion = $activity->userAgentDetails['version'];                           

                            switch ($platform) {

                                case 'Windows':
                                    $platformIcon = 'fab fa-windows';
                                    break;
                                case 'iPad':
                                    $platformIcon = 'fas fa-tablet-alt';
                                    break;
                                case 'iPhone':
                                    $platformIcon = 'fas fa-mobile-alt';
                                    break;
                                case 'Macintosh':
                                    $platformIcon = 'fas fa-apple-alt';
                                    break;
                                case 'Android':
                                    $platformIcon = 'fab fa-android';
                                    break;
                                case 'BlackBerry':
                                    $platformIcon = 'fab fa-blackberry';
                                    break;
                                case 'Unix':                               
                                    $platformIcon = 'fab fa-unity';
                                    break;
                                case 'Linux':
                                    $platformIcon = 'fab fa-linux';
                                    break;
                                case 'CrOS':
                                    $platformIcon = 'fab fa-chrome';
                                    break;
                                case 'X11':
                                    $platformIcon = 'fab fa-linux';
                                    break;
                                default:
                                    $platformIcon = 'fas fa-exclamation-circle';
                                    break;
                            }

                            switch ($browser) {
                                case 'Chrome':
                                    $browserIcon  = 'fab fa-chrome';
                                    break;

                                case 'Firefox':
                                    $browserIcon  = 'fab fa-firefox';
                                    break;

                                case 'Opera':
                                    $browserIcon  = 'fab fa-opera';
                                    break;

                                case 'Safari':
                                    $browserIcon  = 'fab fa-safari';
                                    break;

                                case 'Internet Explorer':
                                    $browserIcon  = 'fab fa-edge';
                                    break;

                                default:
                                    $browserIcon  = 'fab fa-chrome';
                                    break;
                            }
                            
                            switch ($activity->langDetails) {
                                case 'es_419':
                                    $langIdm  = 'ES';
                                    break;
                                default:
                                    $langIdm  = 'ES';
                                    break;
                            }
                        @endphp
                        <i class="{{ $browserIcon }}" aria-hidden="true">
                            <span class="sr-only">
                                {{ $browser }}
                            </span>
                        </i>
                        <sup>
                            <small>
                                {{ $browserVersion }}
                            </small>
                        </sup>
                        <i class="{{ $platformIcon }}" aria-hidden="true">
                            <span class="sr-only">
                                {{ $platform }}
                            </span>
                        </i>
                        <sup>
                            <small>
                                {{ $langIdm }}
                            </small>
                        </sup>
                    </td>
                    @if(Request::is('activity/cleared'))
                        <td>
                            {{$langIdm}}
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if(config('LaravelLogger.loggerPaginationEnabled'))
    <div class="text-center">
        <div class="d-flex justify-content-center">
            {!! $activities->render() !!}
        </div>
        <p>
            {!! trans('laravel-logger.pagination.countText', ['firstItem' => $activities->firstItem(), 'lastItem' => $activities->lastItem(), 'total' => $activities->total(), 'perPage' => $activities->perPage()]) !!}
        </p>
    </div>
@endif