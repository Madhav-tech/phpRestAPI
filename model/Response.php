<?php

class Response
{
    private $_success;
    private $_httpStatusCode;
    private $_messages = [];
    private $_data;
    private $_toCache = false;
    private $_responseData = [];



    public function set_success($_success)
    {
        $this->_success = $_success;

        return $this;
    }
    public function set_httpStatusCode($_httpStatusCode)
    {
        $this->_httpStatusCode = $_httpStatusCode;

        return $this;
    }
    public function set_messages($_messages)
    {
        $this->_messages[] = $_messages;

        return $this;
    }
    public function set_data($_data)
    {
        $this->_data = $_data;

        return $this;
    }

    public function set_toCache($_toCache)
    {
        $this->_toCache = $_toCache;

        return $this;
    }
    public function set_responseData($_responseData)
    {
        $this->_responseData[] = $_responseData;

        return $this;
    }

    public function send()
    {
        header('Content-type:application/json;charset=utf-8');
        if ($this->_toCache) {
            header('Cache-control: max-age=60');
        } else {
            header('Cache-control: no-cache, no-strore');
        }
        if (($this->_success !== false && $this->_success !== true) || !is_numeric($this->_httpStatusCode)) {
            http_response_code(500);
            $this->_responseData['statusCode'] = 500;
            $this->_responseData['success'] = false;
            $this->set_messages("Response creation error");
            $this->_responseData['messages'] = $this->_messages;
        } else {
            http_response_code($this->_httpStatusCode);
            $this->_responseData['statusCode'] = $this->_httpStatusCode;
            $this->_responseData['success'] = $this->_success;
            $this->_responseData["messages"] = $this->_messages;
            $this->_responseData["data"] = $this->_data;
        }
        echo json_encode($this->_responseData);
    }
}
