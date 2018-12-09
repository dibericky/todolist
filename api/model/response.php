<?php
class Response{
    public $data;
    public $error = "no_error";

    public function getJson(){
        if($this->data == null){
            $this->data = array();
        }
        $response = array(
            "data"=> $this->data,
            "error"=>$this->error
        );
        return json_encode($response);
    }
}
?>