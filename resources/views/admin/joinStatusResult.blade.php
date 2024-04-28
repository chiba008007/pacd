
<html>
<head>

</head>
<body>
<input type="hidden" id="message" value="{{$message}}" />
<input type="hidden" id="href" value="{{ route('admin.qrhome') }}" />

<script>
    var msg = document.getElementById("message").value;
    alert(msg);
    var href = document.getElementById("href").value;
    location.href=href;
</script>


</body>

</html>



{{--  @section('content')
    <div class="uk-section-small">
        <div class="uk-container uk-container-large">
        @if($message)
            <div class="uk-alert-primary" uk-alert>
                <p>{{$message}}</p>
            </div>
        @else
            <div class="uk-alert-danger" uk-alert>
                <p>{{$error}}</p>
            </div>
        @endif
            <a href="{{ route('admin.qrhome') }}">QRコードリーダ</a>

        </div>
    </div>
@endsection  --}}


