<?php

namespace app\admin\controller;

use Alxg\Library\DB\Query;
use Alxg\Library\Log\Log;
use Alxg\Library\Web\Controller;

class Site extends Controller
{

    public function __init($action = null)
    {
        parent::__init($action);
    }

    /**
     * @throws \Exception
     */
    public function index()
    {
        echo 'Welcome to ITtalk';
    }

    public function sql(){
        $q = new Query();
        echo $q->select('id as ids,a,b,c')->from('cells')->getLastSql();
    }


}