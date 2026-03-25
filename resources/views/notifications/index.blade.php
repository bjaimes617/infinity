<script src="{{asset('plugins/sweetalert2/sweetalert2.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/toastr/toastr.min.js')}}" type="text/javascript"></script>
<script>
function main() {
    return Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        //confirmButtonText :'<i class="fas fa-check-circle"></i>',
        timer: 10000,           
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
    });
};
</script>

@if(Session('success'))
<script>
    var Toastr = main();
    $(function () {
    Toastr.fire({        
    icon: 'success',
            title: "&nbsp;{{ Session::get('success')}}",
    });
    });</script>
@endif
@if(Session('error'))
<script>
    var Toastr = main();
    Toastr.fire({
    icon: 'error',
            title: "&nbsp; {{Session::get('error')}}",
    });</script>
@endif
@if(Session('info'))
<script>
    var Toastr = main();
    Toastr.fire({
    icon: 'info',
            title: "&nbsp; {{Session::get('info')}}",
    });</script>
@endif
@if(Session('warning'))
<script>
    var Toastr = main();
    Toastr.fire({
    icon: 'warning',
            title: "&nbsp; {{Session::get('warning')}}",
    });</script>
@endif
@if(Session('question'))
<script>
    var Toastr = main();
    Toastr.fire({
    icon: 'question',
            title: "&nbsp; {{Session::get('question')}}",
    });</script>
@endif

@if(Session('errorimports'))
<script>
    var msg = "";
    @foreach(Session::get('errorimports') as $error)
            msg += "&nbsp; {!!$error!!} <br />"
            @endforeach
            console.log(msg);
    var Toastr = main();
    Toastr.fire({
    icon: 'error',
            title: msg,
    });</script>
@endif

@if(count($errors) > 0)
<script>
    var msg = "";
    @foreach($errors->all() as $error)
            msg += "&nbsp; {!!$error!!} <br />"
            @endforeach


            var Toastr = main();
    Toastr.fire({
    icon: 'error',
            title: msg,
    });
</script>
@endif