<?php

$config = array(
  'csv-url' => 'https://beta.bvg.de/dam/jcr:2f4f829a-780b-4749-8207-e34c898f7704/corona-schnelltest-termin-data.csv',
);

?>
<!doctype html>
<html lang="de" dir="ltr">
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

          $timezone = new DateTimeZone('Europe/Berlin');

          $today = new DateTime();
          $today->setTime(0, 0, 0);
          $today->setTimezone($timezone);

          $data = array();

          $raw = @file_get_contents($config['csv-url']);
          $lines = explode("\n", $raw);
          foreach ($lines as $line) {
            if (empty($line)) {
              continue;
            }

            $line = html_entity_decode($line);
            list($date, $location, $url, $hints, $begin, $end) = explode(';', $line, 6);
            $data[$date][$location] = array(
              'url' => trim($url),
              'hints' => trim($hints),
              'startTime' => trim($begin),
              'endTime' => trim($end),
            );
          }

          ksort($data);
          foreach ($data as $date => $locations) {
            $datetime = new DateTime($date);
            $datetime->setTimezone($timezone);

            if (!$datetime || $today > $datetime) {
              continue;
            }

            echo '<article>';
              echo '<header>';
                echo '<h2>'.formatDate($datetime).'</h2>';
              echo '</header>';
              
              echo '<nav>';
                ksort($locations);
                foreach ($locations as $location => $content) {
                  $begin = null;
                  if (!empty($content['startTime'])) {
                    $begin = new DateTime($content['startTime']);
                  }
                  $end = null;
                  if (!empty($content['endTime'])) {
                    $end = new DateTime($content['endTime']);
                  }

                  echo '<a href="'.$content['url'].'" target="_blank">';
                    echo $location;
                    if ($content['hints']) {
                      echo ' <sup>'.$content['hints'].'</sup>';
                    }

                    if ($begin && $end) {
                      echo '<div class="timeframe">';
                        echo $begin->format('H:i').' Uhr &ndash; '.$end->format('H:i').' Uhr';
                      echo '</div>';
                    }
                  echo '</a>';
                }
              echo '</nav>';
            echo '</article>';
          }

          ?>

      </section>

      <p>1 &ndash; Diese Teststation(en) sind <strong>ausschließlich</strong> für Mitarbeiterinnen und Mitarbeiter des jeweiligen Standortes zugelassen.</p>
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