<!doctype html>
<html lang="en" dir="ltr">
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
        <h1>Die BVG testet sich: kostenlose Corona-Testangebote</h1>
      </div>
    </header>

    <main>
      <section class="tutorial">
        <article>
          <div>1</div>
          <div>Ein passendes Datum, sowie einen Standort aus der nachfolgenden Liste auswählen. Es erfolgt anschließend eine Weiterleitung zum Dienstleister. Das Testangebot ist kostenlos.</div>
        </article>

        <article>
          <div>2</div>
          <div>Im dortigen Terminvereinbarungsportal eine Uhrzeit auswählen und die persönlichen Daten eingeben.<br />Das Feld <em>Dokumentennummer</em> ist mit der <em>Dienstausweisnummer</em> zu befüllen.<br />Die Terminvereinbarung muss vollständig durchgeführt werden - andernfalls ist keine Testung möglich.</div>
        </article>

        <article>
          <div>3</div>
          <div>Der gebuchte Zeitpunkt ist möglichst einzuhalten und der Dienstausweis, sowie das Ticket (Digital oder Print) ist mitzubringen und vor Ort unaufgefordert vorzuzeigen.</div>
        </article>
      </section>

      <section>
          <?php

          $data = array();

          $root = dirname(__FILE__);
          $path = $root.'/termin-data.csv';

          $raw = @file_get_contents($path);
          $lines = explode("\n", $raw);
          foreach ($lines as $line) {
            if (empty($line)) {
              continue;
            }

            $line = utf8_encode($line);
            list($date, $location, $url) = explode(';', $line);
            $data[$date][$location] = $url;
          }

          ksort($data);
          foreach ($data as $date => $locations) {
            $datetime = new DateTime($date);

            echo '<article>';
              echo '<header>';
                echo '<h2>'.formatDate($datetime).'</h2>';
              echo '</header>';
              
              echo '<nav>';
                ksort($locations);
                foreach ($locations as $location => $url) {
                  echo '<a href="'.$url.'" target="_blank">';
                    echo $location;
                  echo '</a>';
                }
              echo '</nav>';
            echo '</article>';
          }

          ?>

      </section>
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
      <div>2021 &copy; Berliner Verkehrsbetriebe</div>
      <div>
        <a href="https://www.bvg.de/de/Serviceseiten/Impressum">Impressum</a>
        <a href="https://www.bvg.de/de/Serviceseiten/Datenschutzhinweise">Datenschutz</a>
      </div>
    </footer>

    <script src="./js/main.js"></script>

  </body>
</html>
<?php

function formatDate(DateTime $datetime) {
  static $weekdays;
  static $months;
  if (!$weekdays) { // w
    $weekdays = array(
      0 => 'Sonntag',
      1 => 'Montag',
      2 => 'Dienstag',
      3 => 'Mittwoch',
      4 => 'Donnerstag',
      5 => 'Freitag',
      6 => 'Samstag',
    );
  }
  if (!$months) { // n
    $months = array(
      1 => 'Januar',
      2 => 'Februar',
      3 => 'März',
      4 => 'April',
      5 => 'Mai',
      6 => 'Juni',
      7 => 'Juli',
      8 => 'August',
      9 => 'September',
      10 => 'Oktober',
      11 => 'November',
      12 => 'Dezember',
    );
  }

  $formated = $datetime->format('Y-n-d-w');
  list($year, $month, $day, $weekday) = explode('-', $formated);

  return sprintf(
    '%s, %d. %s %d',
    $weekdays[$weekday],
    $day,
    $months[$month],
    $year);
}