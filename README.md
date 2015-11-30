# PHPDateHumanizer

PHPDateHumanizer — PHP-класс, который принимает на вход одну или две даты и возвращает строковую в человеческом формате. Например, на «2015-01-01, 2015-01-02» класс вернет «1—2 января 2015». Или даже «1—2 января», если речь едёт про текущий год.

PHPDateHumanizer родился из необходимости красиво рендерить даты событий: в админке задается дата старта события и, если нужно, дата окончания. Класс распознаёт разные комбинации форматов. 

Класс убивает дубликаты на случай, если вы подали на вход в качестве начала и окончания две одинаковые даты.

Сейчас PHPDateHumanizer работает только с одиночными датами и с интервалами. Будет здорово, если найдется кто-нибудь, кто допишет нужный код, который позволит работать с любым количеством дат, поступающим на вход:

* Если на вход подана одна дата — то это без вариантов одна точечная дата.
* Если на вход поданы две даты — это интервал и его нужно рендерить как промежуток, с тире
* Если на вход поданы три и более дат — их нужно рендерить через запятую: «1,2 и 3 июля»,  «Сентябрь, октябрь и декабрь 2011» и так далее.

## Работа с классом

1. Скачайте
2. Подключите

```
require_once( 'PhpDateHumanizer.php' );
```

3. Создайте экземпляр класса с нужными опциями:

```
$arg = array(
    'dates' => array( '2014-02-12T19:00:00', '2014-02-13T19:00:00' ),
    'timezone' => 'Asia/Tokyo'
);
$date = new PhpDateHumanizer( $arg );
echo $date->output;
```

## Опции

* `dates` — массив дат.
* `show_this_year` — скрывать год, если он совпадает с текущим. По умолчанию true.
* `use_alias` — «вчера», «сегодня», «завтра» вместо дат.
* `timezone` — нужна для корректной работы функции `date()` и применяется только для определения синонимов «Сегодня», «Вчера», «Завтра». Она никак не повлияет на часы и минуты, которые будут вы передадите в массиве `dates`. По умолчанию `timezone` равен `UTC`.
* `lang` — массив строковых для локализации. По умолчанию класс возвращает строчку на английском языке. Чтобы работать с русским языком, нужно передать в опции `lang` такой массив:

```
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
```

## Известные проблемы

* Как написано выше, остается открытым вопрос в случае, когда переданных дат больше двух.
* Капитализация первой буквы: для кириллицы в PHP это сделано через жопу. Поэтому делайте первую букву большой, если это нужно, через CSS.
* Порядок слов на выходе как часть языкового пресета. Например, в английском языке дата пишется в формате «February 14, 2015», в русском — «14 февраля 2015». 

Все баги, пулл-реквесты и все остальное на Гитхабе.

## Примеры

| 2015 | 2015 |
| 2015 —&nbsp;2015 | 2015 |
| 2015 —&nbsp;2016 | 2015—2016 |
| 2015-02 | февраль 2015 |
| 2015-02 —&nbsp;2016 | февраль 2015&nbsp;— 2016 |
| 2015-02-20 —&nbsp;2016 | 20 февраля 2015&nbsp;— 2016 |
| 2015-02-20 —&nbsp;2015-08 | 20 февраля&nbsp;— август 2015 |
| 2015-02-20 —&nbsp;2016-08 | 20 февраля 2015&nbsp;— август 2016 |
| 2015 —&nbsp;2016-02 | 2015&nbsp;— февраль 2016 |
| 2015 —&nbsp;2016-02-19 | 2015&nbsp;— 19 февраля 2016 |
| 2015-02 —&nbsp;2015-02 —&nbsp;2015-02 | февраль 2015 |
| 2015-02 —&nbsp;2016-02 | февраль 2015&nbsp;— февраль 2016 |
| 2015-02 —&nbsp;2015-03 | февраль&nbsp;— март 2015 |
| 2015-02 —&nbsp;2016-03 | февраль 2015&nbsp;— март 2016 |
| 2015-11-28 | 28 ноября 2015 |
| 2015-11-29 | 29 ноября 2015 |
| 2015-11-30 | 30 ноября 2015 |
| 2015-02-01 | 1 февраля 2015 |
| 2015-02-01 —&nbsp;2015-02-01 | 1 февраля 2015 |
| 2015-02-01 —&nbsp;2015-02-02 | 1—2 февраля 2015 |
| 2015-02-01 —&nbsp;2015-03 | 1 февраля&nbsp;— март 2015 |
| 2015-02-01 —&nbsp;2016-01 | 1 февраля 2015&nbsp;— январь 2016 |
| 2015-02 —&nbsp;2015-03-08 | февраль&nbsp;— 8 марта 2015 |
| 2015-02 —&nbsp;2016-03-08 | февраль 2015&nbsp;— 8 марта 2016 |
| 2015-02-01 —&nbsp;2015-02-06 | 1—6 февраля 2015 |
| 2013-02-01 —&nbsp;2013-03-06 | 1 февраля&nbsp;— 6 марта 2013 |
| 2015-02-01 —&nbsp;2015-03-06 | 1 февраля&nbsp;— 6 марта 2015 |
| 2014-02-12T17:00:00 | 12 февраля 2014 в 17:00 |
| 2014-02-12T17:00:00 —&nbsp;2014-02-12T17:00:00 | 12 февраля 2014 в 17:00 |
| 2014-02-12T17:00:00 —&nbsp;2014-02-12T19:00:00 | 12 февраля 2014 с 17:00 по 19:00 |
| 2014-02-12T19:00:00 —&nbsp;2014-02-13T19:00:00 | 12 февраля 2014 в 19:00&nbsp;— 13 февраля 2014 в 19:00 |