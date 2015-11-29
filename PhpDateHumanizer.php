<?php
/**
 * PHP Date Humanizer
 *
 * Convert date intervals to a pretty string.
 *
 * @author Aleksey Cherenkevich <cherenkevich.com@gmail.com>
 * @copyright 2015 Aleksey Cherenkevich
 * @license http://opensource.org/licenses/mit-license.php The MIT License (MIT)
 */

class PhpDateHumanizer {

    public $dates;
    public $use_alias;
    public $show_this_year;
    public $type;
    public $lang;
    public $output;

    /**
     * Construct
     * @param array $arg Config
     */
    public function __construct( $arg ) {

        if ( !isset( $arg['dates'] ) || !$arg['dates'] ) {
            return;
        }

        $this->dates            = $arg['dates']; // Array of dates E.g. 2015, 2015-11, 2015-11-22, 2015-11-22T22:32
        $this->use_alias        = isset( $arg['use_alias'] ) ? $arg['use_alias'] : true; // Alias are 'today', 'yesterday' and 'tomorrow' for appropriate dates
        $this->show_this_year   = isset( $arg['show_this_year'] ) ? $arg['show_this_year'] : false; // If true hides current year in some cases
        $this->timezone         = isset( $arg['timezone'] ) ? $arg['timezone'] : 'UTC'; // Need for right alias calculating. Doesn't affect start or end time value

        $this->lang = array(
            'nominative'        => array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ),
            'genitive'          => array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ),
            'nounSeparator'     => '&ndash;',
            'numberSeparator'   => '&ndash;',
            'yesterday'         => 'Yesterday',
            'today'             => 'Today',
            'tomorrow'          => 'Tomorrow',
        );

        $this->lang             = isset( $arg['lang'] ) && is_array( $arg['lang'] ) ? array_merge( $this->lang, $arg['lang'] ) : $this->lang;

        $this->set_timezone();
        $this->parse();
        $this->remove_duplicates();
        $this->set_type();

        $this->format();
        $this->get_alias();
        $this->render();
    }

    public function debug() {
        $output = '';
        foreach ( $this->dates as $date ) {
            $output .= implode( ' ', $date ) . ' ';
        }
        return $output;
    }

    public function set_timezone() {
        date_default_timezone_set( $this->timezone );
    }

    /**
     * Devide dates into segments
     */
    public function parse() {

        if ( $this->dates ) {
            foreach ( $this->dates as $d=>$datetime ) {
                $datetime = explode( "T", $datetime );
                $date = $datetime[0];
                $time = $datetime[1];

                $this->dates[ $d ] = array();

                $date = explode( "-", $date);
                if ( $date[0] ) {
                    $this->dates[ $d ]['y'] = $date[0];
                }
                if ( $date[1] ) {
                    $this->dates[ $d ]['m'] = $date[1];
                }
                if ( $date[2] ) {
                    $this->dates[ $d ]['d'] = $date[2];
                }

                $time = explode( ":", $time);
                if ( $time[0] ) {
                    $this->dates[ $d ]['h'] = $time[0];
                }
                if ( $time[1] ) {
                    $this->dates[ $d ]['i'] = $time[1];
                }
            }
        }
    }

    /**
     * Check if start and end dates are the same, and remove or leave the end part.
     */
    public function remove_duplicates() {
        if ( !empty( $this->dates ) && count( $this->dates ) > 1 ) {
            $loops = count( $this->dates );
            $is_duplicates = true;

            for ( $i = 1; $i < $loops; $i++ ) {
                if ( $this->dates[0] != $this->dates[ $i ] ) {
                    $is_duplicates = false;
                    break;
                }
            }

            if ( $is_duplicates ) {
                for ( $i = 1; $i < $loops; $i++ ) {
                    unset( $this->dates[ $i ] );
                }
            }
        }
    }

    /**
     * Set the type of date based on the dates array
     */
    public function set_type() {
        switch ( count( $this->dates ) ) {
            case 1:
                $this->type = 'date';
                break;
            case 2:
                $this->type = 'range';
                break;
            default:
                $this->type = 'list';
                break;
        }
    }

    /**
     * Remove unnecessary segments
     */
    public function format() {

        switch ( $this->type ) {
            case 'date':

                break;

            case 'range':

                if ( $this->has_time() ) {

                    if ( $this->dates[0]['y'] == $this->dates[1]['y'] && $this->dates[0]['m'] == $this->dates[1]['m'] && $this->dates[0]['d'] == $this->dates[1]['d'] ) {
                        // The same days
                        unset( $this->dates[1]['y'] );
                        unset( $this->dates[1]['m'] );
                        unset( $this->dates[1]['d'] );
                    }

                } else {
                    if ( $this->dates[0]['m'] || $this->dates[1]['m'] ) {
                        // With any month

                        if ( $this->dates[0]['y'] == $this->dates[1]['y'] ) {
                            // Equal years
                            unset( $this->dates[0]['y'] );
                        }
                    }

                    if ( $this->dates[0]['d'] && $this->dates[1]['d'] ) {
                        // Has any day

                        if ( $this->dates[0]['m'] == $this->dates[1]['m'] ) {
                            // Equal months
                            unset( $this->dates[0]['m'] );
                        }
                    }
                }
                break;

            default:

                break;
        }
    }

    /**
     * Get an alias
     */
    public function get_alias() {
        if ( $this->type == 'date' && $this->dates[0]['d'] && $this->use_alias ) {

            $date = strtotime( $this->dates[0]['d'] . '-' . $this->dates[0]['m'] . '-' . $this->dates[0]['y']);
            $diff = ( date( 'U' ) - $date ) / 86400;

            if ( $diff >= 0 && $diff < 1 ) {
                $this->output = $this->today;
            }
            if ( $diff >= 1 && $diff < 2 ) {
                $this->output = $this->yesterday;
            }
            if ( $diff >= -1 && $diff < 0 ) {
                $this->output = $this->tomorrow;
            }
        }
    }

    /**
     * Render segments to string
     * @return string Date
     */
    public function render() {
        if ( $this->output ) {
            return;
        }

        switch ( $this->type ) {
            case 'date':
                $this->output .= trim( $this->d( 0 ) . ' ' . $this->m( 0 ) . ' ' . $this->y( 0 ) . ' ' . $this->time( 0 ) );
                break;

            case 'range':
                $this->output .= trim( $this->d( 0 ) . ' ' . $this->m( 0 ) . ' ' . $this->y( 0 ) . ' ' . $this->time( 0 ) );
                $this->output .= $this->sep();
                $this->output .= trim( $this->d( 1 ) . ' ' . $this->m( 1 ) . ' ' . $this->y( 1 ) . ' ' . $this->time( 1 ) );
                break;

            default:

                break;
        }

        $this->output = ucfirst( $this->output );
    }

    /**
     * Check if date has time segments
     * @return boolean True if has
     */
    public function has_time() {
        foreach ( $this->dates as $date ) {
            if ( isset( $date['h'] ) && $date['h'] ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Time
     * @param  string $part Part of the dates array
     * @return string       Processed time
     */
    public function time( $part ) {
        $time = '';
        $prefix = $this->lang['at'];
        if ( $this->dates[ $part ]['h'] ) {
            $time = (int) $this->dates[ $part ]['h'] . ':' . ( $this->dates[ $part ]['i'] ? $this->dates[ $part ]['i'] : '00' );

            if ( $part == 0 && $this->dates[1]['h'] && !$this->dates[1]['d'] ) {
                $prefix = $this->lang['since'];
            }

            if ( $part == 1 && !$this->dates[1]['d'] ) {
                $prefix = $this->lang['till'];
            }

            return $prefix . ' ' . $time;
        }
    }

    /**
     * Day
     * @param  string $part Part of the dates array
     * @return string       Processed day
     */
    public function d( $part ) {
        if ( $this->dates[ $part ]['d'] ) {
            $d = (int) $this->dates[ $part ]['d'];
            return $d;
        }
    }

    /**
     * Month
     * @param  string $part Part of the dates array
     * @return string       Processed month
     */
    public function m( $part ) {
        if ( $this->dates[ $part ]['m'] ) {

            $m = (int) $this->dates[ $part ]['m'];

            if ( $this->dates[ $part ]['d'] ) {
                return $this->lang['genitive'][ $m - 1 ];
            } else {
                return $this->lang['nominative'][ $m - 1 ];
            }
        }
    }

    /**
     * Year
     * @param  string $part Part of the dates array
     * @return string       Processed year
     */
    public function y( $part ) {
        if ( $this->dates[ $part ]['y'] ) {
            $y = $this->dates[ $part ]['y'];

            if ( !$this->show_this_year ) {
                if ( !$part && !$this->dates[0]['y'] && date( 'Y' ) == $this->dates[1]['y'] ||
                     !$part && $this->dates[0]['m'] && !$this->dates[0]['d'] && !$this->dates[1]['y'] && date( 'Y' ) == $this->dates[0]['y'] ) {

                    return '';
                }
            }

            return $y;
        }
    }

    /**
     * Returns appropriate date separator
     * @return string Separator
     */
    public function sep() {
        $separator = '';

        if ( !$this->dates[0]['m'] && ( !$this->dates[0]['y'] || !$this->dates[1]['m'] ) ) {
            $separator .= $this->lang['numberSeparator'];
        } else {
            $separator .= $this->lang['nounSeparator'];
        }
        if ( $this->dates[0]['h'] && $this->dates[1]['h'] && !$this->dates[1]['d'] ) {
            $separator = ' ';
        }
        return $separator;
    }
}