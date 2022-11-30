<?php

namespace Core\Controller; 
use Core\Database\DB;
use Exception;

class front
{
public function render ()
{
    include dirname(__DIR__,2)."/resources/todo.php";
}
}