<?php

class Ajde_Resource_Local_Compressor_Js extends Ajde_Resource_Local_Compressor
{
    private $_lib = 'closure'; // packer|closure

    public function  __construct()
    {
        $this->setType(Ajde_Resource::TYPE_JAVASCRIPT);
        parent::__construct();
    }

    public function compress()
    {
        $compressor = $this->getCompressor($this->_contents);
        $compressed = $compressor->compress();

        $compressed = 'try{' . $compressed . '}catch(e){throw \'JavaScript parse error (\' + e.message + \').\';}';
        $this->_contents = $compressed;

        return true;
    }

    public function getCompressor($contents)
    {
        require_once 'lib/' . ucfirst($this->_lib) . '.php';
        $className = 'Ajde_Resource_Local_Compressor_Js_' . ucfirst($this->_lib);

        return new $className($contents);
    }
}
