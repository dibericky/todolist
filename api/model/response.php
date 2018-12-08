<?php
class Response{
    public $data = [];
    public $error = "no_error";

    public function getJson(){
        $response = array(
            "data"=> $this->data,
            "error"=>$this->error
        );
        return json_encode($response);
    }
}
?>