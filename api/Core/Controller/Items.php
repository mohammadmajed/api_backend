<?php

namespace Core\Controller; 
use Core\Database\DB;
use Exception;

class Items 
{
    protected $db ;
    protected $http_code=200;
    protected $request_body;
    protected $resonse_schema=array(
   "success"=> true,
   "messege_code"=>"",
   "body"=>array()
);
    public function __construct()
    {
        $this->db = new DB();
        $this->request_body=json_decode(file_get_contents("php://input",true));
        $user_logged_in=false;
        // try{
        //     if($user_logged_in == false){
        //         throw new \Exception("User not logged in");
        //     }
        // }catch(\Exception $error){
    
        // }
    }
    public function index(): void
    {
        $items = array();
        try {
            $result = $this->db->submit_query("SELECT * FROM items");

            if (!$result) {
                $this->http_code = 500;
                throw new Exception("server_error");
            }

            if ($result->num_rows == 0) {
                $this->http_code = 404;
                throw new Exception("items_not_found");
            }

            while ($row = $result->fetch_object()) {
                $items[] = $row;
            }
            $this->resonse_schema['body'] = $items;
            $this->resonse_schema['message_code'] = "items_collected";
        } catch (Exception $error) {
            $this->resonse_schema['success'] = false;
            $this->resonse_schema['message_code'] = $error->getMessage();
        }
    $this->resonse_schema['body']=$items;
    $this->resonse_schema['messege_code']="items_collected";

    
    
    }
    public function single()
{
    
}
public function create()
{
    try{
        if(!isset($this->request_body->name)){
            $this->http_code = 422;
            throw new Exception('name_param_not_found');
        }
        if(!$this ->db->submit_query("INSERT INTO items(name) VALUE ('{$this->request_body->name}')"));
        $this->http_code = 500;
        throw new Exception('server_error');
        

    }catch(Exception $error){
        $this->resonse_schema['success'] = false;
        $this->resonse_schema['message_code'] = $error->getMessage();
        $this->resonse_schema['body'][] = $this->get_item_by_id($this->db->connection->insert_id);

    }
    $this->resonse_schema['messege_code']="items_ccreate";
}

public function render ()
{
    header("Content-Type: application/json");
    http_response_code($this->http_code);
    echo json_encode($this->resonse_schema);
}
public function update()
{
    try{
        if(!isset($this->request_body->id)){
            $this->http_code = 422;
            throw new Exception('id_param_not_found');
        }
        $items=$this->get_item_by_id($this->request_body->id);

        if(empty ($item)){
            $this->http_code = 404;
        throw new Exception('no_item_was_found'); 
        }

        $completed =!(bool)$item->completed;
        $completed =$completed?"1":"0";
        if(!$this ->db->submit_query("UPDATE items SET complated=$completed WHERE id={$this->request_body->id}"));{
        $this->http_code = 500;
        throw new Exception('server_error');
        }

    }catch(Exception $error){
        $this->resonse_schema['success'] = false;
        $this->resonse_schema['message_code'] = $error->getMessage();
    }
    $this->resonse_schema['messege_code']="items_updated";
}



protected function get_item_by_id($id)
{
    $result =$this ->db->submit_query("SELECT *FROM items WHERE id=$id");
    return $result->fetch_object();
}


public function delete()
{
    try{
        if(!isset($this->request_body->id)){
            $this->http_code = 422;
            throw new Exception('id_param_not_found');
        }
        $items=$this->get_item_by_id($this->request_body->id);

        if(empty ($item)){
            $this->http_code = 404;
        throw new Exception('no_item_was_found'); 
        }

      
        if(!$this ->db->submit_query("DELETE FROM items WHERE id={$this->request_body->id}"));{
        $this->http_code = 500;
        throw new Exception('server_error');
       } 
    }catch(Exception $error){
        $this->resonse_schema['success'] = false;
        $this->resonse_schema['message_code'] = $error->getMessage();
    }
    $this->resonse_schema['messege_code']="items_deleted";
}


}
