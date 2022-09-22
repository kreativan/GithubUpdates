# GithubUpdates

**Processwire module** to download and install updates using github repo.   
You can use public or private repo for updates.    
If you use private repo, in module settings along side with github user name and repository name, you need to add a personal access token.    
It's up to you how you handle the updates, usualy it's just copy paste files.

* Create `vendor.json` to your processwire site folder, and add the same file to updates repo root or /site/ folder. This files will be compared when checking for updates.
* Optionaly, create `github-updates.php` file in updates repo root or site folder. This file will be executed when triggering updates.


Repo structure example:
```
|- /classes/
|- /modules/
|- /templates/
|- vendor.json
|- github-updates.php (optional)
```

**vendor.json**
```
{
  "vendor: "kreativan.dev",
  "project": "My Project Name",
  "version": "0.0.1",
  "website": "kreativan.dev"
}
```

### github-updates.php
This file is optional, it will be executed on updates install, if missing, module `$this->installUpdate()` method will be executed instead.    
`$this->installUpdate()` will simply copy base folders (modules, classes, templates) and files (vendor.json, ready.php, init.php and finished.php).     
So, if you want to add your own custom logic, use `github-updates.php` file...

