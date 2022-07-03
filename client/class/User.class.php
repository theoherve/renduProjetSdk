<?php

class User{
	private mixed $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData(...$params): array
    {
		$return = [];
	    foreach($params as $param){
			$return[$param] = $this->data[$param];
		}
        return $return;
    }

    /**
     * @param mixed $data
     */
    public function setData(mixed $data): void
    {
        $this->data = json_decode($data, true);;
    }
	
}