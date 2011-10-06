<?php

namespace App;
use \Lib\Request;

class Songs extends \Lib\Base\App {

    public function init() {
        $count = Request::get('l') ?: \Lib\Config::getInstance()->getOption('app', 'songsCount');
        $this->songs = \Lib\AudioParser::search (
            Request::get('q'), 
            Request::get('offset', '0')
        );
        
        if ( !count($this->songs) && 
             !Request::get('tokenreset') && 
            ('ajax' != Request::get('app')) )
        { // токен сдох
          // фигово тем, что если нифига не найдено будет выполнен перелогин, 
          // но к сожалению это единственный безболезненый способ узнать жив ли еще токен.
            $ids = \Lib\Config::getInstance()->getOption('vk', 'id');
            foreach ($ids as $key => $value) {
                unlink( \Lib\VkLogin::COOK_PATH . $key );
            }

            $url = Request::getAllGet();
            $url['tokenreset'] = true;
            $location = http_build_query( $url );

            header("Location: ?{$location}");
        }
        
        $cut = true;
        
        if ( 1 == $count ) {
            foreach ($this->songs as $value) {
                if ( Request::get('q') == trim($value['artist']."-".$value['name']) ) {
                    $this->songs = array($value);
                    $cut = false;
                    break;
                }
            }

        } 
        
        if ( $cut ) {
            $this->songs = array_slice(// @todo
                $this->songs, 0, $count
            );
        }
    }

}
