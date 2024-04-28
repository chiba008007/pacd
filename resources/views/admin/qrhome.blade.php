@section('content')
    @extends('layouts.qradmin')

    <link rel="stylesheet" href="/css/qr.css" />
    <link
    rel="apple-touch-icon"
    sizes="180x180"
    href="/img/qr/icons/apple-touch-icon.png"
    />
    <link rel="manifest" href="/qr/manifest.json" />
    <link rel="manifest" href="manifest.webmanifest" />
    <script
    async
    src="https://cdn.jsdelivr.net/npm/pwacompat@2.0.9/pwacompat.min.js"
    integrity="sha384-VcI6S+HIsE80FVM1jgbd6WDFhzKYA0PecD/LcIyMQpT4fMJdijBh0I7Iblaacawc"
    crossorigin="anonymous"
    ></script>



    <div class="uk-section-small">
        <div class="uk-container uk-container-large">


            <div class="reader">
                <video
                    id="js-video"
                    class="reader-video"
                    autoplay
                    playsinline
                ></video>
            </div>

            <div class="reticle">
                <div class="reticle-box"></div>
            </div>

            <div style="display: none">
                <canvas id="js-canvas"></canvas>
            </div>

            <div id="js-modal" class="modal-overlay">
                <div class="modal">
                    <div class="modal-cnt" style="position:relative;">
                        <span class="modal-title">読み取り結果</span>
                        <div style="text-align:center;color:red;">読み取り成功</div>
                        <input type="hidden"
                            id="js-result"
                            class="modal-result"
                            value=""
                            readonly
                        />
                    </div>
                    <a href="" id="js-link" class="modal-btn" >
                        受付実施
                    </a>
                    <button id="js-copy" class="modal-btn" target="_blank" style="display:none;">
                        コピー
                    </button>
                    <button type="button" id="js-modal-close" class="modal-btn">
                        閉じる
                    </button>
                </div>
            </div>

            <div id="js-unsupported" class="unsupported">
                <p class="unsupported-title">Sorry!</p>
                <p>Unsupported browser</p>
            </div>


        </div>

    </div>
    <script src="/js/qr/jsQR.js"></script>
    <script src="/js/qr/app.js"></script>
@endsection


