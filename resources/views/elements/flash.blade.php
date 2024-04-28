@if (session()->has('flash.error'))
    <script>
        UIkit.notification({
            message: '{!! session()->pull('flash.error') !!}',
            status: 'danger',
            pos: 'top-right',
            timeout: 5000
        });
    </script>
@endif

@if (session()->has('flash.success'))
    <script>
        UIkit.notification({
            message: '{!! session()->pull('flash.success') !!}',
            status: 'success',
            pos: 'top-right',
            timeout: 5000
        });
    </script>
@endif