<?php

$root = dirname(__FILE__);
require_once $root.'/config.php';

$timezone = new DateTimeZone('Europe/Berlin');

$today = new DateTime();
$today->setTime(0, 0, 0);
$today->setTimezone($timezone);

$weekdays = array(
  'Mon' => 'Mo',
  'Tue' => 'Di',
  'Wed' => 'Mi',
  'Thu' => 'Do',
  'Fri' => 'Fr',
  'Sat' => 'Sa',
  'Sun' => 'So',
);

$data = @file_get_contents($config['data-url']);
$data = json_decode($data, true);

?>
<!doctype html>
<html lang="de">
  <head>
    <title>BVG.de | kostenlose Corona-Testangebote</title>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="mobile-web-app-capable" content="yes">

    <link rel="stylesheet" href="./css/style.css">
  </head>
  <body>

    <header>
      <div class="black-line">
        <img src="./images/logo.svg" />
      </div>
      <div>
        <h1>Die BVG testet sich: kostenlose Corona-Testangebote.</h1>
      </div>
    </header>

    <main>
      <aside>
        <div>
          <h3>1</h3>
          <p>Standort aussuchen und Termin auswählen. Der Testanbieter ist CovidZentrum.de</p>
        </div>

        <div>
          <h3>2</h3>
          <p>Persönliche Daten eingeben und nachfolgenden Gutschein-Code verwenden.</p>
        </div>

        <div>
          <h3>3</h3>
          <p>Weiteren Anweisungen folgen und Buchung abschließen.</p>
        </div>
      </aside>

      <div class="voucher">
        <code><?php echo $data['voucher'] ?></code>
      </div>

      <div class="container">
          <?php
          
          foreach ($data['locations'] as $location) {
            echo '<article>';
              echo '<header>';
                echo '<h2>'.$location['title'].'</h2>';
              echo '</header>';
              
              echo '<section>';
                echo '<address>'.$location['address'].'</address>';
                if ($location['note']) {
                  echo '<p class="note">'.$location['note'].'</p>';
                }

                $periods = $location['periods'];
                $todays_period = $periods[$today->format('D')];
                $is_open = !$todays_period['closed'];

                $classes = array();
                $classes[] = 'todays-opening';
                $open_text = '&ndash; derzeit geschlossen &ndash;';
                if ($is_open) {
                  $begin = substr($todays_period['begin'], 0, -3);
                  $end = substr($todays_period['end'], 0, -3);

                  $classes[] = 'is-open';
                  $open_text = 'Geöffnet, von '.$begin.' bis '.$end.' Uhr';
                }
                echo '<p class="'.implode(' ', $classes).'">';
                  echo $open_text;
                echo '</p>';

                echo '<div class="periods">';
                foreach ($location['periods'] as $weekday => $period) {
                  echo '<div class="period-row">';
                    echo '<span>'.$weekdays[$weekday].'</span>';
                    echo '<span>';
                      if ($period['closed']) {
                        echo 'geschlossen';
                      } else {
                        echo substr($period['begin'], 0, -3);
                        echo ' &ndash; ';
                        echo substr($period['end'], 0, -3);
                        echo ' Uhr';
                      }
                    echo '</span>';
                  echo '</div>';
                }
                echo '</div>';

                echo '<a href="'.$location['url'].'" target="_blank">';
                  echo 'Termin buchen...';
                echo '</a>';

              echo '</section>';
            echo '</article>';
          }

          ?>
      </div>
    </main>

    <div id="dialog-container">
      <div class="dialog">
        <div class="dialog-title">Datenschutzhinweis</div>
        <div class="dialog-body">
          <div>
            <p>Hiermit bestätige ich, dass ich die nachfolgenden Datenschutzhinweise zur 
              Kenntnis genommen habe und willige in die Datenverarbeitung ein.</p>
          </div>
          <div>
            <strong>freiwillige Teilnahme am Testangebot:</strong>
            <p>Hiermit bestätige ich zusätzlich, dass ich die nachfolgenden Einwilligungserklärung  
              zur Kenntnis genommen habe und willige in die Datenverarbeitung ein.</p>
          </div>
          <div>
            <a href="./documents/20210413_Datenschutzhinweis.pdf" target="_blank">Datenschutzhinweis</a>
          </div>
          <div>
            <a href="./documents/20210413_Einwilligungserklaerung.pdf" target="_blank">Einwilligungserklärung</a>
          </div>
        </div>
        <div class="dialog-buttons">
          <button id="btnDialogCancel" class="button">Abbrechen</button>
          <button id="btnDialogContinue" class="button button-colored">Zustimmen</button>
        </div>
      </div>
    </div>

    <footer>
      <div><?php echo date("Y", time()) ?> &copy; Berliner Verkehrsbetriebe</div>
      <div>
        <a href="https://www.bvg.de/de/impressum">Impressum</a>
        <a href="https://www.bvg.de/de/datenschutz">Datenschutz</a>
      </div>
    </footer>

    <script src="./js/main.js"></script>

  </body>
</html>