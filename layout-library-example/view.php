<!doctype html>
<html lang="<?=$this->user->get('lang')?>" itemscope itemtype="http://schema.org/WebPage" class="no-js">
    <head>
        <title><?=$title_for_layout?></title>
        <meta content="<?=$description_for_layout?>" name="description">
        <meta content="<?=$keywords_for_layout?>" name="keywords">
        <meta content="<?=$title_for_layout?>" name="title">

        <meta charset="utf-8">
        <meta content="text/html; charset=UTF-8" http-equiv="content-type">
        <meta content="IE=edge" http-equiv="X-UA-Compatible">
        <link rel="alternate" hreflang="en-gb" href="http://www.examplesite.co.uk<?=$_SERVER['REQUEST_URI']?>">
        <link rel="alternate" hreflang="it-it" href="http://www.examplesite.it<?=$_SERVER['REQUEST_URI']?>">
        <link rel="alternate" hreflang="ru-ru" href="http://www.examplesite.ru<?=$_SERVER['REQUEST_URI']?>">
        <link rel="alternate" hreflang="fr-fr" href="http://www.examplesite.fr<?=$_SERVER['REQUEST_URI']?>">
        <link rel="alternate" href="http://www.examplesite.com<?=$_SERVER['REQUEST_URI']?>" hreflang="x-default">
        <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="examplesite" name="author">
        <meta content="Company" name="copyright">
        <meta content="Global" name="Distribution">
        <meta content="index,follow" name="robots">
        <meta content="15/04/2015" name="creation_Date">
        <meta content="7 days" name="revisit-after">
        <!-- added Meta -->
        <?php if(count($metas) > 0) { ?>
            <?php foreach($metas as $key => $o) { ?>
                <meta content="<?=$o?>" name="<?=$key?>">
            <?php } ?>
        <?php } ?>

        <!-- og metas -->
        <?php if(count($ogmetas) > 0) { ?>
            <?php foreach($ogmetas as $key => $o) { ?>
                <meta content="<?=$o?>" property="<?=$key?>">
            <?php } ?>
        <?php } ?>

        <!-- tag rel="publisher" important for SEO-->
        <link href="https://plus.google.com/+examplesite" rel="publisher" />

        <link rel="apple-touch-icon" sizes="57x57" href="<?=base_url().$publicpath?>favicon.ico/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="<?=base_url().$publicpath?>images/favicon.ico/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="<?=base_url().$publicpath?>images/favicon.ico/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="<?=base_url().$publicpath?>images/favicon.ico/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="<?=base_url().$publicpath?>images/favicon.ico/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="<?=base_url().$publicpath?>images/favicon.ico/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="<?=base_url().$publicpath?>images/favicon.ico/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="180x180" href="<?=base_url().$publicpath?>images/favicon.ico/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="<?=base_url().$publicpath?>images/favicon.ico/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="<?=base_url().$publicpath?>images/favicon.ico/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="<?=base_url().$publicpath?>images/favicon.ico/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url().$publicpath?>images/favicon.ico/favicon-16x16.png">
        <link rel="manifest" href="<?=base_url().$publicpath?>images/favicon.ico/manifest.json">

        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="<?=base_url().$publicpath?>images/favicon.ico/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">

        <!-- Yandex Webmaster verification-->
        <meta name="yandex-verification" content="efc820b67f824314" />

        <!-- Disabling the sitelinks search -->
        <meta name="google" content="nositelinkssearchbox">
        <!-- Pinterest verification-->
        <meta name="p:domain_verify" content="663124348aa6a02bc408e0b0b34d2e96">
        <!-- Google Webmaster verification-->
        <meta name="google-site-verification" content="08Il1n71sQIaYzi-9GOiAcohCFCwlBoZKf370dr9MsY">
        <!-- Bing Webmaster verification-->
        <meta name="msvalidate.01" content="F385FC8A6E43414AC66B9C69560A64E7">
        <!-- <link rel="icon" href="javascript:void(0)"> -->
        <link rel="stylesheet" href="<?=base_url().$publicpath?>css/font-awesome.min.css">
        <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,900' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" media="print" href="<?=base_url().$publicpath?>styles/printfriendly.min.css">

        <!-- My css -->
        <link rel="stylesheet" href="<?=base_url().$publicpath?>styles/sass/bootstrap.min.css">
        <link rel="stylesheet" href="<?=base_url().$publicpath?>styles/sass/reset.min.css">
        <link rel="stylesheet" href="<?=base_url().$publicpath?>styles/sass/styles.css">
        <!-- <link rel="stylesheet" href="<?=base_url().$publicpath?>styles/sass/styles.min.css"> -->
        <!-- <link rel="stylesheet" href="<?=base_url().$publicpath?>styles/css/hover.min.css"> -->
        <link rel="stylesheet" href="<?=base_url().$publicpath?>styles/css/cookiebar.min.css">
        <link rel="stylesheet" href="<?=base_url().$publicpath?>addons/jconfirm/css/jquery-confirm.min.css">
        <link rel="stylesheet" href="<?=base_url().$publicpath?>addons/animate/css/animate.min.css">

        <?=$css_for_layout?>

        <!-- JavaScript -->
        <script type="text/javascript" src="<?=base_url().$publicpath?>js/jQuery.min.js"></script>
        <!--<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>-->
        <!-- <script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script> -->

        <script type="text/javascript" src="<?=base_url().$publicpath?>js/bootstrap.min.js"></script>
        <script type="text/javascript" src="<?=base_url().$publicpath?>js/cookiebar.min.js"></script>
        <script type="text/javascript" src="<?=base_url().$publicpath?>addons/jconfirm/js/jquery-confirm.min.js"></script>
        <script type="text/javascript" src="<?=base_url().$publicpath?>addons/noty/packaged/jquery.noty.packaged.min.js"></script>

        <!-- SLIDE-MENU -->
        <!-- menu styles -->
        <link rel="stylesheet" href="<?=base_url().$publicpath?>styles/css/slide-menu.css">
        <link rel="stylesheet" href="<?=base_url().$publicpath?>styles/css/media-query.css"> 
        <script src="<?=base_url().$publicpath?>js/slide-menu/modernizr-custom.js"></script>

        <!-- <script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script> -->
        <!-- END SLIDE-MENU -->


        <!-- Hotjar Tracking Code for www.examplesite.com -->
    <script>
        //removed
    </script>
        
        <script>
            (function($) {
                $(document).scroll(function(){
                    // Mostro o nascondo lo sfondo del menù allo scroll e visualizzo o nascondo l'hamburger menù
                    var row2 = $('.clear-all'), scrollTop = $(this).scrollTop();

                    if(scrollTop < 400 && scrollTop > 300) {   
                        row2 .addClass('clear-all-pre-fixed');
                    } else if (scrollTop >= 400 || scrollTop < 300) {
                        row2 .removeClass('clear-all-pre-fixed');
                    }
                    if(scrollTop >= 400) {   
                        row2 .addClass('clear-all-fixed');
                    } else if (scrollTop < 400) {
                        row2 .removeClass('clear-all-fixed'); 
                    }
                });
            })(jQuery);
            </script>
        <?=$js_for_layout?>
    </head>
    <body id="home" class="<?=$bodyClass?>">
        <header>
            <button class="action action--open" aria-label="Open Menu"><span class="icon icon--menu"></span></button>
            <div class="menu-shadow action action--close Raction--close" aria-label="Close Menu"></div>
            <nav id="ml-menu" class="menu">
              <button class="action action--close" aria-label="Close Menu"></button>
              <div class="menu__wrap">
                <span class="bottom-shadow"></span>
                <?=$leftMenuView?>
              </div>
            </nav>
            <nav class="row bms-menu">
                <h1 id="logo"><a href="<?=site_url()?>"><span class="buy">buy</span><span class="made">made</span><span class="simple">simple</span></a></h1>
                <a class="search-icon"><span class="fa fa-search"></span></a>
                <a href="<?=site_url('wishlist')?>" class="wishlist-icon"><span class="fa fa-heart-o"></span></a>
            </nav>
        </header>
        <?php if($sale) { ?>
        <div id="topDiscount"><a href="<?=$sale->link?>" target="_blank"><?=$sale->description?> <span>Shop Now</span></a></div>
        <?php } ?>
        <ul class="search-row">
          <li><?=form_open($bodySearch, array("method" => "get", "id" => "headersearchform"))?><input id="headersearch" name="q" type="text" placeholder="<?=$this->lang->line('views.index.what_to_wear')?>"><i id="headersearchbutton" class="pointer fa fa-search"></i><?=form_close()?></li>
          <?php if($this->user->isLoggedIn()) { ?>
            <li class="link menu__item logout-menu"><a class="menu__link" href="<?=site_url("logout")?>"><?=$this->lang->line('views.index.logout')?> <i class="fa fa-sign-out"></i></a></li>  
          <?php } else { ?>
            <li class="link menu__item login-menu"><a class="menu__link" href="<?=site_url("login")?>"><?=$this->lang->line('views.index.login')?>  <i class="fa fa-sign-in"></i></a></li>
          <?php } ?>

          <li class="link"><a href="<?=site_url("wishlist")?>"><?=$this->lang->line('views.wishlist.wishlist')?></a></li>
          <li class="link"><a href="<?=site_url("selections")?>"><?=$this->lang->line('views.selections.selections')?></a></li>
          <li class="link"><a href="#removed" target="blank"><?=$this->lang->line('views.index.blog')?></a></li>
          <li class="link"><a href="<?=site_url("affiliate")?>"><?=$this->lang->line('views.index.affiliate')?></a></li>
        </ul> 
        <div class="content"><?=$content_for_layout?></div>
      <footer>
        <div class="row cookie">
        </div>
      </footer>

    <script>
        $(document).ready(function(){
          var baseUrl = "<?=base_url()?>"; 
          var currentUrl = window.location.href;
          var inShop = false;
            //loading custom messages
            <?php
            if($successMessage != NULL) {
                echo "var n = noty({
                        text: '" . $successMessage . "',
                        animation: {
                            open: 'animated flipInX',
                            close: 'animated flipOutX'
                        },
                        timeout: 4000,
                        type: 'success'
                    });";
            }

            if($alertMessage != NULL) {
                echo "var n = noty({
                        text: '" . $alertMessage . "',
                        animation: {
                            open: 'animated flipInX',
                            close: 'animated flipOutX'
                        },
                        timeout: 4000,
                        type: 'alert'
                    });";
            }

            if($warningMessage != NULL) {
                echo "var n = noty({
                        text: '" . $warningMessage . "',
                        animation: {
                            open: 'animated flipInX',
                            close: 'animated flipOutX'
                        },
                        timeout: 4000,
                        type: 'warning'
                    });";
            }
            ?>
            $.cookieBar({
                message: "<?=$this->lang->line('views.index.cookiePolicy')?>",
                acceptButton: true,
                acceptText: 'OK',
                acceptFunction: null,
                declineButton: false,
                declineText: 'Disable Cookies',
                declineFunction: null,
                policyButton: true,
                policyText: '<?=$this->lang->line('views.index.privacy_footer')?>',
                policyURL: '<?=site_url("privacy-policy")?>',
                autoEnable: true,
                expireDays: 365,
                effect: 'slide',
                element: 'div.cookie',
                append: false,
                fixed: false,
                bottom: true,
                domain: '<?=base_url()?>',
                referrer: '<?=base_url()?>'
            });

            function openLogin() {
                $.confirm({
                    content: 'url:<?=site_url("partials/loginModal");?>',
                    title: '<?=$this->lang->line('views.auth.login')?>',
                    cancelButton: false,
                    confirmButton: false,
                    backgroundDismiss: true,
                    keyboardEnabled: true,
                    theme: 'white',
                    columnClass: 'col-md-12'
                });
            }
            function updateQueryStringParameter(uri, key, value) {
              var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
              var separator = uri.indexOf('?') !== -1 ? "&" : "?";
              if (uri.match(re)) {
                return uri.replace(re, '$1' + key + "=" + value + '$2');
              }
              else {
                return uri + separator + key + "=" + value;
              }
            }
            $("#loginLink").click(function(e){
                e.preventDefault();
                openLogin(); 
            });

            var menuEl = document.getElementById('ml-menu'),
            mlmenu = new MLMenu(menuEl, {
              // breadcrumbsCtrl : true, // show breadcrumbs
              // initialBreadcrumb : 'all', // initial breadcrumb text
              backCtrl : false, // show back button
              // itemsDelayInterval : 60, // delay between each menu item sliding animation
              onItemClick: loadDummyData // callback: item that doesn´t have a submenu gets clicked - onItemClick([event], [inner HTML of the clicked item])
            });

          // mobile menu toggle
          var openMenuCtrl = document.querySelector('.action--open'),
            closeMenuCtrl = document.querySelector('.action--close');

          
            if(openMenuCtrl !== null) {
              openMenuCtrl.addEventListener('click', openMenu);
              closeMenuCtrl.addEventListener('click', closeMenu);
            }

          function openMenu() {
            classie.add(menuEl, 'menu--open');
            $('.menu-shadow').addClass('shadow--open');
          }
          function openRefine() { 
            classie.add(menuRef, 'Rmenu--open');
            $('.menu-shadow').addClass('shadow--open');
          }

          function closeMenu() {
            classie.remove(menuEl, 'menu--open');
            $('.menu-shadow').removeClass('shadow--open');
          }
          function closeRefine() {
            classie.remove(menuRef, 'Rmenu--open');
            $('.menu-shadow').removeClass('shadow--open');
          }

          // simulate grid content loading
          var gridWrapper = document.querySelector('.content');

          function loadDummyData(ev, itemName) {
            ev.preventDefault();
            closeMenu();
            closeRefine();
          }

            <?=$js_on_load?>

            //header form button
            $('#headersearch').keypress(function (e) {
                e.prenventDefault();
                if (e.which == 13 && $(this).val() != "") {
                  $("#headersearchform").submit();
                }
              });
            $("#headersearchbutton").click(function(e){
              e.preventDefault();
              var cnt = $('#headersearch').val();
              if(cnt != "") {
                $("#headersearchform").submit();
              }
            });
        });
    </script>
    
        <script src="<?=base_url().$publicpath?>js/slide-menu/classie.js"></script>
        <script src="<?=base_url().$publicpath?>js/slide-menu/dummydata.js"></script>
        <script src="<?=base_url().$publicpath?>js/slide-menu/main.js"></script>
        <script>
          // gestisce la comparsa della search bar e degli elementi che dipendono dal suo ingresso
          $(".search-icon").click(function(){
              $(".search-row").toggleClass("show-search");

              if ($('.search-row').hasClass('show-search')) {
                $('body').addClass('search-margin');
              } else{
                $('body').removeClass('search-margin');
              }
          });
        </script>
        <script>
          // swipe 
          $(document).on("pagecreate","#home",function(){
            $("body").on("swipeleft",function(){
              if ($('.menu').hasClass('menu--open')) {
                $('.menu').removeClass('menu--open');
                $('.menu-shadow').removeClass('shadow--open');
              }
            });
            $("body").on("swiperight",function(){
              if ($('.Rmenu').hasClass('Rmenu--open')) {
                $('.Rmenu').removeClass('Rmenu--open');
                $('.menu-shadow').removeClass('shadow--open');
              }
            });
          });
        </script>
            <!-- Google analytics code -->
    <script>
      //removed
    </script>
    </body>
</html>