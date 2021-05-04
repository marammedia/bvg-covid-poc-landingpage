<?php

$root = dirname(__FILE__);
require_once $root.'/config.php';

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
  $rows = explode(';', $line, 6);
  $rows = array_map('trim', $rows);

  list($date, $location, $url, $hints, $begin, $end) = $rows;
  $data[$date][$location] = array(
    'url' => $url,
    'hints' => $hints,
    'startTime' => $begin,
    'endTime' => $end,
  );
}
ksort($data);


$accessibilities = array();
$raw = @file_get_contents($config['accessibility-url']);
$lines = explode("\n", $raw);
foreach ($lines as $line) {
  if (empty($line)) {
    continue;
  }

  $line = html_entity_decode($line);
  $rows = explode(';', $line);
  $rows = array_map('trim', $rows);

  $location = array_shift($rows);

  foreach ($rows as $row) {
    $accessibilities[$location][] = explode('+', $row, 2);
  }
}

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
                    echo '<div>';
                    echo $location;
                    if ($content['hints']) {
                      echo ' <sup>'.$content['hints'].'</sup>';
                    }

                    if ($begin && $end) {
                      echo '<div class="timeframe">';
                        echo $begin->format('H:i').' Uhr &ndash; '.$end->format('H:i').' Uhr';
                      echo '</div>';
                    }
                    echo '</div>';
                    echo '<div>';
                      if (isset($accessibilities[$location])) {
                        echo '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000">';
                          echo '<path d="M0 0h24v24H0V0z" fill="#f0d722" />';
                          echo '<circle cx="12" cy="4" r="2" />';
                          echo '<path d="M19 13v-2c-1.54.02-3.09-.75-4.07-1.83l-1.29-1.43c-.17-.19-.38-.34-.61-.45-.01 0-.01-.01-.02-.01H13c-.35-.2-.75-.3-1.19-.26C10.76 7.11 10 8.04 10 9.09V15c0 1.1.9 2 2 2h5v5h2v-5.5c0-1.1-.9-2-2-2h-3v-3.45c1.29 1.07 3.25 1.94 5 1.95zm-9 7c-1.66 0-3-1.34-3-3 0-1.31.84-2.41 2-2.83V12.1c-2.28.46-4 2.48-4 4.9 0 2.76 2.24 5 5 5 2.42 0 4.44-1.72 4.9-4h-2.07c-.41 1.16-1.52 2-2.83 2z" />';
                        echo '</svg>';
                      }
                    echo '</div>';
                  echo '</a>';
                }
              echo '</nav>';
            echo '</article>';
          }

          ?>
      </section>

      <p>1 &ndash; Diese Teststation(en) sind <strong>ausschließlich</strong> für Mitarbeiterinnen und Mitarbeiter des jeweiligen Standortes zugelassen.</p>
      <p>
        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000">
          <circle cx="12" cy="4" r="2" />
          <path d="M19 13v-2c-1.54.02-3.09-.75-4.07-1.83l-1.29-1.43c-.17-.19-.38-.34-.61-.45-.01 0-.01-.01-.02-.01H13c-.35-.2-.75-.3-1.19-.26C10.76 7.11 10 8.04 10 9.09V15c0 1.1.9 2 2 2h5v5h2v-5.5c0-1.1-.9-2-2-2h-3v-3.45c1.29 1.07 3.25 1.94 5 1.95zm-9 7c-1.66 0-3-1.34-3-3 0-1.31.84-2.41 2-2.83V12.1c-2.28.46-4 2.48-4 4.9 0 2.76 2.24 5 5 5 2.42 0 4.44-1.72 4.9-4h-2.07c-.41 1.16-1.52 2-2.83 2z" />
        </svg>
        Diese Teststation(en) bieten einen barrierefreien Zugang.</p>
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