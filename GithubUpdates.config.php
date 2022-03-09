<?php
class GithubUpdatesConfig extends ModuleConfig {

  public function getInputfields() {

		$inputfields = parent::getInputfields();
		$wrapper = new InputfieldWrapper();

    //-------------------------------------------------------- 
    //  fields
    //-------------------------------------------------------- 

    $github_set = $this->wire("modules")->get("InputfieldFieldset");
    $github_set->label = "Github";
    $github_set->icon = "github";
    $github_set->description = "Github data needed: `user` name, `repository` name and `personal access token`.";
    $wrapper->add($github_set);

    $f = $this->wire('modules')->get("InputfieldText");
		$f->attr("name" ,"github_user");
    $f->label = "User";
    $f->optionColumns = "1";
    $f->columnWidth = "100%";
    $github_set->add($f);

    $f = $this->wire('modules')->get("InputfieldText");
		$f->attr("name" ,"github_repo");
    $f->label = "Repository";
    $f->optionColumns = "1";
    $f->columnWidth = "100%";
    $github_set->add($f);

    $f = $this->wire('modules')->get("InputfieldText");
		$f->attr("name" ,"github_token");
    $f->label = "Personal Access Token";
    $f->optionColumns = "1";
    $f->columnWidth = "100%";
    $f->notes = "Generate it on github: Settings / Developer settings / Personal Access tokens";
    $github_set->add($f);

    $inputfields->add($github_set);

    $f = $this->wire('modules')->get("InputfieldText");
		$f->attr("name" ,"last_update");
    $f->label = "Last Update";
    $f->optionColumns = "1";
    $f->columnWidth = "100%";
    $f->collapsed = "7";
    $inputfields->add($f);

    //-------------------------------------------------------- 
    //  render inputfields
    //-------------------------------------------------------- 
		return $inputfields;


  }

}