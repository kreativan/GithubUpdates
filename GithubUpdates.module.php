<?php

class GithubUpdates extends Process {

  public function __construct() {
    $this->updates_dir = $this->config->paths->assets . "github-updates/";
    $this->temp_dir = $this->config->paths->assets . "temp/";
    $this->vendor_version = $this->vendor("version");
  }

  public function init() {
		parent::init(); // always remember to call the parent init

    // Create updates and temp dir 
    if(!is_dir($this->updates_dir)) $this->files->mkdir($this->updates_dir);

    $current = $this->vendor_version;
    $next = $this->getUpdateInfo("version");

    // console.log(ProcessWire.config.GithubUpdates);
    $this->config->js('GithubUpdates', [
      'user' => $this->github_user,
      'repo' => $this->github_repo,
      'token' => $this->github_token,
      "json_folder" => "site/templates/vendor.json",
      'current_version' => "{$this->vendor_version}",
      'pending_version' => "{$this->getUpdateInfo("version")}",
      'debug' => wire("config")->debug ? true : false,
    ]);

    //
    // Download update from github
    //
    if($this->input->get->download_update) {
      $this->downloadGithub();
      $this->session->redirect("./");
    }

    //
    // Delete ready update
    //
    if($this->input->get->delete_updates) {
      $this->files->rmdir($this->updates_dir, true);
      $this->message("Updates has been removed");
      $this->session->redirect("./");
    }

    //
    //  Install updates
    //
    if($this->input->get->install_updates) {
      $update_php = $this->updates_dir . "update.php";
      if(file_exists($update_php)) include($update_php);
    }

  }


  //-------------------------------------------------------- 
  //  Methods
  //-------------------------------------------------------- 
	public function vendor($field_name) {
		$vendor_json = wire("config")->paths->templates . "vendor.json";
    $vendor_json_data = file_get_contents($vendor_json);
    $vendor_data = json_decode($vendor_json_data, true);
    if($field_name != "") {
      return isset($data[$field_name]) && !empty($data[$field_name]) ? $data[$field_name] : false;
    } else {
      return $data;
    }
	}
	
  public function needUpdate($current, $next) {
    $compare = version_compare($current,  $next);
    return ($compare == "-1") ? true : false;
  }

  public function isUpdateReady() {
    $updates_file = $this->updates_dir . "update.php";
    $json = $this->updates_dir . "site/templates/vendor.json";
    if(!file_exists($updates_file) || !file_exists($json)) return false;
    if($this->getUpdateInfo("version") <= $this->vendor_version) return false;
    return true;
  }

  public function getUpdateInfo($field_name = "") {
    $json = $this->updates_dir . "site/templates/vendor.json";
    if(!file_exists($json)) return false;
    $json_data = file_get_contents($json); 
    $update_vendor = json_decode($json_data, true);
    return ($field_name != "") ? $update_vendor[$field_name] : $update_vendor;
  }

  public function getReadme() {
    $readme_md = $this->updates_dir . "README.md";
    $changelog_md = $this->updates_dir . "CHANGELOG.md";
    $md = file_exists($changelog_md) ? $changelog_md : $readme_md;
    if(!file_exists($md)) return false;
    if(!$this->modules->isInstalled("TextformatterMarkdownExtra")) {
      $this->warning("TextformatterMarkdownExtra module is required to display changelog...");
      return false;
    }
    $readme = file_get_contents($md);
    $textformatter = $this->modules->get("TextformatterMarkdownExtra");
    $textformatter->format($readme);
    return $readme;
  }

  /**
   *  Check for updates
   *  by fetching vendor.json from a github repo
   */
  public function checkForUpdate() {

    $user   = $this->github_user;
		$repo   = $this->github_repo;
		$token  = $this->github_token;
    $url = "https://api.github.com/repos/$user/$repo/contents/vendor.json";

    $http = new WireHttp();
		$http->setHeader("Authorization", "token $token");

    if($http->status($url) == "200") {

      $data = $http->getJSON($url);
      $download_url = $data["download_url"];
      $json = $http->getJSON($download_url);

      if($json["version"] > $this->vendor_version) {
        $this->warning("New update available!");
      }

    }
    
  }

  /**
	 *  Downlaod Updates (GitHub)
	 *  This will download updates from a github repo
	 *  @param string $user     github user name
	 *  @param string $repo     reposetory name
	 *  @param string $token    github private access token
	 */
  public function downloadGithub() {

    $user   = $this->github_user;
		$repo   = $this->github_repo;
		$token  = $this->github_token;
		$json           = "https://api.github.com/repos/{$user}/{$repo}";
		$downlaod_url   = "https://api.github.com/repos/{$user}/{$repo}/zipball/main";

    $http = new WireHttp();
		$http->setHeader("Authorization", "token $token");

    if($http->status($json) == "200") {

      // remove updates dir adn create it again, to cleanup
      $this->files->rmdir($this->updates_dir, true);
      if(!is_dir($this->updates_dir)) $this->files->mkdir($this->updates_dir);

      // get downlaod http head
			// so we can get redirect lcoation from it
			// $http = new WireHttp();
			$httpHead = $http->head($downlaod_url);
			$download_location = $httpHead["location"];

      // download destination
			$dest = $this->temp_dir;
			// if doesent exists create it
			if(!is_dir($dest)) $this->files->mkdir($dest);
			// dest file name and path
			$tempFile = $dest . "updates-temp.zip";

      // download to temp folder
			$http->download($download_location, $tempFile);
      // unzip to temp folder
      $this->files->unzip($tempFile, $dest);
      // delete temp zip
      $this->files->unlink($tempFile);
      // copy from temp to updates folder
      $this->copyTempUpdates();
      // delete temp folder
      $this->files->rmdir($dest, true);

      $this->message("Updates has been downloaded!");

    } else {

      $this->error("Update failed");

    }

  }

  /**
   *  Copy temp updates
   *  to updates folder
   */
  public function copyTempUpdates() {
    $folders = scandir($this->temp_dir);
    $temp_updates_folder = $this->temp_dir . $folders[2] . "/";
    $this->files->copy($temp_updates_folder, $this->updates_dir, true);
  }

  //-------------------------------------------------------- 
  //  Admin UI
  //-------------------------------------------------------- 
  
  public function ___execute() {
    $this->headline('GitHub Updates');
    $this->breadcrumb('./', 'GitHub Updates');
    return [
      "this_module" => $this,
    ];
  }

}
