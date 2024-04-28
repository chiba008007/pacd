<style type="text/css">
    ul.w80{
        width:720px;
        height:80px;
        margin:0 auto;
        position:relative;
    }

    ul.w80 li img{
        width:230px;
        height:60px;
    }
    .slick-prev{
        position:absolute;
        top:0;
        left:-40px;
        font-weight:bold;
        height:80px;
    }
    .slick-next{
        position:absolute;
        top:0px;
        right:-40px;
        left:auto;
        font-weight:bold;
        height:80px;

    }
    .slick-next i,
    .slick-prev i{
        margin-top:30px;
    }
</style>
<?php $loop=3 ;?>
@if(isset($bannar) && count($bannar) > 0 )
<ul class="bannar_slider w80" >
<?php foreach($bannar as $key=>$value): ?>
<li><a href="<?=$value->url?>" target=_blank><img src="<?=asset('storage/bannar/'.$value->filename)?>"  /></a></li>
<?php endforeach; ?>
</ul>
<?php if(count($bannar) <= 4){ $loop=1;} ?>
@endif
<?php if(empty($smooth)){$smooth = 0;} ?>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
  <script type="text/javascript">
    $('.bannar_slider').slick({
		autoplay: true,//自動的に動き出すか。初期値はfalse。
        autoplaySpeed:{{$smooth}},
		infinite: true,//スライドをループさせるかどうか。初期値はtrue。
		slidesToShow: 3,//スライドを画面に3枚見せる
		slidesToScroll: {{$loop}},//1回のスクロールで3枚の写真を移動して見せる
		prevArrow: '<div class="slick-prev" ><i uk-icon="chevron-left"></i></div>',//矢印部分PreviewのHTMLを変更
		nextArrow: '<div class="slick-next"><i uk-icon="chevron-right"></i></div>',//矢印部分NextのHTMLを変更
		dots: false,//下部ドットナビゲーションの表示
        pauseOnFocus: false,
		responsive: [
		{
			breakpoint: 769,//モニターの横幅が769px以下の見せ方
			settings: {
				slidesToShow: 2,//スライドを画面に2枚見せる
				slidesToScroll: 2,//1回のスクロールで2枚の写真を移動して見せる
			}
		},
		{
			breakpoint: 426,//モニターの横幅が426px以下の見せ方
			settings: {
				slidesToShow: 1,//スライドを画面に1枚見せる
				slidesToScroll: 1,//1回のスクロールで1枚の写真を移動して見せる
			}
		}
	    ]
	});
  </script>
