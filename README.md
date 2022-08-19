# GithubUpdates

**Processwire module** to download and install updates using github repo.   
You can use public or private repo for updates.    
If you use private repo, in module settings along side with github user name and repository name, you need to add a personal access token.    
It's up to you how you handle the updates, usualy it's just copy paste files.

* Create `vendor.json` to your processwire templates folder, and add the same file to updates repo root folder. This files will be compared when checking for updates.
* Create `updates.php` file in updates repo root. This file will be executed when triggering updates.


Repo structure example:
```
|- update.php
|- vendor.json
|- /inc/
|- /site/
  |- /classes/
  |- /modules/
  |- /templates/
```

**vendor.json**
```
{
  "project": "My Project Name",
  "version": "0.0.1",
  "website": "kreativan.dev"
}
```

**update.php**
```
# In this example we copy everything from repo /site/ folder to your processwire /site/ folder... 
# In /inc/ folder, you can create custom update scripts that will be executed automatically during the install.    

<?php
/**
 *  Copy files from updates folder to template folder
 *  @var string $this->updates_dir - get updates folder root
 */
$copyFrom = $this->updates_dir . "site/";
$copyTo = $this->config->paths->site;
$this->files->copy($copyFrom, $copyTo);
$this->message("Files copy finished.");

/**
 *  We are splitting all our updates as files (scripts) stored in /inc/ folder.
 *  Install files will be automatically included and executed.
 */
$inc_dir = $this->updates_dir . "inc/";
$inc = scandir($inc_dir);
foreach($inc as $php) {
  if($php != "" && $php != "." && $php != "..") {
    include($inc_dir.$php);
  }
}

// All update files executed
$this->message("All update files executed");

// Last update timestamp
$this->modules->saveModuleConfigData("GithubUpdates", [
  "last_update" => time()
]);

//--------------------------------------------------------
//  Finish - cleanup and redirect
//--------------------------------------------------------
$this->files->rmdir($this->updates_dir, true);
$this->message("Updates finished");
$this->session->redirect("./");
```
