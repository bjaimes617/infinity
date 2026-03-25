@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
        © {{ date('Y') }} <a href="{{ENV('APP_WEB')}}" target="_blank">@lang('Directa Group C.A')</a> <br> {{ config('app.name') }} @lang('is Powered by Gerencia Ingeniería de Software <br> All rights reserved.')
        @endcomponent
    @endslot
@endcomponent
