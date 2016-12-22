<?php namespace axenox\PackageManager\Actions;

use kabachello\ComposerAPI\ComposerAPI;
use exface\Core\Interfaces\Actions\iModifyData;
use exface\Core\Exceptions\Actions\ActionInputMissingError;
use exface\Core\Exceptions\Actions\ActionInputInvalidObjectError;

/**
 * This action runs one or more selected test steps
 * 
 * @author Andrej Kabachnik
 *
 */
class ComposerRequire extends AbstractComposerAction implements iModifyData {
	
	protected function init(){
		parent::init();
		$this->set_icon_name('install');
	}	
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \axenox\PackageManager\Actions\AbstractComposerAction::perform_composer_action()
	 */
	protected function perform_composer_action(ComposerAPI $composer){
		if (!$this->get_input_data_sheet()->get_meta_object()->is_exactly('axenox.PackageManager.PACKAGE_INSTALLED')){
			throw new ActionInputInvalidObjectError($this, 'Wrong input object for action "' . $this->get_alias_with_namespace() . '" - "' . $this->get_input_data_sheet()->get_meta_object()->get_alias_with_namespace() . '"! This action requires input data based based on "axenox.PackageManager.PACKAGE_INSTALLED"!', '6T5E8Q6');
		}
				
		$packages = array();
		foreach ($this->get_input_data_sheet()->get_rows() as $nr => $row){
			if (!isset($row['name']) || !$row['name']){
				throw new ActionInputMissingError($this, 'Missing package name in row ' . $nr . ' of input data for action "' . $this->get_alias_with_namespace() . '"!', '6T5TRFE');
			}
			$packages[] = $row['name'] . ($row['version'] ? ':' . $row['version'] : '');
			
			if ($row['repository_type'] && $row['repository_url']){
				$composer->config('repositories.'.$row['name'], array($row['repository_type'], $row['repository_url']));
			}
		}
		
		return $composer->require($packages);
	}
	
}
?>