<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	 <meta name="keywords" content="<?php echo CHtml::encode($this->meta_keywords); ?>" />
	 <meta name="description" content="<?php echo CHtml::encode($this->meta_description); ?>" />	
	
	<meta name="viewport" content="initial-scale=1"/>
	<link rel="stylesheet" media="screen" href="/css/screen.css" >
        <link rel="stylesheet" href="/css/responsive.css" />
	<!--[if IE]><script src="/js/html5shiv.js"></script><![endif]-->
	<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-141497033-7"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-141497033-7');
</script>

<script data-ad-client="ca-pub-3243264408777652" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>

</head>
<body class="l">
<!-- BEGIN HEADER -->
<header>
	<a href="/" class="logo" title="Cars technical information"><span>Wheel</span> Offset</a>
	<!--<div class="search">
		<input type="text" placeholder="Search">
		<button type="submit" class="btn btn_search"></button>
	</div>-->
	<!--
	<a href="#" class="sign-in">Sign in</a>
	<a href="#" class="join">Join</a>
	-->
</header>

<nav>
	<ul class="menu-list">
	<?php $uri = Yii::app()->request->requestUri;?>
		<li <?=($uri=='/')?'class="is-active"':''?>><a href="/">All cars</a></li>
		<li <?=($uri=='/wheels.html')?'class="is-active"':''?>><a href="/wheels.html">Wheel Offsets</a></li>
	</ul>
</nav>

<ul class="breadcrumb">
	<?php foreach ($this->breadcrumbs as $url=>$item):?>
		<li>
			<?php 
			$anchor = '';
			$title = '';
			if (is_array($item)) {
				$anchor = $item['anchor'];
				if (isset($item['title']))
					$title = $item['title'];
			} else {
				$anchor = $item;
			}?>
		
			<?php if ($url != '#'):?>
				<a href="<?=$url?>" <?=!empty($title)?"title='{$title}'":""?> ><?=$anchor?></a><span>→</span>
			<?php else:?>
				<a href="#"><?=$anchor?></a>
			<?php endif;?>
		</li>
	<?php endforeach;?>
</ul>

<?php echo $content;?>

<!-- BEGIN FOOTER -->
<footer>
		<section class="footer__copyright"><br>

<br>		&copy <?=date('Y')?>

            <a rel="nofollow" href="/privacy-policy.html">Privacy policy</a>


                </section>
</footer>




<?php if (YII_DEBUG):?>
<div style="position:fixed;right:0;top:0;color:green;margin-right:5px;z-index:10000;">
  <?php $stat = Yii::app()->db->getStats();?>
  <?=$stat[0]?> / <?=round($stat[1],5)?>
</div> 
<?php endif;?>


</body>
</html>
