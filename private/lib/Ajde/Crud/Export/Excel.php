<?php

require_once 'excel.lib.php';

class Ajde_Crud_Export_Excel extends Ajde_Object_Standard implements Ajde_Crud_Export_Interface
{
    /**
     * @var Excel
     */
    private $writer;

    public function prepare($title, $tableData)
    {
        $this->writer = new Excel($title);

        foreach ($tableData as $row) {
            $this->writer->home();
            foreach($row as $cell) {
                $this->writer->label($cell);
                $this->writer->right();
            }
            $this->writer->down();
        }
    }

    public function send()
    {
        $this->writer->send();
    }
}