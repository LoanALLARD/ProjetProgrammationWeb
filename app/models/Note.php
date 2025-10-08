<?php
class Note
{
    // these attributes are the attributes of my Note table
    // private $_id; I don't think it's necessary because id is auto-incrementing.
    private $_titre;
    private $_contenue;
    private $_date;

    // Constructor
    public function __construct(array $data)
    {
        $this->hydrate($data);
    }

    // Hydratation
    public function hydrate(array $data)
    {
        foreach($data as $key => $value)
        {
            $method = 'set'.ucfirst($key);

            if (method_exists($this, $method))
                $this->$method($value);
        }
    }


    // Setters
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

    // Methods
     
}