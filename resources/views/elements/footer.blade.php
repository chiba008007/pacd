<footer class="footer uk-continer bg-dark">
    <div class="uk-margin-medium-bottom uk-grid-small uk-width-3-4 uk-margin-auto" uk-grid>
        @if (Route::has('privacy'))
            <div class="uk-margin-auto"><a href="{{ route('privacy') }}" class="text-light uk-text-small">プライバシーポリシー</a></div>
        @endif
        <div class="uk-margin-auto"><a href="https://www.pacd.jp/law.html" target=_blank class="text-light uk-text-small">特定商取引法に基づく表記</a></div>
    </div>
    <div class="uk-text-right uk-text-meta">All Rights Reserved, Copyright (c) 2003, THE JAPAN SOCIETY FOR ANALYTICAL CHEMISTRY</div>
</footer>
<style type="text/css">
textarea::placeholder {
  color: red;
}

/* IE */
input:-ms-input-placeholder {
  color: red;
}

/* Edge */
textarea::-ms-input-placeholder {
  color: red;
}
.uk-textarea2{
    height:80px;
    max-width: 97%;
    width: 97%;
    border: 0 none;
    padding: 4px 10px;
    background: #fff;
    color: #666;
    border: 1px solid #e5e5e5;
    transition: 0.2s ease-in-out;
    transition-property: color, background-color, border;
}
</style>
