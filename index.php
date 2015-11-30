<?php

require_once( 'PhpDateHumanizer.php' );

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>PhpDateHumanizer Test</title>
</head>
<body>

<?php

echo '<table>';

$dates = array(
    array( '2015' ),
    array( '2015', '2015' ),
    array( '2015', '2016' ),
    array( '2015-02' ),
    array( '2015-02', '2016' ),
    array( '2015-02-20', '2016' ),
    array( '2015-02-20', '2015-08' ),
    array( '2015-02-20', '2016-08' ),
    array( '2015', '2016-02' ),
    array( '2015', '2016-02-19' ),
    array( '2015-02', '2015-02', '2015-02' ),
    array( '2015-02', '2016-02' ),
    array( '2015-02', '2015-03' ),
    array( '2015-02', '2016-03' ),
    array( '2015-11-28' ),
    array( '2015-11-29' ),
    array( '2015-11-30' ),
    array( '2015-02-01' ),
    array( '2015-02-01', '2015-02-01' ),
    array( '2015-02-01', '2015-02-02' ),
    array( '2015-02-01', '2015-03' ),
    array( '2015-02-01', '2016-01' ),
    array( '2015-02', '2015-03-08' ),
    array( '2015-02', '2016-03-08' ),
    array( '2015-02-01', '2015-02-06' ),
    array( '2013-02-01', '2013-03-06' ),
    array( '2015-02-01', '2015-03-06' ),
    array( '2014-02-12T17:00:00' ),
    array( '2014-02-12T17:00:00', '2014-02-12T17:00:00' ),
    array( '2014-02-12T17:00:00', '2014-02-12T19:00:00' ),
    array( '2014-02-12T19:00:00', '2014-02-13T19:00:00' ),
);

echo '<table>';
foreach ( $dates as $key => $value ) {

    $arg = array(
        'dates'             => $value,
        'show_this_year'    => true,
        'use_alias'         => false,
        'lang'              => array(
            'since'             => 'с',
            'till'              => 'по',
            'at'                => 'в',
            'today'             => 'Сегодня',
            'tomorrow'          => 'Завтра',
            'yesterday'         => 'Вчера',
            'nounSeparator'     => '&nbsp;&mdash; ',
            'numberSeparator'   => '&mdash;',
            'nominative'        => array( 'январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь' ),
            'genitive'          => array( 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря')
        )
    );

    echo '<tr>';
        echo '<td>' . implode( $value, " — " ) . '</td>';
        $pdh = new PhpDateHumanizer( $arg );
        echo '<td>' . $pdh->output . '</td>';
    echo '</tr>';
}

echo '</table>';

?>

</body>
</html>