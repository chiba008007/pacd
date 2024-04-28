{{-- head, topnav --}}
@include('elements.header')

    {{-- side nav --}}
    @include('elements.sidenav')

    {{-- main contents --}}
    <main class="content content-padder">
        @yield('content')
    </main>

    {{-- footer --}}
    <div class="content-padder">
        @include('elements.footer')
    </div>
</body>

</html>
