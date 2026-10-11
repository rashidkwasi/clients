<?php
namespace app\Traits;


trait HttpResponses{

    protected function success($data,$message = null,$statuscode = 200){

        return response() -> json([
            'status' => 'Request was successful',
            'message' => $message,
            'data'=> $data
        ],status: $statuscode);

    }

     protected function error($data,$message = null,$statuscode = 400){

        return response() -> json([
            'status' => 'Request was not sucessful',
            'message' => $message,
            'data'=> $data
        ],$statuscode);

    }




}
