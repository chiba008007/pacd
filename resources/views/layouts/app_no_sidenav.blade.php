{{-- head, topnav --}}
@include('elements.header')

    {{-- main content --}}
    <main class="content">
        @yield('content')
    </main>

    {{-- footer --}}
    @include('elements.footer')

</body>

</html>
