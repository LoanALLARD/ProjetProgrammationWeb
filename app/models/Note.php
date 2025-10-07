<?php
class Note
{
    // attributes of the class Notes in the database 
    private $_titre;
    private $_contenue;
    private $_date;

    //constructer
    public function __construct(array $data)
    {
        $this->hydrate($data);
    }

    //hydration
    public function hydrate(array $data)
    {
        foreach($data as $key => $value)
        {
            $method = 'set'.ucfirst($key);

            if (method_exists($this, $method))
                $this->$method($value);
        }
    }


    //SETTERS
    public function setTitre($newTitre){
        if(is_string($newTitre)){
            $this->_titre = $newTitre;
        }
        
    }

    public function setContenue($_newContenue){
        if(is_string($_newContenue)){
            $this->_contenue = $_newContenue;
        }
        
    } 

    public function setDate($_newDate){
        $this->_date = $_newDate;
    }

    //GETTERS
    public function getTitre(){
        return $this->_titre;
    }

    public function getContenue(){
        return $this->_contenue;
    }

    public function getDate(){
        return $this->_date;
    }
     
}