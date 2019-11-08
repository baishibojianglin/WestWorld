<?php
namespace app\venue\controller;

class Index extends Base
{
    public function index()
    {
        //halt(session(config('venue.session_venue'), '', config('venue.session_venue_scope')));
        return $this->fetch();
    }

    public function console()
    {
        return $this->fetch();
    }
}
