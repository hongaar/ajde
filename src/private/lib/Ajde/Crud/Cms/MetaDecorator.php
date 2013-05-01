<?php

class Ajde_Crud_Cms_MetaDecorator extends Ajde_Object_Standard
{
	/**
	 *
	 * @var Ajde_Crud_Options 
	 */
	protected $options;
	
	protected $activeRow = 0;
	protected $activeColumn = 0;
	protected $activeBlock = 0;
	
	public function __construct() {
		
	}
	
	public function setActiveRow($row)
	{
		$this->activeRow = $row;
	}
	
	public function setActiveColumn($column)
	{
		$this->activeColumn = $column;
	}
	
	public function setActiveBlock($block)
	{
		$this->activeBlock = $block;
	}
	
	public function decorateCrudOptions(Ajde_Crud_Options $options)
	{
		$this->options = $options;
		
		foreach($this->getTypes() as $type) {
			
		}
		
		$show = $options->_stack['edit']['layout']['rows'][$this->activeRow]['columns'][$this->activeColumn]['blocks'][$this->activeBlock]['show'];
		$show[] = 'hoi';
		$options->_stack['edit']['layout']['rows'][$this->activeRow]['columns'][$this->activeColumn]['blocks'][$this->activeBlock]['show'] = $show;
	}
	
	public function getTypes()
	{
		$ds = DIRECTORY_SEPARATOR;
		$files = Ajde_FS_Find::findFiles(LIB_DIR.'Ajde'.$ds.'Crud'.$ds.'Cms'.$ds.'Type'.$ds, '*.php');
		$ret = array();
		foreach($files as $file) {
			$ret = filei
		}
		return $files;
	}
}