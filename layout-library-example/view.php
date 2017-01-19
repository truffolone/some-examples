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

        <!-- My css loaded by a controller line thanks to the Layout library -->
        <?=$css_for_layout?>

        <!-- JavaScript -->
        <script type="text/javascript" src="<?=base_url().$publicpath?>js/jQuery.min.js"></script>

        <!-- My JS loaded from the controller thanks to the Layout library -->
        <?=$js_for_layout?>

    </head>
    <body id="home" class="<?=$bodyClass?>">
        <header>
            <!-- whatever you want -->
        </header>
        <!-- view content -->
        <div class="content"><?=$content_for_layout?></div>
      <footer>
        <div class="row cookie">
        </div>
      </footer>

    <script>
        $(document).ready(function(){
            //basic variables accessible from all the partial js loaded with the jsOnLoad in the Layout and set up on the controller on demand)
          var baseUrl = "<?=base_url()?>"; 
          var currentUrl = window.location.href;

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

            //whatever you loaded as a partial custom js is now loaded into the single $(document).ready()
            <?=$js_on_load?>
        });
    </script>
    </body>
</html>